<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 06.09.2016
 * Time: 12:08
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;

if(isset($_POST['id'])){
    $rows = $dbc->dbselect(array(
            "table"=>"art_errs",
            "select"=>"err_id",
            "where"=>"art_id=".$_POST['id']
        )
    );
    $out_row['result'] = 'OK';
    $i=0;
    $numRows = $dbc->count;
    $out_row['count'] = $numRows;
    if ($numRows > 0) {
        foreach($rows as $row){
            $out_row['err'][$i] = $row['err_id'];
            $i++;
        }
    }

}
else{
    $out_row['result'] = 'Err1';
}

header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;