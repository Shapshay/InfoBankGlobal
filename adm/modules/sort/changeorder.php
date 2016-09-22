<?php
error_reporting (E_ALL);
ini_set("display_errors", 1);
require_once("../../inc/BDFunc.php");
$dbc = new BDFunc;

if(isset($_POST['neworder'])){
	$neworder = stripslashes($_POST['neworder']);
	$data = json_decode($neworder);
	if (null == $data) {
		$out_row['result'] = 'Err';
	}
	else{
		foreach ($data as $note) {
			$dbc->element_update('pages',substr($note->id, 5),array(
				'sortfield' => $note->order
			));
		}
		$dbh = null;
	}
	$out_row['result'] = 'OK';
}
else{
	$out_row['result'] = 'Err';
}

header("Content-Type: text/html;charset=utf-8");
$result = preg_replace_callback('/\\\u([0-9a-fA-F]{4})/', create_function('$_m', 'return mb_convert_encoding("&#" . intval($_m[1], 16) . ";", "UTF-8", "HTML-ENTITIES");'),json_encode($out_row));
echo $result;