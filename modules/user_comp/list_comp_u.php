<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 12.05.2016
 * Time: 14:36
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;

$rows = $dbc->dbselect(array(
        "table"=>"users",
        "select"=>"*"
    )
);
$u_rows = '';
foreach($rows as $row){
    $rows2 = $dbc->dbselect(array(
            "table"=>"user_art",
            "select"=>"*",
            "where"=>"user_id = ".$row['id']." AND art_id = ".$_POST['comp_id']." AND close = 0",
            "limit"=>1
        )
    );
    $numRows = $dbc->count;
    if ($numRows > 0) {
        $row2 = $rows2[0];
        $comp_check = ' checked="checked"';
        $date_sheduled = $row2['date'];
    }
    else{
        $comp_check = '';
        $date_sheduled = '---';
    }

    $u_rows.= '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['name'].'</td>
                    <td>'.$row['login'].'</td>
                    <td>'.$date_sheduled.'</td>
                    <td><input type="checkbox" id="'.$row['id'].'" class="cb-element"'.$comp_check.'></td>
                </tr>';
}

$out_row['result'] = 'OK';
$out_row['html'] = $u_rows;

header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;
?>