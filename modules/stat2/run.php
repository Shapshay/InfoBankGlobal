<?php
# SETTINGS #############################################################################
$moduleName = "stat2";
$prefix = "./modules/".$moduleName."/";

$tpl->define(array(
	$moduleName => $prefix . $moduleName.".tpl",
	$moduleName . "html" => $prefix . "html.tpl",
	$moduleName . "user_rows" => $prefix . "user_rows.tpl",
));

# MAIN #################################################################################

$tpl->parse("META_LINK", ".".$moduleName."html");


$dateStart = date('d-m-Y',strtotime(date("d-m-Y", mktime()) . " - 3 day"));
$tpl->assign("EDT_DATE_START", $dateStart);
$tpl->assign("EDT_DATE_END", date("d-m-Y"));

$rows = $dbc->dbselect(array(
		"table"=>"user_art",
		"select"=>"user_art.control_id as root_id,
            users.name as croot,
            COUNT(user_art.id) as kol",
        "joins"=>"LEFT OUTER JOIN users ON user_art.control_id = users.id",
        "group"=>"users.name"
	)
);
//echo $dbc->outsql;
foreach($rows as $row){

    $tpl->assign("STAT_NAME", $row['croot']);
    $tpl->assign("STAT_KOL", $row['kol']);
    

	$tpl->parse("USER_ROWS", ".".$moduleName."user_rows");
}
$tpl->parse(strtoupper($moduleName), ".".$moduleName);







?>