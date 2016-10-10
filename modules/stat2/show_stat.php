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

if(isset($_POST['date_start'])){



    $html = '';
    $rows = $dbc->dbselect(array(
        "table"=>"user_art",
        "select"=>"user_art.control_id as root_id,
            users.name as croot,
            COUNT(user_art.id) as kol",
        "joins"=>"LEFT OUTER JOIN users ON user_art.control_id = users.id",
        "where"=>"DATE_FORMAT(user_art.date,'%Y%m%d')>='".date("Ymd",strtotime($_POST['date_start']))."'
			AND DATE_FORMAT(user_art.date,'%Y%m%d')<='".date("Ymd",strtotime($_POST['date_end']))."'",
        "group"=>"users.name"));
    $sql = $dbc->outsql;
    $numRows = $dbc->count;
    if ($numRows > 0) {
        foreach ($rows as $row) {
            
            $html.= '<tr>
                    <td>'.$row['croot'].'</td>
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