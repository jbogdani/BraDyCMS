<?php
/**
 * 
 * @author     Julian Bogdani <jbogdani@gmail.com>
 * @copyright  2007-2021 Julian Bogdani
 * @license    AGPL-3.0; see LICENSE file
 */
// TODO:
//  1. admin ui: button to set to: int | 0

class download_ctrl extends Controller
{

  /**
   * Outputs main admin UI
   * @return string Valid html of download count list
   */
  public function all()
  {
    $this->render('download', 'all');
  }

  /**
   * Returns valis json of available download counts
   * @return string valid JSON woth pagination and query information, fit to use in datatables
   */
  public function sql2json()
  {
    $aColumns = [ 'id', 'file', 'tot' ];

    // Paging
    $sLimit = '';
    if ( isset( $this->request['iDisplayStart'] ) && $this->request['iDisplayLength'] != '-1' )
    {
      $sLimit = "LIMIT " . $this->request['iDisplayStart'] .", "
        . $this->request['iDisplayLength'];
    }

    // Ordering
    if ( isset( $this->request['iSortCol_0'] ) )
    {
      $sOrder = "ORDER BY  ";

      for ( $i=0 ; $i<intval( $this->request['iSortingCols'] ) ; $i++ )
      {
        if ( $this->request[ 'bSortable_' . intval($this->request['iSortCol_' . $i]) ] == "true" )
        {
          $sOrder .= $aColumns[ intval( $this->request['iSortCol_'.$i] ) ] . "
            " . $this->request['sSortDir_'.$i] . ", ";
        }
      }

      $sOrder = substr_replace( $sOrder, "", -2 );

      if ( $sOrder == "ORDER BY" )
      {
        $sOrder = "";
      }
    }

    $sWhere = "";


    // Filtering
    if ( $this->request['sSearch'] != "" )
    {
      $sWhere .= ($sWhere == "" ? "WHERE (" : "AND (");
      for ( $i=0 ; $i<count($aColumns) ; $i++ )
      {
        $sWhere .= $aColumns[$i]." LIKE '%". $this->request['sSearch'] ."%' OR ";
      }
      $sWhere = substr_replace( $sWhere, "", -3 );
      $sWhere .= ')';
    }

    $totalRows = $this->request['iTotalRecords'] ? $this->request['iTotalRecords'] : R::getCell(" SELECT count(*) FROM `seo` " . $sWhere);

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
      if ( $this->request['bSearchable_'.$i] == "true" && $this->request['sSearch_'.$i] != '' )
      {
        if ( $sWhere === "" )
        {
          $sWhere = "WHERE ";
        }
        else
        {
          $sWhere .= " AND ";
        }
        $sWhere .= $aColumns[$i]." LIKE '%" . $this->request['sSearch_'.$i] ."%' ";
      }
    }

    $sQuery = "
      SELECT `" . implode('`, `', $aColumns ). "` FROM `downloads`
        $sWhere
        $sOrder
        $sLimit
      ";

    $result = R::getAll($sQuery);

    $output = array(
      "sEcho" => intval($this->request['sEcho']),
      "iTotalRecords" => count($result),
      "iTotalDisplayRecords" => $totalRows,
      "aaData" => $result
      );

    header('Content-type: application/json');
    echo json_encode($output);
  }

  /**
   * Forces download of file or text and adds a count hit in the database
   *    GET parameters will be used:
   *      file: file name to be downloaded, or
   *      text: text to be downloaded, packed as a plain text file
   *      name: name of the plain text file to be downloaded
   * Throws an Exception if no valid file is found.
   */
  public function go($file = false, $text = false, $name = false)
  {
    try
    {
      $file = $file ? $file : $this->get['file'];
      $text = $text ? $text : $this->get['text'];
      $name = $name ? $name : $this->get['name'];

      if ($file)
      {
        DownloadAndCount::file($file);
      }
      else if ($text && $name)
      {
        DownloadAndCount::text(utils::safe_decode($text), $name);
      }
    }
    catch (Exception $e)
    {
      error_log($e->getMessage());
      echo 'Something went wrong... ' . $e->getMessage();
    }
  }


  public function resetCount()
  {
    DownloadAndCount::resetCount($this->get['id']);
    echo $this->responseJson('success', tr::get('ok_count_reset'));
  }

}
?>
