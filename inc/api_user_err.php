<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 08.09.2016
 * Time: 16:06
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../adm/inc/BDFunc.php");
$dbc = new BDFunc;

function setUserInArt($u_id, $art_id) {
    global $dbc;
    $rows = $dbc->dbselect(array(
            "table"=>"user_art",
            "select"=>"id",
            "where"=>"user_id = '".$u_id."' AND art_id = '".$art_id."' AND close = 0",
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
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$_POST['u_id'] = '2';
$_POST['control_id'] = '2';
$arr_err = array(21,23);

if(isset($_POST['u_id'])){
    //$arr_err = $_POST['errs'];
    $i=0;
    $where = '';
    foreach ($arr_err as $v){
        if($i!=0){
            $where.= ' OR ';
        }
        $where.= ' err_id = '.$v;
        $i++;
    }
    $rows = $dbc->dbselect(array(
            "table"=>"art_errs",
            "select"=>"*",
            "where"=>"(".$where.")"
        )
    );
    //echo $dbc->outsql."<br>";
    $art_arr = array();
    $i=0;
    foreach ($rows as $row){
        $art_arr[$i] = $row['art_id'];
        $i++;
    }
    //print_r($art_arr);
    //echo "<br>";
    $art_arr = array_unique($art_arr);
    //print_r($art_arr);
    //echo "<br>";
    foreach ($art_arr as $art_id) {
        if(setUserInArt($_POST['u_id'], $art_id)==0){
            $dbc->element_create("user_art",array(
                "user_id" => $_POST['u_id'],
                "art_id" => $art_id,
                "control_id"=>$_POST['control_id'],
                "date" => 'NOW()'));
        }
    }

    $out_row['result'] = 'OK';
}
else{
    $out_row['result'] = 'Err';
}
header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;