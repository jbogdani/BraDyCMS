<?php

/**
 * @author      Julian Bogdani <jbogdani@gmail.com>
 * @copyright    BraDyUS. Communicating Cultural Heritage, http://bradypus.net 2007-2013
 * @license      MIT, See LICENSE file
 * @since        Nov 4, 2013
 * @example
 * try
 * {
 *   $citation = new Citation();
 *
 *   $citation->type = 'ejournal';
 *   $citation->author = 'Julian Bogdani';
 *   $citation->author_address = 'Via Antonio Zannoni, 41. Bologna. Italy';
 *   $citation->date = '2013-10-10';
 *   $citation->title = 'This is tha article\'s title';
 *   $citation->journal = 'Storicamente';
 *   $citation->volume = '10';
 *   $citation->art_no = '7';
 *   $citation->city = 'Bologna';
 *   $citation->doi = '1234567890';
 *   $citation->keywords = 'uno';
 *   $citation->keywords = 'due';
 *   $citation->keywords = 'tre';
 *   $citation->language = 'EN';
 *   $citation->publisher = 'BraDypUS';
 *   $citation->issn = '1234-5678';
 *   $citation->url = 'http://bradypus.net';
 *
 *   echo '<pre>' . $citation->export('plaintext') . '</pre>';
 *   echo '<pre>' . $citation->export('endnote') . '</pre>';
 *   echo '<pre>' . $citation->export('bibtext') . '</pre>';
 *   echo '<pre>' . $citation->export('html') . '</pre>';
 * }
 * catch (Exception $e)
 * {
 *   var_dump($e->getMessage());
 * }
*/
class Citation
{
    /**
     *
     * @var array data container
     */
    private $data = array();
    /**
     *
     * @var array required keys
     */
    private $required_keys = array(
    'type',
    'author',
    'author_address',
    'date',
    'title',
    'journal',
    'volume',
    'art_no',
    'city',
    'doi',
    'keywords',
    'language',
    'publisher',
    'issn',
    'url'
    );


    /**
     * Sets all data and throws Exception in case on invalid keys
     * @param string $key Key to set
     * @param string $value Value to set
     * @throws Exception
     */
    public function __set($key, $value)
    {
        $all_keys = $this->required_keys;
        $all_keys[] = 'abstract';

        if (!in_array($key, $all_keys)) {
            throw new Exception($key . ' is not a valid key.');
        }

        if ($key === 'keywords' && is_string($value)) {
            $this->data[$key][] = $value;
        } else {
            $this->data[$key] = $value;
        }
    }

    /**
     * Main getter
     * @param type $key
     * @return boolean
     */
    public function __get($key)
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return false;
    }

    /**
     * Checks data structure and throws Exception on erros
     * @return boolean
     * @throws Exception
     */
    private function checkStructure()
    {
        foreach ($this->required_keys as $key) {
            if (!array_key_exists($key, $this->data)) {
                throw new Exception('Error! Required value ' . $key . ' is not set!');
            }
        }

        $types = array('ejournal');

        if (!in_array($this->data['type'], $types)) {
            throw new Exception('Type is not valid. One of the following mus be used: ' .
        implode(', ', $types));
        }
        return true;
    }

    /**
     * Exports citation dat in specified format
     * @param string $format one of plaintext|endnote|bibtext|refman|html
     * @param boolean $echo if true the citation will be retuned otherwize it will be returned
     * @return string Well formatted citetion
     * @throws Exception
     */
    public function export($format, $echo = false)
    {
        $this->checkStructure();

        switch ($format) {
      case 'plaintext':
      case 'endnote':
      case 'bibtext':
      case 'refman':
      case 'html':
        if (method_exists($this, $format)) {
            $text = call_user_func(array($this, $format));

            if ($echo) {
                echo $text;
            } else {
                return $text;
            }
        } else {
            throw new Exception($format . ' export has not been implemented yet!');
        }
        break;

      default:
        throw new Exception($format . ' is not a valid export format!');
        break;
    }
    }

    /**
     * Returns citation in RefMan format
     * @return string
     */
    private function refman()
    {
        $text = '';

        switch ($this->data['type']) {
      case 'ejournal':
        $text .= "TY  -  EJOUR\n";
        break;
    }

        $text .= 'AU  -  ' . $this->author . "\n" .
      'T1  -  ' . $this->title . "\n" .
      'AD  -  ' . $this->author_address . "\n" .
      'DO  -  ' . $this->doi . "\n" .
      'CY  -  ' . $this->city . "\n";

        foreach ($this->data['keywords'] as $kw) {
            $text .= 'KW  -  ' . $kw . "\n";
        }

        $text .= 'LA  -  ' . $this->data['language'] . "\n" .
      'M3  -  Editorial Material' . "\n" .
      'VL  -  ' . $this->data['volume'] . "\n" .
      'NV  -  ' . $this->data['art_no'] . "\n" .
      'C7  -  ' . $this->data['art_no'] . "\n" .
      'PB  -  ' . $this->data['publisher'] . "\n" .
      'PY  -  ' . date('Y', strtotime($this->data['date'])) . "\n" .
      'SE  -  ' . $this->data['date'] . "\n" .
      'SN  -  ' . $this->data['issn'] . "\n" .
      'T2  -  ' . $this->data['journal'] . "\n" .
      'UR  -  ' . $this->data['url'] . "\n"
      ;

        return $text;
    }

    /**
     * Alias for refman
     * @return string
     */
    private function plaintext()
    {
        return $this->refman();
    }

    /**
     * Returns citation in EndNote format
     * @return string
     */
    private function endnote()
    {
        switch ($this->data['type']) {
      case 'ejournal':
        $text = "%0\tElectronic Article\n";
        break;
    }

        $text .= "%A\t" . $this->author . "\n" .
      "%T\t" . $this->title . "\n" .
      "%+\t" . $this->author_address . "\n" .
      "%C\t" . $this->city . "\n" .
      "%R\t" . $this->doi . "\n" .
      "%K\t" . implode("\n", $this->data['keywords']) . "\n";

        foreach ($this->data['keywords'] as $kw) {
            $text .= 'KW  -  ' . $kw . "\n";
        }

        $text .= 'LA  -  ' . $this->data['language'] . "\n" .
      "%9\tEditorial Material" . "\n" .
      "%V\t" . $this->data['volume'] . "\n" .
      "%6\t" . $this->data['art_no'] . "\n" .
      "%]\t" . $this->data['art_no'] . "\n" .
      "%I\t" . $this->data['publisher'] . "\n" .
      "%D\t" . date('Y', strtotime($this->data['date'])) . "\n" .
      "%&\t" . $this->data['date'] . "\n" .
      "%@\t" . $this->data['issn'] . "\n" .
      "%B\t" . $this->data['journal'] . "\n" .
      "%U\t" . $this->data['url'] . "\n"
      ;

        return $text;
    }

    /**
     * Returns citation in BiBTeX format
     * @return string
     */
    private function bibtext()
    {
        $text = "@misc{\n" .
      "\tauthor = {{$this->author}},\n" .
      "\ttitle = {{$this->title}},\n" .
      "\tpublisher = {{$this->data['publisher']}},\n" .
      "\tvolume = {{$this->data['volume']}},\n" .
      ($this->data['abstract'] ? "\tabstract = {{$this->data['abstract']}},\n" : '') .
      "\tkeywords = {" . implode("\n", $this->data['keywords']) . "},\n" .
      "\tISBN = {{$this->data['issn']}},\n" .
      "\tyear = {" . date('Y', strtotime($this->data['date'])) . "}\n" .
      "}";

        return $text;
    }

    /**
     * Returns citation in HTML format
     * @return string
     */
    private function html()
    {
        $text = $this->author . ', ' .
      '<em>' . $this->title . '</em>,' .
      '"' . $this->journal . '", ' .
      $this->data['volume'] . '(' . date('Y', strtotime($this->data['date'])) . ')' .
      '# ' . $this->data['art_no'] .
      'DOI: <a href="http://dx.doi.org/' . $this->doi . '">' . $this->doi . '</a>';
    }
}
