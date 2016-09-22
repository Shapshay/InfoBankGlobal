<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 20.05.2016
 * Time: 14:44
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../adm/inc/BDFunc.php");
$dbc = new BDFunc;
//$_POST['u_lgn'] = 'oper';

if(isset($_POST['u_lgn'])){
    $user = $dbc->element_find_by_field('users','login',$_POST['u_lgn']);
    $rows = $dbc->dbselect(array(
            "table"=>"user_art",
            "select"=>"user_art.art_id as comp_id,
			articles.title as comp",
            "joins"=>"LEFT OUTER JOIN articles ON user_art.art_id = articles.id
			LEFT OUTER JOIN users ON user_art.user_id = users.id",
            "where"=>"users.login = '".$_POST['u_lgn']."' AND close = 0",
            "limit"=>1
        )
    );
    //echo $dbc->outsql;
    $numRows = $dbc->count;
    if ($numRows > 0) {
        $row = $rows[0];
        $out_row['result'] = 'OK';
        $out_row['id'] = $row['comp_id'];
        $out_row['c_title'] = $row['comp'];
    }
    else{
        $out_row['result'] = 'NO';
    }
}
else{
    $out_row['result'] = 'Err';
}
header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;