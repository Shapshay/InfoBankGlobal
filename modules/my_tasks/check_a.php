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

// получение страницы через POST
function post_content ($url,$postdata) {
    $uagent = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)";

    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    //curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_USERAGENT, $uagent);  // useragent
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    curl_setopt($ch, CURLOPT_COOKIEJAR, "inc/coo.txt");
    curl_setopt($ch, CURLOPT_COOKIEFILE,"inc/coo.txt");

    $content = curl_exec( $ch );
    $err     = curl_errno( $ch );
    $errmsg  = curl_error( $ch );
    $header  = curl_getinfo( $ch );
    curl_close( $ch );

    $header['errno']   = $err;
    $header['errmsg']  = $errmsg;
    $header['content'] = $content;

    return $header;
}

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

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

        // отправка в бенто
        $user_row = $dbc->element_find('users',$_POST['u_id']);
        $url = 'https://mybento.kz/inc/ajax/block_info_del.php';
        $postdata = 'block=1&login='.$user_row['login'].'&art_id='.$_POST['comp_id'];
        $result = post_content ($url,$postdata);


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