<h2>Статья: {ART_TITLE}</h2>
<h4>Раздел: {CH_TITLE}</h4>
<div class="content-box-content">
    <button type="button" class="acord_btn" onclick="ShowArt();">СТАТЬЯ</button>
    <div id="HideArtDiv">
        {COMP_ART}
    </div>
<hr>
    <button type="button" class="acord_btn" onclick="ShowQuest();">ВОПРОСЫ</button>
    <div id="HideQuestions" style="display: none;">
        {QUESTIONS}
    </div>
</div>
<div id="task_timer"><canvas id="myCanvas" ></canvas></div>