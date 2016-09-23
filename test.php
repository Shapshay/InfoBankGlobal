<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 22.09.2016
 * Time: 16:48
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("adm/inc/BDFunc.php");
$dbc = new BDFunc;
date_default_timezone_set ("Asia/Almaty");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//echo date("Y-m-d H:i", strtotime("-30 minute"));
$rows = $dbc->dbselect(array(
        "table"=>"user_art",
        "select"=>"id",
        "where"=>"user_id = '2' AND art_id = '36' AND close = 1 AND (date BETWEEN '".date("Y-m-d H:i", strtotime("-30 minute"))."'  AND  '".date("Y-m-d H:i")."')",
        "limit"=>"1"
    )
);
echo $dbc->outsql."<p>";
$numRows = $dbc->count;
if ($numRows > 0) {
    $row = $rows[0];
    echo $row['id'];
}
else {
    echo 0;
}