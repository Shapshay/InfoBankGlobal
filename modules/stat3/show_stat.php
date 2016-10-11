<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 27.09.2016
 * Time: 9:27
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;
date_default_timezone_set ("Asia/Almaty");
function getItemCHPU($id, $item_tab) {
    global $dbc;
    $resp = $dbc->element_find($item_tab,$id);
    return $resp['chpu'];
}

//////////////////////////////////////////////////////////////////////////////////////

if(isset($_POST['oper_type'])){

    $sql_user = '';
    if($_POST['oper_id']>0){
        $sql_user = ' AND users.id = '.$_POST['oper_id'];
    }


    $html = '';
    $rows = $dbc->dbselect(array(
        "table"=>"user_art",
        "select"=>"COUNT(DISTINCT DATE_FORMAT(user_art.date,'%Y-%m-%d %H:%i')) as kol,
            users.name as oper,
            articles.title as art",
        "joins"=>"LEFT OUTER JOIN users ON user_art.user_id = users.id
        	LEFT OUTER JOIN articles ON user_art.art_id = articles.id",
        "where"=>"users.office_id = ".$_POST['office_id'].$sql_user.
            " AND DATE_FORMAT(user_art.date,'%Y%m%d')>='".date("Ymd",strtotime($_POST['date_start']))."'
			AND DATE_FORMAT(user_art.date,'%Y%m%d')<='".date("Ymd",strtotime($_POST['date_end']))."'",
        "group"=>"users.name, articles.title",
        "order"=>"users.name",
        "order_type"=>"ASC",
        "limit"=>$_POST['limit']));
    $sql = $dbc->outsql;
    $numRows = $dbc->count;
    if ($numRows > 0) {
        foreach ($rows as $row) {
            
            $html.= '<tr>
                    <td>'.$row['oper'].'</td>
                    <td>'.$row['art'].'</td>
                    <td>'.$row['kol'].'</td>
                    </tr>';
        }
    }




    $out_row['sql'] = $sql;
    $out_row['html'] = $html;
    $out_row['result'] = 'OK';
}
else{
    $out_row['result'] = 'Err';
}
header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;