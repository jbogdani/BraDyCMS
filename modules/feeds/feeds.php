<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Mar 18, 2013
 */

class feeds_ctrl
{
  public static function rss2($lang = false)
  {
    self::feed();
  }

  public static function atom($lang = false)
  {
    self::feed('atom');
  }

  private static function feed($type = false, $lang = false)
  {
    set_time_limit(0);

    ini_set('memory_limit', '512M');

    $feedWriter = ($type == 'atom' ? new \FeedWriter\Atom() : new \FeedWriter\RSS2);

    $feedWriter->setTitle(cfg::get('title'));

    $feedWriter->setLink(utils::getBaseUrl());

    $feedWriter->setChannelElement('description', cfg::get('description'));

    $feedWriter->setChannelElement('language', $lang ? $lang : cfg::get('sys_lang'));

    $feedWriter->setChannelElement('pubDate', date(DATE_RSS, time()));

    $total_arts = Article::countAllValid();

    $page_step = 100;

    if ($total_arts <= $page_step) {
      $art_array = Article::getAllValid($lang);
    } else {
      $maxpages = floor($total_arts / $page_step);

      $baseURL = utils::getBaseUrl() .  'feed/' . ($type === 'atom' ? 'atome' : 'rss');

      $self = $baseURL . ($_GET['page'] && $_GET['page'] > 1 ? '?page=' . $_GET['page'] : '');

      $next = ($_GET['page'] < $maxpages) ?
        $baseURL . '?page=' . ($_GET['page'] ? ($_GET['page'] + 1) : 1)
        : false;

      $previous = ($_GET['page'] && $_GET['page'] > 1) ?
        $baseURL . '?page=' . ($_GET['page'] - 1)
        : false;

      $first = $baseURL;

      $last = $baseURL . '?page=' . $maxpages;

      $feedWriter->setPagination($self, $next, $previous, $first, $last);

      $start = $_GET['page'] && $_GET['page'] > 1 ? ($_GET['page'] * $page_step) : 0;

      $art_array = Article::getAllValid($lang, false, $start . ', ' . $page_step);
    }

    if (is_array($art_array)) {
      foreach ($art_array as $art) {
        $newItem = $feedWriter->createNewItem();

        $newItem->setTitle($art['title'] ? self::cleanStr($art['title']) : '');

        $newItem->setLink(self::cleanStr($art['full_url']));

        $date = $art['publish'] ? $art['publish'] : ($art['created'] ? $art['created'] : false);

        $date ? $newItem->setDate(self::cleanStr($date)) : '';

        $description = $art['summary'] ? $art['summary'] : substr($art['text'], 0, 500) . '...';
        $newItem->setDescription(self::cleanStr($description));

        $feedWriter->addItem($newItem);
      }
    }
    $feedWriter->printFeed('application/rss+xml; charset=utf-8');
  }

  private static function cleanStr($str)
  {
    return trim(htmlentities(strip_tags($str)));
  }
}
