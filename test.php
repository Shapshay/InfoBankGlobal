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
        "table"=>"cour_polis",
        "select"=>"*"
    )
);
$numRows = $dbc->count;
if ($numRows > 0) {
    foreach ($rows as $row){
        
    }
}
else {
    echo 0;
}