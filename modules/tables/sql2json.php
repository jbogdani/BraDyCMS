<?php
/**
 * @author			Julian Bogdani <jbogdani@gmail.com>
 * @copyright		BraDypUS 2007-2011
 * @license			All rights reserved
 * @since			Nov 27, 2011
 */

require_once 'lib/class.tableAction.inc';

$tbA = new tableAction();

$page = $_GET['page'];

// get the requested page
$limit = $_GET['rows'];

// get how many rows we want to have into the grid
$sidx = $_GET['sidx'];

// get index row - i.e. user click to sort
$sord = $_GET['sord']; 

// get the direction
if(!$sidx) $sidx =1; 

if ($_GET['tot'])
{
	$count = $tbA->getTotal($_GET['tb']);
}
else
{
	$count = $_GET['tot'];
}

if ( $count > 0 )
{
	$total_pages = ceil($count/$limit); 
}
else
{
	$total_pages = 0; 
}

if ( $page > $total_pages AND $total_pages > 0 )
{
	$page = $total_pages;
}

$start =  $limit*$page - $limit; 

// serach
if ($_GET['_search'] === true)
{
	$where = '`' . $_GET['searchField'] . '`';
	switch($_GET['searchOper'])
	{
		case 'eq':
			$where .= " = '" . $_GET['searchString'] . "'";
			break;
		case 'ne':
			$where .= " != '" . $_GET['searchString'] . "'";
			break;
		case 'bw':
			$where .= " LIKE '" . $_GET['searchString'] . "%'";
			break;
		case 'bn':
			$where .= " NOT LIKE '" . $_GET['searchString'] . "%'";
			break;
		case 'ew':
			$where .= " LIKE '%" . $_GET['searchString'] . "'";
			break;
		case 'en':
			$where .= " NOT LIKE '%" . $_GET['searchString'] . "'";
			break;
		case 'cn':
			$where .= " LIKE '%" . $_GET['searchString'] . "%'";
			break;
		case 'nc':
			$where .= " NOT LIKE '%" . $_GET['searchString'] . "'%";
			break;
		case 'nu':
			$where .= " IS NULL ";
			break;
		case 'nn':
			$where .= " IS NOT NULL ";
			break;
		case 'in':
			$where .= " IN ( '" . $_GET['searchString'] . "')";
			break;
		case 'ni':
			$where .= " NOT IN ( '" . $_GET['searchString'] . "')";
			break;
		
		
		}

	}
else
{
	$where = '1';
}

$res2 = $tbA->getAllRows($_GET['tb'], $where, " ORDER BY $sidx $sord LIMIT $start , $limit");

// $SQL = "SELECT a.id, a.invdate, b.name, a.amount,a.tax,a.total,a.note FROM invheader a, clients b WHERE a.client_id=b.client_id ORDER BY $sidx $sord LIMIT $start , $limit";

// set $responce
$responce->query_arrived = base64_decode( $_GET['q'] );
$responce->query_executed = $query;
$responce->page = $page;
$responce->total = $total_pages;
$responce->records = $count;
$responce->tb = $_GET['tb'];

foreach ( $res2 as $k => $arr )
{
	$responce->rows[$k] = $arr;
}
echo json_encode($responce);