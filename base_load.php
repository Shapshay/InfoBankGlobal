<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 22.09.2016
 * Time: 15:30
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("adm/inc/BDFunc.php");
$dbc = new BDFunc;
require_once("adm/inc/RFunc.php");
$rfq = new RFunc;
date_default_timezone_set ("Asia/Almaty");

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/*
$rows = $dbc->dbselect(array(
    "table"=>"bento_users2",
    "select"=>"*"));
foreach($rows as $row){
    if($row['id']>90){
        echo "<p>";
        print_r($row);
        $dbc->element_create("users",array(
            "name" => $row['name'],
            "login" => $row['login'],
            "password" => md5('123456'.SECRET),
            "login_1C" => $row['login_1C'],
            "phone" => $row['phone'],
            "office_id" => $row['office_id'],
            "prod" => $row['prod'],
            "page_id" => 1,
            "reg_date"=>'NOW()'));
        $u_id = $dbc->ins_id;
        $rows2 = $dbc->dbselect(array(
            "table"=>"bento_r_user_role",
            "select"=>"*",
            "where"=>"user_id = ".$row['id']));
        foreach($rows2 as $row2){
            echo "<br>";
            print_r($row2);
            $dbc->element_create("r_user_role",array(
                "user_id" => $u_id,
                "role_id" => $row2['role_id']));
        }
    }
}
*/
/*define("SECRET", 'IIib@v~X');
$rows = $dbc->dbselect(array(
    "table"=>"users",
    "select"=>"*"));
foreach($rows as $row){
    if($row['id']>90){
        $dbc->element_update('users',$row['id'],array(
            "password" => md5('123456'.SECRET)
        ));
    }
}*/
