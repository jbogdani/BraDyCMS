<?php
/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since      Dec 18, 2012
 */

class OutHtml extends Out
{
    /**
     * Returns a well-formatted html string to use for requiring and including an
     * asset, typically a javascript or css file, in a html document. The asset type
     * should be specified as the second argument ($type) as 'js' or 'css'.
     * If missing it will be guessed by the url extension (.js or .css). Some very common
     * presets (shortcuts) are available: query|bootstrap|fancybox|frontend.
     * If $version is present this version will be loaded
     * If the system is setted to use CDN, the the available CDN version will be loaded
     * instead of the local version.
     * @param string $asset   The asset url or one of the following presets: query|bootstrap|fancybox|frontend
     * @param string|false $type    The asset type: js|css. If missing the system will try to guess
     * by looking at the extension of the url; for presets the default js value will be used
     * @param string|false $version A specific version to load. By default the following
     * versions will be loaded for the presets: jquery: 1.11.3; boostrap: 3.3.5; fancybox: 2.1.5
     */
    public function asset($asset, $type = false, $version = false)
    {
        if (in_array($asset, Assets::getNames())) {
            $data = Assets::resolve($asset, $type, cfg::get('cdn') ? 'cdn':'local', $version);

            if (!$data) {
                return '<!-- Asset: ' . $asset . ' not found! -->';
            } else {
                $asset = $data['path'];
                $type = $data['type'];
            }
        }

        $l2h = $this->link2('home');

        if (strtolower($type) === 'js' || (!$type && strtolower(substr($asset, -2)) === 'js')) {
            if (file_exists('sites/default/js/' . $asset)) {
                return '<script src="' . $l2h . 'sites/default/js/' . $asset . '"></script>';
            } else {
                return '<script src="' . $asset . '"></script>';
            }
        } elseif (strtolower($type) === 'css' || (!$type && strtolower(substr($asset, -3)) === 'css')) {
            if (file_exists('sites/default/css/' . $asset)) {
                return '<link rel="stylesheet" href="' . $l2h . 'sites/default/css/' . $asset . '">';
            } else {
                return '<link rel="stylesheet" href="' . $asset . '">';
            }
        }
        return false;
    }

    /**
     * Returns html with general and Open graph metadata (if $no_og is false).
     * @param boolean $no_og
     * @return string
     */
    public function metadata($no_og = false)
    {
        $part = [];

        array_push($part, '<!-- HTML metadata -->');

        array_push($part, '<meta http-equiv="content-type" content="text/html; charset=utf-8" />');
        array_push($part, '<meta name="robots" content="' . $this->getPageData('robots') . '" />');
        // Since v 3.7.10 custom_title is used for HTML metadata
        array_push($part, '<title>' .
      ($this->getPageData('custom_title') ? $this->getPageData('custom_title', true) : $this->getPageData('title', true)) .
      '</title>');
        array_push($part, '<meta name="description" content="' . $this->getPageData('description', true) . '" />');
        array_push($part, '<meta name="keywords" content="' . $this->getPageData('keywords', true) . '" />');
        array_push($part, '<meta lang="' . $this->getPageData('lang', true) . '" />');
        array_push($part, '<meta name="viewport" content="width=device-width, initial-scale=1.0,shrink-to-fit=no">');
        array_push($part, '<meta name="generator" content="BraDyCMS ' . $this->getVersion() . '">');

        // http://stackoverflow.com/a/23717829/586449
        $url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

        // Current page in current language
        array_push($part, '<link rel="alternate" href="' . $url . '" hreflang="' . $this->getLang('current', true) . '" />');

        // code, string, is_current, href
        $lang_arr = $this->getLanguages();

        foreach ($lang_arr as $l) {
            if ($l['is_current']) {
                continue;
            }

            array_push($part, '<link rel="alternate" href="' .
        str_replace('//', '/', str_replace($_SERVER['REQUEST_URI'], '/' . $l['href'], $url)) .
        '" hreflang="' . $l['code'] . '" />');
        }

        if (!$no_og) {
            array_push($part, '');
            array_push($part, '<!-- Open Graph metadata -->');
            // Since v 3.7.10 cutstom_title is used for OpenGraph metadata
            array_push($part, '<meta property="og:title" content="'
        . ($this->getPageData('custom_title') ? $this->getPageData('custom_title', true) : $this->getPageData('title', true))
        . '" />');
            array_push($part, '<meta property="og:description" content="' . html_entity_decode($this->getPageData('description', true)) . '" />');
            array_push($part, '<meta property="og:url" content="' . $this->getPageData('url', true) . '" />');
            if ($this->getPageData('image')) {
                $rel_path = str_replace(utils::getBaseUrl(), null, $this->getPageData('image', true));
                $image_size = getimagesize($rel_path);
                array_push($part, '<meta property="og:image" content="' . $this->getPageData('image', true) . '" />');

                array_push($part, '<meta property="og:image:width" content="' . $image_size[0] . '" />');
                array_push($part, '<meta property="og:image:height" content="' . $image_size[1] . '" />');
            }
        }

        array_push($part, '');
        array_push($part, '<!-- Feed links -->');
        array_push($part, '<link rel="alternate" type="application/rss+xml" title="RSS" href="/feed/rss" />');
        array_push($part, '<link rel="alternate" type="application/atom" title="RSS" href="/feed/atom" />');



        return implode("\n  ", $part);
    }

    /**
     * Returns html with Dublin Core metadata
     *    DC.Type:            "Text"
     *    DC.Format:          "text/html"
     *    DC.Identifier:      article id
     *    Identifier.URI      Page (article) URL
     *    DC.Identifier.DOI:  Page (article) DOI
     *    DC.Title:           Page (article) title
     *    DC.Description:     Article summary or Page description (can be: seo.description, article.customdescription, article.summary, cfg.description)
     *    DC.Language:        Article language
     *    DC.Creator:         Page (article) author
     *    DC.Creator.PersonalName: Same as DC.Creator
     *    DC.Publisher:       MD Publisher
     *    DC.subject:         Page (article) keywords
     *    DC.Date:            Page (article) publish date
     *    DC.Date.created:    Same as DC.Date
     *    DC.Date.issued:     Same as DC.Date
     *    DC.Date.modified:   Article updated
     *    DC.Source:          MD repository name
     *    DC.Source.ISSN:     MD issn
     *    DC.Source.URI       Page (article) URI
     * @return string
     */
    public function metadata_dc()
    {
        /**
         * <meta name="DC.Contributor.Sponsor" xml:lang="en" content=""/>
         * <meta name="DC.Contributor.Sponsor" xml:lang="it" content=""/>
         * <meta name="DC.Date.dateSubmitted" scheme="ISO8601" content="2012-12-20"/>
         * <meta name="DC.Identifier.pageNumber" content="1-46"/>
         * <meta name="DC.Rights" content="I copyright degli articoli pubblicati su questa rivista appartengono agli autori, e i diritti di prima pubblicazione sono concessi alla rivista. Nel momento in cui presentano il proprio lavoro, gli autori accettano che possa essere copiato da chiunque per fini non commerciali, ma solamente nel caso in cui venga appropriatamente citato. In virtù della loro apparizione su questa rivista gratuita, gli articoli sono dichiarati usufruibili gratuitamente, con una corretta attribuzione, in contesti non commerciali. Ogni autore è responsabile per il contenuto del proprio lavoro, incluse citazioni, attribuzione e permessi d&#039;uso."/>
         * <meta name="DC.Title.Alternative" xml:lang="en" content="The Bronze Age settlement of S. Giovanni in Triario (Bologna)"/>
         * <meta name="DC.Type" content="Text.Serial.Journal"/>
         */

        $art = $this->getArticle();

        if (!$art || !is_array($art) || empty($art)) {
            return;
        }

        $part = [];

        // Starting comment
        array_push($part, '<!-- Dublin Core metadata tags -->');
        // Type
        array_push($part, '<meta name="DC.Type" content="Text" />');
        // Format
        array_push($part, '<meta name="DC.Format" content="text/html" />');
        // Identifier
        array_push($part, '<meta name="DC.Identifier" content="' . $art['id'] . '"/>');
        // Identifier.URI
        array_push($part, '<meta name="DC.Identifier.URI" scheme="URI" content="' . $this->getPageData('url', true) . '" />');
        // Identifier.DOI
        if ($art[$this->getMD()->getTable('id')]) {
            array_push($part, '<meta name="DC.Identifier.DOI" ' .
        'content="' . ($this->getMD()->getDoiPrefix() ? $this->getMD()->getDoiPrefix() : '') .
        $art[$this->getMD()->getTable('id')] . '" />');
        }
        // Titile
        array_push($part, '<meta name="DC.Title" content="' . $this->getPageData('title', true) . '" />');
        // Description
        array_push($part, '<meta name="DC.Description" ' .
      'content="' . ($art['summary'] ? trim(strip_tags($art['summary'])) : $this->getPageData('description', true)) . '" />');
        // Language
        array_push($part, '<meta name="DC.Language" scheme="ISO639-1" content="' . $this->getPageData('lang', true) . '" />');
        // Creator && Creator.PersonalName
        if ($this->getPageData('author')) {
            array_push($part, '<meta name="DC.Creator" content="' .  $this->getPageData('author', true) . '" />');
            array_push($part, '<meta name="DC.Creator.PersonalName" content="' .  $this->getPageData('author', true) . '" />');
        }
        // Publisher
        if ($this->getMD()->getPublisher()) {
            array_push($part, '<meta name="DC.Publisher" content="' . $this->getMD()->getPublisher() . '" />');
        }
        // Subject
        if ($this->getPageData('keywords', true)) {
            array_push($part, '<meta name="DC.subject" scheme="RFC3066" content="' . $this->getPageData('keywords', true) . '" />');
        }
        // Date & Date.created & Date.issued
        if ($this->getPageData('date')) {
            array_push($part, '<meta name="DC.Date" scheme="W3CDTF" content="' . $this->getPageData('date') . '" />');
            array_push($part, '<meta name="DC.Date.created" scheme="ISO8601" content="' . $this->getPageData('date') . '" />');
            array_push($part, '<meta name="DC.Date.issued" scheme="ISO8601" content="' . $this->getPageData('date') . '" />');
        }
        // Date.modified
        if ($art['updated']) {
            array_push($part, '<meta name="DC.Date.modified" scheme="ISO8601" content="' . $art['updated'] . '" />');
        }
        // Source
        if ($this->getMD()->getRepositoryName()) {
            array_push($part, '<meta name="DC.Source" content="' . $this->getMD()->getRepositoryName() . '"/>');
        }
        // Source.ISSN
        if ($this->getMD()->getISSN()) {
            array_push($part, '<meta name="DC.Source.ISSN" content="' . $this->getMD()->getISSN() . '"/>');
        }
        // Source.URI
        if ($this->getMD()->getURL()) {
            array_push($part, '<meta name="DC.Source.URI" content="' . $this->getMD()->getURL() . '"/>');
        }
        // Source.Volume
        if ($this->getMD()->getTable()['volume'] && $art[$this->getMD()->getTable()['volume']]) {
            array_push($part, '<meta name="DC.Source.Volume" content="' . $art[$this->getMD()->getTable()['volume']] . '" />');
        }
        // Source.Issue
        if ($this->getMD()->getTable()['issue'] && $art[$this->getMD()->getTable()['issue']]) {
            array_push($part, '<meta name="DC.Source.Issue" content="' . $art[$this->getMD()->getTable()['issue']] . '" />');
        }
        // DC.Rights, copyright holder and year & license
        if ($this->getMD()->getCopyright()) {
            if ($art[$this->getMD()->getCopyright('year')] && preg_match("/[0-9]{4}/", $art[$this->getMD()->getCopyright('year')])) {
                $year = $art[$this->getMD()->getCopyright('year')];
            } elseif (preg_match("/[0-9]{4}/", $this->getMD()->getCopyright('year'))) {
                $year = $this->getMD()->getCopyright('year');
            } else {
                $year = date("Y");
            }

            $copyright_holder = ($this->getMD()->getCopyright('holder') === 'author' ? $art['author'] : $this->getMD()->getCopyright('holder'));

            array_push($part, '<meta name="DC.Rights" content="Copyright (c) ' . $year . ' ' . $copyright_holder . '" />');

            if ($this->getMD()->getCopyright('license')) {
                if ($art[$this->getMD()->getCopyright('license')]) {
                    $license = $art[$this->getMD()->getCopyright('license')];
                } else {
                    $license = $this->getMD()->getCopyright('license');
                }
                array_push($part, '<meta name="DC.Rights" content="' . $license . '" />');
            }
        }
        return implode("\n  ", $part);
    }


    /**
     * Returns html with Highwire Press metadata, for Google Scholar usage
     * http://www.google.com/intl/en/scholar/inclusion.html#indexing
     *    citation_journal_title: MD repository name
     *    citation_issn:          MD issn
     *    citation_author:        Page author (article author)
     *    citation_title:         Page title (artile title)
     *    citation_date:          Page date (article publish)
     *    citation_doi:           Page DOI (article doi)
     *    citation_abstract_html_url:Page url (article url)
     *    citation_language:      Page language code (article language)
     *    citation_pdf_url:       Pdf full url (both patterns are supported: images/pdf/{textid}.pdf and images/articles/media/{id}/{textid}.pdf)
     * @return string
     */
    public function metadata_hp()
    {
        /**
         * <meta name="citation_firstpage" content="1-46"/>
         */
        $art = $this->getArticle();

        $html =  "\n  <!-- Highwire Press metadata tags -->"

        // citation_journal_title
        . ($this->getMD()->getRepositoryName() ?
          "\n  " . '<meta name="citation_journal_title" ' .
            'content="' . $this->getMD()->getRepositoryName() . '"/>' : '')

        // citation_issn
        . ($this->getMD()->getISSN() ?
          "\n  " . '<meta name="citation_issn" ' .
            'content="' . $this->getMD()->getISSN() . '"/>' : '')

        // citation_volume
        . ($this->getMD()->getTable('volume') && $art[$this->getMD()->getTable('volume')] ?
            "\n  " . '<meta name="citation_volume" ' .
            'content="' . $art[$this->getMD()->getTable('volume')] . '" />' : '')

        // citation_issue
        . ($this->getMD()->getTable('issue') && $art[$this->getMD()->getTable('issue')] ?
            "\n  " . '<meta name="citation_issue" ' .
            'content="' . $art[$this->getMD()->getTable('issue')] . '" />' : '')

        // citation_author
        . ($this->getPageData('author') ?
          "\n  " . '<meta name="citation_author" ' .
            'content="' . $this->getPageData('author', true) . '" />' : '')

        // citation_title
        . "\n  " . '<meta name="citation_title" ' .
          'content="' . $this->getPageData('title', true) . '" />'

        // citation_date
        . ($this->getPageData('date') ?
            "\n  " . '<meta name="citation_date" ' .
              'content="' . $this->getPageData('date', true) . '" />' : '')

        // citation_doi
        . "\n  " . '<meta name="citation_doi" ' .
          'content="' . ($this->getMD()->getDoiPrefix() ? $this->getMD()->getDoiPrefix() : '') .
            ($art[$this->getMD()->getTable('id')] ?
              $art[$this->getMD()->getTable('id')] : ''). '"/>'

        // citation_abstract_html_url
        . "\n  " . '<meta name="citation_abstract_html_url" ' .
          'content="' . $this->getPageData('url', true) . '"/>'

        // citation_language
        . "\n  " . '<meta name="citation_language" ' .
          'content="' . $this->getPageData('lang', true) . '" />';

        // citation_pdf_url
        $pdf_url = [
      'sites/default/images/pdf/' . $art['textid'] . '.pdf',
      'sites/default/images/articles/media/' . $art['id'] . '/'. $art['textid'] . '.pdf'
    ];
        foreach ($pdf_url as $p) {
            if (file_exists($p)) {
                $html .= "\n  " . '<meta name="citation_pdf_url" content="' . $this->getMD()->getURL() . '/' . $p . '"/>';
            }
        }
        return $html;
    }

    /**
     * Returns html with menu data
     * @param string $menu menu name to display
     * @param string $class CSS class or space-separated classes to attach to main UL
     * @param boolean $strip if true no styles or other (bootstrap style) markup will be printed
     * @param array $data if array if data is provided, these data will be shown
     * @return boolean|string
     */
    public function menu($menu = false, $class = false, $strip = false, $data = false)
    {
        if ($menu) {
            $menu_arr = $this->getMenu($menu);
        } elseif ($data) {
            $menu_arr = $data;
        } else {
            error_log('No menu or data to display');
            return false;
        }

        if (!is_array($menu_arr) || empty($menu_arr)) {
            error_log('Menu <' . $menu . '> not found');
            return false;
        }


        $html = '<ul class="' . ($menu ? 'menu ' . $class . ' ' . $menu : 'submenu' . ($strip ? '' : ' dropdown-menu')) . '">';

        foreach ($menu_arr as $menu) {
            if ($menu['item'] == '.' && $menu['href'] == link::format('.')) {
                $html .= '<li class="divider"></li>';
            } else {
                $html .= '<li class="menu-item ' . ($menu['sub'] ? ' dropdown-submenu ' : '') . ($menu['current'] ? 'active' : '') . '">'
            . '<a href="' . $menu['href'] . '" '
                . ($menu['title'] ? ' title="' . $menu['title'] . '" ' : '')
                . ($menu['target'] ? ' target="' . $menu['target'] . '" ' : '')
                . ($menu['sub'] && !$strip ? ' class="dropdown-toggle" data-toggle="dropdown"' : '')
                . '>'
                . $menu['item']
                . ($menu['sub'] && !$strip ? ' <b class="caret"></b>' : '')
            . '</a>';
            }

            if ($menu['sub']) {
                $html .= $this->menu(false, false, $strip, $menu['sub']);
            }

            $html .= '</li>';
        }

        $html .= '</ul>';

        return $html;
    }


    /**
     * Returns html for language selection
     * @param boolean $flags if true flags images will be shown
     * @param boolean $no_text if true no test will be shown
     * @return string
     */
    public function langMenu($flags = false, $no_text = false)
    {
        $lang_arr = $this->getLanguages();

        $html = '<ul class="menu lang">';

        foreach ($lang_arr as $lang) {
            $flag_html = '<img src="./img/flags/' . $lang['code'] . '.png"  alt="' . $lang['string'] . '"  /> ';

            $html .= '<li ' . ($lang['is_current'] ? 'class="current" ' : '') . '>'
        . '<a '
          . ' data-ajax="false" '
          . ' href="' . $lang['href'] . '" '
          . ' title="' . $lang['string'] . '">'
          . ($flags ? $flag_html : '')
          . ($no_text ? '' : $lang['string'])
          . '</a></li>';
        }

        $html .= '</ul>';

        return $html;
    }


    /**
     * Returns html with search form
     * @return string
     */
    public function searchForm()
    {
        $html = '<form action="javascript:void(0);" '
          . 'id="searchForm" '
          . 'data-path="' . $this->link2('home') . '">'
      . '<input class="search-query" '
          . 'type="search" '
          . 'placeholder="' . tr::get('search_site') . '" '
          . 'name="search" '
          . 'id="search"'
          . ($this->getSearchString() ? ' value="' . $this->getSearchString(true) . '"' : '') . ' />'
      . '</form>';
        $js = <<<EOD
<script>
$('#searchForm').submit(function(){
  if($('#search').val() !== '' ){
    window.location = $(this).data('path') + '/search:' + encodeURIComponent($('#search').val());
  }
});
</script>
EOD;
        $this->setQueue('modules', $js, true);
        return $html;
    }


    /**
     * Returns html with formatted search result
     * @return string
     */
    public function searchResults()
    {
        $art_list = $this->getSearchResults();

        $tot_found = $art_list ? count($art_list) : 0;

        if ($tot_found === 0) {
            if ($this->getSearchString()) {
                $html = '<h4>' . sprintf(tr::get('no_result_for_query'), $this->getSearchString()). '</h4>';
            } else {
                $html = '<h4>' . sprintf(tr::get('no_result_for_tags'), implode(', ', $this->getFilterTags())). '</h4>';
            }
        } else {
            if ($this->getSearchString()) {
                $html = '<h4>' . sprintf(tr::get('x_results_for_query'), $this->getSearchString(), $tot_found) . '</h4>';
            } else {
                $html = '<h4>' . sprintf(tr::get('x_results_for_tags'), implode(', ', $this->getFilterTags()), $tot_found) . '</h4>';
            }

            $html .= $this->array2html($art_list, 'search');
        }
        return $html;
    }


    /**
     * Returns html with article's body
     * @param string $article article to show, if false current article will be shown
     * @return string
     */
    public function articleBody($article = false)
    {
        $art = $this->getArticle($article);

        if ($art) {
            $html = '<div class="section">' .
          '<div class="article">' .
            '<h1>' . $art['title'] . '</h1>' .
            '<div class="content">' . $art['text'] . '</div>' .
          '</div>' .
        '</div>';
        } else {
            $html = '<div class="section error">' . tr::get('article_does_not_exist') . '</div>';
        }

        return $html;
    }


    /**
     * Returns html with list of articles matching tags
     * @return string
     */
    public function tagBlog()
    {
        if (func_get_args()) {
            $art_list = $this->getArticlesByTagArray(func_get_args());
            $tags = implode(' ', func_get_args());
        } else {
            $art_list = $this->getArticlesByTag();
            $tags = implode(' ', $this->getFilterTags());
        }

        return $this->array2html($art_list, 'tags ' . $tags);
    }


    /**
     * Returns html data from array of articles
     * @param array $art_list  array of articles (arrays)
     * @param string $class  CSS class
     * @return boolean|string
     */
    private function array2html($art_list, $class = false)
    {
        if (!is_array($art_list)) {
            return false;
        }

        $html = '<div class="blog ' . ($class ? $class : '') . '">';

        foreach ($art_list as $art) {
            $html .= '<div class="article">' .
            ($art['title'] ? ' <h3><a href="' . $art['url']  . '">' . $art['title'] . '</a></h3>' : '') .
            '<div class="content">' . $art['summary'] . '</div>' .
              '<div class="read_more">' .
                '<a href="' . $art['url']  . '">' . tr::get('read_more') . '</a>' .
              '</div>' .
            '</div>';
        }
        $html .= '</div>';

        return $html;
    }


    /**
     * Returns html with list of articles similar to present article
     * @param int $max maximum number of array elements to return
     * @return boolean|string
     */
    public function similarBlog($max = false)
    {
        $art_list = $this->getSimilar(false, $max);

        if (!is_array($art_list)) {
            return false;
        }

        $html = '<h3>' . tr::get('from_same_section') . '</h3>';

        $html = $this->array2html($art_list, 'similar');

        return $html;
    }


    /**
     * Returns html with Google Analytics javascript code
     * @return string
     */
    public function GA()
    {
        $id = cfg::get('ga_id');

        $restrict_domain = cfg::get('ga_domain');


        if ($id && (!$restrict_domain || (preg_match('/' . $restrict_domain . '/', $_SERVER['HTTP_HOST']))) && !$this->cfg['isDraft']) {
            $html = '<script type="text/javascript">'
          . 'var _gaq = _gaq || [];'
          . "_gaq.push(['_setAccount', '" . $id . "']);"
          . " _gaq.push(['_trackPageview']);"
          . "(function() {"
          . "var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;"
          . "ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';"
          . "var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);"
          . '})();'
        . '</script>';
        }

        return $html;
    }

    /**
     * Returns html with Google Universal Analytics javascript code
     * @return string
     */
    public function GUA()
    {
        $id = cfg::get('ga_id');

        $restrict_domain = cfg::get('ga_domain');


        if ($id && (!$restrict_domain || (preg_match('/' . $restrict_domain . '/', $_SERVER['HTTP_HOST']))) && !$this->cfg['isDraft']) {
            $html = '<script>'
        . "(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){"
        . "(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),"
        . "m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)"
        . "})(window,document,'script','//www.google-analytics.com/analytics.js','ga');"
        . "ga('create', '" . $id . "', 'auto');"
        . "ga('send', 'pageview');"
        . '</script>';
        }

        return $html;
    }


    /**
     * Returns html with image gallery. Alias for customTags:gallery()
     * @param string $gal  Gallery name
     * @param string $class CSS class or space-separated CSS classes to apply to main UL
     * @return type
     */
    public function gallery($gal, $class = false)
    {
        return customTags::gallery(array('content' => $gal, 'class' => $class));
    }


    /**
     * Runs method of customTags object and returns html
     * @param string $method method of customTags object to call
     * @param string|array $param array or json encoded array with method parameters
     */
    public function ct($method, $param = array())
    {
        if ($param && is_string($param)) {
            $param = json_decode($param, true);
        }

        if (method_exists('customTags', $method)) {
            $html = call_user_func(array('customTags', $method), $param, $this);
        } elseif (file_exists('sites/default/modules/' . $method . '/' . $method. '.inc')) {
            require_once 'sites/default/modules/' . $method . '/' . $method . '.inc';
            $html = call_user_func_array([new $method, 'init'], [$param, $this]);
        } elseif (file_exists('sites/default/modules/' . $method . '/' . $method. '.php')) {
            require_once 'sites/default/modules/' . $method . '/' . $method . '.php';
            $html = call_user_func_array([new $method, 'init'], [$param, $this]);
        }

        return $html;
    }


    /**
     * Returns well formatted html containing table
     * @param string $node Download node name
     * @param string $class CSS class or space-separated classes to attche tomain table element
     * @return string|boolean well formatted html
     */
    public function downloadNode($node, $class = false)
    {
        return customTags::download(
      array(
        'content' => $node,
        'class' => $class
        )
      );
    }

    /**
     *
     * @param string $className Css class name for main ul
     * @return boolean|string Well formatted ul with pagination links
     */
    public function pagination($className = false)
    {
        $pagination = $this->getPagination();

        if (!$pagination['start'] && !$pagination['end']) {
            return false;
        }

        $href = false;

        $html = '<ul class="pagination' . ($className ? ' ' . $className : '') . '">';

        $showed_end = $showed_start = false;

        for ($x = $pagination['start']; $x<=$pagination['end']; $x++) {
            switch ($this->getContext()) {
              case 'tags':
                $href = link::to_tags(
                  $this->getFilterTags(),
                  $this->getLang('input'),
                  $x
                  );
                break;

              case 'search':
                $href = link::to_search($this->getSearchString(), $this->getLang('input'), $x);
                break;

              default:

                return false;
                break;
            }



            if (
        $x != $pagination['start'] && $x != $pagination['end']
        &&
        ($x < $pagination['current'] - 2 || $x > $pagination['current'] + 2)
        ) {
                if ($x < $pagination['current'] && !$showed_start) {
                    $html .= '<li class="page-item disabled"><a class="page-link disabled" href="#">...</a>';
                    $showed_start = true;
                }

                if ($x > $pagination['current'] && !$showed_end) {
                    $html .= '<li class="page-item disabled"><a class="page-link disabled" href="#">...</a>';
                    $showed_end = true;
                }
            } else {
                $html .= '<li class="page-item' . ($x == $pagination['current'] ? ' active' : '') . '">'
            . '<a href="' . $href . '" class="page-link">' . $x . '</a>'
          . "</li>";
            }
        }

        $html .= '</ul>';

        return $html;
    }

    /**
     * Checks if current user can view or not the current page
     * @return boolean
     */
    public function canView()
    {
        switch ($this->getContext()) {

      case 'tags':
        $tags = $this->getFilterTags();
        break;
      case 'article':
        $art = $this->getArticle();
        $tags = $art['tags'];
        break;
      case 'home':
      case 'search':
      default:
        return true;
        break;
    }


        $protectedtags = new protectedtags_ctrl();
        return $protectedtags->canUserRead($tags);
    }

    /**
     * Shows login form for protected tags
     * @param  array|false $css Optional array with css classes
     * @return string      Well-formatted HTML and javascript
     */
    public function loginForm($css = false)
    {
        $protectedtags = new protectedtags_ctrl();
        return $protectedtags->loginForm($css);
    }

    /**
     * Shows register form for a protected tag
     * @param  string $tag tag id
     * @param  array|false $css Optional array with css classes
     * @return string      Well-formatted HTML and javascript
     */
    public function registerForm($tag, $css = false)
    {
        $protectedtags = new protectedtags_ctrl();
        return $protectedtags->registerForm($tag, $css);
    }

    /**
     * Prints a logout button, html & javascript (jQuery requred)
     * @param  array|false $css Optional array with css classes
     * @return string      Well-formatted HTML and javascript
     */
    public function logoutButton($css = false)
    {
        $protectedtags = new protectedtags_ctrl();
        return $protectedtags->logoutButton($css);
    }
}
