<?php
# SETTINGS #############################################################################
$moduleName = "user_comp";
$prefix = "./modules/".$moduleName."/";

$tpl->define(array(
	$moduleName => $prefix . $moduleName.".tpl",
	$moduleName . "html" => $prefix . "html.tpl",
	$moduleName . "user_rows" => $prefix . "user_rows.tpl",
));

# MAIN #################################################################################

$tpl->parse("META_LINK", ".".$moduleName."html");

$rows = $dbc->dbselect(array(
		"table"=>"articles",
		"select"=>"articles.id as id, 
            articles.title as title,
            chapters.title as ch",
        "joins"=>"LEFT OUTER JOIN chapters ON articles.ch_id = chapters.id",
        "where"=>"page_id = 2171 AND ch_id<>0",
        "order"=>"ch"
	)
);
//echo $dbc->outsql;
$sel_comp = '';
$curent_comp = 0;
$i = 0;
$ch = '';
foreach($rows as $row){
    if($i == 0){
        $curent_comp = $row['id'];
        $sel_comp.= '<optgroup label="'.$row['ch'].'">';
        $ch = $row['ch'];
    }

    if($ch!=$row['ch']){
        $sel_comp.= '</optgroup><optgroup label="'.$row['ch'].'">';
        $ch = $row['ch'];
    }

	$sel_comp.= '<option value="'.$row['id'].'">'.$row['title'].'</option>';
    $i++;
}
$tpl->assign("COMP_SEL", $sel_comp.'</optgroup>');

$rows = $dbc->dbselect(array(
		"table"=>"users",
		"select"=>"*"
	)
);
foreach($rows as $row){
    $rows2 = $dbc->dbselect(array(
            "table"=>"user_art",
            "select"=>"*",
            "where"=>"user_id = ".$row['id']." AND art_id = ".$curent_comp." AND close = 0",
            "limit"=>1
        )
    );
    $numRows = $dbc->count;
    if ($numRows > 0) {
        $row2 = $rows2[0];
        $comp_check = ' checked="checked"';
        $date_sheduled = $row2['date'];
    }
    else{
        $comp_check = '';
        $date_sheduled = '---';
    }
    
    $tpl->assign("ITEM_ID", $row['id']);
    $tpl->assign("EDT_NAME", $row['name']);
    $tpl->assign("EDT_LOGIN", $row['login']);
    $tpl->assign("EDT_CHECK", $comp_check);
    $tpl->assign("EDT_DATE_SHEDULED", $date_sheduled);

	$tpl->parse("USER_ROWS", ".".$moduleName."user_rows");
}
$tpl->parse(strtoupper($moduleName), ".".$moduleName);







?>