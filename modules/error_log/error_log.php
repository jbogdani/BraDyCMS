<?php

/**
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 * @since      Dec 11, 2012
 */

class error_log_ctrl
{
  private static $file = 'logs/error.log';

  public static function show()
  {
    if (file_exists(self::$file) and filesize(self::$file) > 0) {
      $handle = fopen(self::$file, 'r');
      $contents = fread($handle, filesize(self::$file));
      fclose($handle);
    }

    $html = '<h2>Error log</h2>' .
      '<div class="btn-group">' .
      '<a class="btn btn-default log-reload"><i class="fa fa-refresh"></i> Reload</a>' .
      ($contents ? '<a class="btn btn-default log-delete"><i class="fa fa-trash"></i> Delete</a>' : '') .
      '</div>' .
      '<hr />' .
      ($contents ? '<pre>' . htmlentities($contents) . '</pre>' : '<div class="text-success"><i class="fa fa-check"></i> Error log is empty</div>');

    echo $html;
?>
    <script>
      $('.log-reload').click(function() {
        admin.tabs.reloadThis(this);
      });
      $('.log-delete').click(function() {
        $.get('controller.php?obj=error_log_ctrl&method=delete', function(data) {
          admin.message(data.text, data.status);
          if (data.status === 'success') {
            admin.tabs.closeActive('error_log/show');
          }
        }, 'json');
      });
    </script>
<?php

  }


  public static function delete()
  {
    $handle = @fopen(self::$file, 'w+');
    if ($handle) {
      fclose($handle);
      $out['status'] = 'success';
      $out['text'] = 'Error-log deleted';
    } else {
      $out['status'] = 'error';
      $out['text'] = 'Error! Error-log not deleted';
    }

    echo json_encode($out);
  }
}
