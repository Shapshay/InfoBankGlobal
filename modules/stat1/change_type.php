<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 27.09.2016
 * Time: 9:21
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;
date_default_timezone_set ("Asia/Almaty");

if(isset($_POST['oper_type'])){
    $oper_rows='';
    $rows = $dbc->dbselect(array(
            "table"=>"users",
            "select"=>"users.*, GROUP_CONCAT(r_user_role.role_id) as role",
            "joins"=>"LEFT OUTER JOIN r_user_role ON users.id = r_user_role.user_id",
            "where"=>"users.office_id = ".$_POST['office_id'],
            "group"=>"users.id",
            "order"=>"users.name"
        )
    );
    if(is_array($rows)){
        $oper_rows.='<option value="0">Все';
        foreach($rows as $row){
            $this_role = explode(",",$row['role']);
            if(in_array($_POST['oper_type'],$this_role)){
                $oper_rows.='<option value="'.$row['id'].'">'.$row['name'];
            }
        }
    }
    $out_row['html'] = $oper_rows;
    $out_row['result'] = 'OK';
}
else{
    $out_row['result'] = 'Err';
}
header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;