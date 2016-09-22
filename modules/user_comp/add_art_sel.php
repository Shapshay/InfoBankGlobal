<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 07.09.2016
 * Time: 14:12
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;

function setOperDateCounter($oper_id) {
    global $dbc;
    $rows = $dbc->dbselect(array(
            "table"=>"oper_counter",
            "select"=>"id",
            "where"=>"oper_id = '".$oper_id."' AND DATE_FORMAT(date, '%Y%m%d') = ".date("Ymd"),
            "limit"=>"1"
        )
    );
    $numRows = $dbc->count;
    if ($numRows > 0) {
        $row = $rows[0];
        return $row['id'];
    }
    else{
        return 0;
    }
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////

$rows = $dbc->dbselect(array(
        "table"=>"articles",
        "select"=>"articles.id as id, 
            articles.title as title,
            chapters.title as ch",
        "joins"=>"LEFT OUTER JOIN chapters ON articles.ch_id = chapters.id",
        "where"=>"page_id = 2171 AND ch_id<>0",
        "order"=>"ch"
    )
);
//echo $dbc->outsql;
$sel_comp = '<p><select name="comp_id'.$_POST['num'].'" id="comp_id'.$_POST['num'].'" class="small-input">';
$curent_comp = 0;
$i = 0;
$ch = '';
foreach($rows as $row){
    if($i == 0){
        $curent_comp = $row['id'];
        $sel_comp.= '<optgroup label="'.$row['ch'].'">';
        $ch = $row['ch'];
    }

    if($ch!=$row['ch']){
        $sel_comp.= '</optgroup><optgroup label="'.$row['ch'].'">';
        $ch = $row['ch'];
    }

    $sel_comp.= '<option value="'.$row['id'].'">'.$row['title'].'</option>';
    $i++;
}

$out_row['result'] = 'OK';
$out_row['html'] = $sel_comp.'</optgroup></select></p>';

header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;