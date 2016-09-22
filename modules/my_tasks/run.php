<?php
# SETTINGS #############################################################################
$moduleName = "my_tasks";
$prefix = "./modules/".$moduleName."/";

$tpl->define(array(
	$moduleName => $prefix . $moduleName.".tpl",
	$moduleName . "tasks_rows" => $prefix . "tasks_rows.tpl",
	$moduleName . "ch_rows" => $prefix . "ch_rows.tpl",
	$moduleName . "main" => $prefix . "main.tpl",
	$moduleName . "html" => $prefix . "html.tpl",
	$moduleName . "item" => $prefix . "item.tpl",
	$moduleName . "item_html" => $prefix . "item_html.tpl",
	$moduleName . "dostup" => $prefix . "dostup.tpl",
	$moduleName . "dostup2" => $prefix . "dostup2.tpl",
	$moduleName . "q_row" => $prefix . "q_row.tpl",
	$moduleName . "a_row" => $prefix . "a_row.tpl",
));
# MAIN #################################################################################

if(!isset($_GET['comp'])){
	// список задач пользователя
	$rows = $dbc->dbselect(array(
			"table"=>"user_art",
			"select"=>"user_art.art_id as comp_id,
			articles.title as comp,
			user_art.date as date_naz,
			user_art.close as close,
			SUM(questions.xp) as xp",
			"joins"=>"LEFT OUTER JOIN articles ON user_art.art_id = articles.id
			LEFT OUTER JOIN questions ON user_art.art_id = questions.art_id",
			"where"=>"user_id = ".ROOT_ID." AND close = 0",
			"group"=>"user_art.art_id",
			"order"=>"date_naz",
			"order_type"=>"ASC"
		)
	);
	//echo $dbc->outsql;
	$numRows = $dbc->count;
	$prog_anim = '';
	if ($numRows > 0) {
		$task1 = false;
		foreach ($rows as $row) {
			$url = getCodeBaseURL("index.php?menu=".$_GET['menu'])."/?comp=".$row['comp_id'];
			$tpl->assign("COMPLEX_URL", $url);
			$tpl->assign("COMPLEX_ID", $row['comp_id']);
			$tpl->assign("COMPLEX_TITLE", $row['comp']);
			$tpl->assign("COMPLEX_XP", $row['xp']);
			$tpl->parse("TASKS_ROWS", "." . $moduleName . "tasks_rows");
			$task1 = true;

		}
	}
	else{
		$tpl->assign("TASKS_ROWS", 'Нет задач');
	}
	$tpl->assign("PROGRES_ANIM", $prog_anim);
	$tpl->parse("META_LINK", ".".$moduleName."html");
	$tpl->parse(strtoupper($moduleName), ".".$moduleName."main");
}
else{
	// запуск прохождения комплекса
	$rows = $dbc->dbselect(array(
			"table"=>"articles",
			"select"=>"articles.*,
			        chapters.title as ch_title",
            "joins"=>"LEFT OUTER JOIN chapters ON articles.ch_id = chapters.id",
			"where"=>"articles.id = ".$_GET['comp'],
			"limit"=>1
		)
	);
	$row = $rows[0];
    //echo $dbc->outsql;
	$tpl->assign("COMP_TITLE", $row['title']);

	$tpl->assign("CH_TITLE", $row['ch_title']);
    //$task_timer = decoct($task_row['time_on_task'] * 60000);
    $task_timer = $row['time_on_task'] * 60000;
    $task_timer_text = $row['time_on_task'] * 60;
    $tpl->assign("TASK_TIMER", $task_timer);
    $tpl->assign("TASK_TIMER_TEXT", $task_timer_text);
    $tpl->assign("ART_TITLE", $row['title']);
    $tpl->assign("ART_ID", $row['id']);
    $tpl->assign("COMP_ART", $row['content']);
    $art_id = $_GET['comp'];

    $q_rows = $dbc->dbselect(array(
            "table" => "questions",
            "select" => "*",
            "where" => "art_id = " . $art_id,
            "order" => "RAND()"
        )
    );
    foreach ($q_rows as $q_row) {
        $tpl->assign("Q_ID", $q_row['id']);
        $tpl->assign("Q_TITLE", $q_row['title']);

        $a_rows = $dbc->dbselect(array(
                "table" => "answers",
                "select" => "*",
                "where" => "q_id = " . $q_row['id'],
                "order" => "RAND()"
            )
        );
        foreach ($a_rows as $a_row) {
            $tpl->assign("A_ID", $a_row['id']);
            $tpl->assign("A_TITLE", $a_row['title']);

            $tpl->parse("ANSWERS", "." . $moduleName . "a_row");
        }

        $tpl->parse("QUESTIONS", "." . $moduleName . "q_row");
        $tpl->clear("ANSWERS");
    }


    $tpl->parse("META_LINK", "." . $moduleName . "item_html");
    $tpl->parse(strtoupper($moduleName), "." . $moduleName . "item");


}


?>