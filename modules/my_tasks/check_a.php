<?php
/**
 * Created by PhpStorm.
 * User: Skiv
 * Date: 16.05.2016
 * Time: 12:52
 */
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../adm/inc/BDFunc.php");
$dbc = new BDFunc;

$a_row = $dbc->element_find('answers',$_POST['a_id']);

if($a_row['correct']==1){
    $out_row['result'] = 'OK';
    $out_row['answer'] = $a_row['title'];

    $cur_comp_rows = $dbc->dbselect(array(
            "table"=>"user_art",
            "select"=>"date",
            "where"=>"art_id = ".$_POST['comp_id']." AND user_id = ".$_POST['u_id'],
            "order"=>"date DESC",
            "limit"=>1
        )
    );
    $cur_comp_row = $cur_comp_rows[0];
    $nazCompDate = $cur_comp_row['date'];


    // проверяем на прохождение статьи
    $close_art = 0;
    $type_ok = '';
    $q_rows = $dbc->dbselect(array(
            "table"=>"questions",
            "select"=>"*",
            "where"=>"art_id = ".$_POST['comp_id']
        )
    );
    $numQ = $dbc->count;
    $a_rows = $dbc->dbselect(array(
            "table"=>"comp_log",
            "select"=>"DISTINCT q_id",
            "where"=>
                "u_id = ".$_POST['u_id']." AND ".
                "art_id = ".$_POST['comp_id']." AND ".
                "correct = 1 AND 
				DATE_FORMAT(date,'%Y%m%d%H%i') > '".date('YmdHi',strtotime($nazCompDate))."'	"
        )
    );
    $numA = $dbc->count;
    $out_row['debug'] = $numQ.'='.$numA;
    //$type_ok = $numQ.'close_art'.$numA;
    if($numQ==($numA+1)){
        $close_art = 1;
        $type_ok = 'close_art';
        $dbc->element_fields_update('user_art',
            " WHERE user_id = ".$_POST['u_id']." AND art_id = ".$_POST['comp_id'],
            array("close" => 1));
    }

    $q_row = $dbc->element_find('questions',$_POST['q_id']);

    $dbc->element_create("comp_log",array(
        "u_id" => $_POST['u_id'],
        "art_id" => $_POST['art_id'],
        "q_id" => $_POST['q_id'],
        "a_id" => $_POST['a_id'],
        "correct" => 1,
        "xp" => $q_row['xp'],
        "close_art" => $close_art,
        "date" => 'NOW()'
    ));

    $sql = "UPDATE users SET xp = xp + ".$q_row['xp']." WHERE id = ".$_POST['u_id'];
    $dbc->element_free_update($sql);


    $out_row['type_ok'] = $type_ok;

}
else{
    $out_row['result'] = 'Err';
    $q_row = $dbc->element_find('questions',$_POST['q_id']);
    $out_row['hint'] = $q_row['hint'];
    $dbc->element_create("comp_log",array(
        "u_id" => $_POST['u_id'],
        "art_id" => $_POST['art_id'],
        "q_id" => $_POST['q_id'],
        "a_id" => $_POST['a_id'],
        "correct" => 0,
        "date" => 'NOW()'
    ));
}

header("Content-Type: text/html;charset=utf-8");
echo json_encode($out_row);

?>