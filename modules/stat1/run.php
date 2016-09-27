<?php
# SETTINGS #############################################################################
$moduleName = "stat1";
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

$offices='';
$office = ROOT_OFFICE;
$rows = $dbc->dbselect(array(
        "table"=>"offices",
        "select"=>"id, title"
    )
);
foreach($rows as $row){
    if($row['id']==ROOT_OFFICE){
        $sel_of = ' selected="selected"';
    }
    else{
        $sel_of = '';
    }
    $offices.='<option value="'.$row['id'].'"'.$sel_of.'>'.$row['title'];
}
$tpl->assign("OFFICES_ROWS", $offices);

$oper_rows='';
$rows = $dbc->dbselect(array(
        "table"=>"users",
        "select"=>"users.*, GROUP_CONCAT(r_user_role.role_id) as role",
        "joins"=>"LEFT OUTER JOIN r_user_role ON users.id = r_user_role.user_id",
        "group"=>"users.id",
        "order"=>"users.name"
    )
);
foreach($rows as $row){
    $this_role = explode(",",$row['role']);
    if(in_array(1,$this_role)){
        $oper_rows.='<option value="'.$row['id'].'">'.$row['name'];
    }
}
$tpl->assign("OPERS_ROWS", $oper_rows);


$rows = $dbc->dbselect(array(
		"table"=>"user_art",
		"select"=>"users.name as oper,
            articles.title as art,
            user_art.date as date",
        "joins"=>"LEFT OUTER JOIN users ON user_art.user_id = users.id
        	LEFT OUTER JOIN articles ON user_art.art_id = articles.id",
        "where"=>"users.office_id = ".ROOT_OFFICE,
        "order"=>"user_art.date",
        "order_type"=>"ASC",
        "group"=>"user_art.id",
        "limit"=>100
	)
);
//echo $dbc->outsql;
foreach($rows as $row){

    $tpl->assign("STAT_NAME", $row['oper']);
    $tpl->assign("STAT_ART", $row['art']);
    $tpl->assign("STAT_DATE", date("d-m-Y H:i",strtotime($row['date'])));
    

	$tpl->parse("USER_ROWS", ".".$moduleName."user_rows");
}
$tpl->parse(strtoupper($moduleName), ".".$moduleName);







?>