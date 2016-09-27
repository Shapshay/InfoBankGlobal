<!-- Start Content Box -->

<div class="content-box-content">
    <p>
        <form method="post" name="s_s">
    <p>
        <strong>Офис:</strong>
        <select name="office_id" id="office_id" onchange="changeOffice();">
            {OFFICES_ROWS}
        </select></p>
    <p>
        <strong>Тип менеджера:</strong>
        <select name="prod" id="prod" onchange="changeOperType();">
            <option value="1">ТД</option>
            <option value="2">Продажники</option>
            <option value="11">СТО</option>
        </select></p>
    <p><strong>Менеджер:</strong>
        <select name="oper_id" id="oper_id">
            <option value="0">Все</option>
            {OPERS_ROWS}
        </select></p>
    <p><strong>Дата начала статистики</strong><br>
        <input type="text" name="date_start" id="date_start" value="{EDT_DATE_START}" readonly="readonly" class="text-input small-input">
    </p>
    <p><strong>Дата окончания статистики</strong><br>
        <input type="text" name="date_end" id="date_end" value="{EDT_DATE_END}" readonly="readonly" class="text-input small-input">
    </p>
    <p><strong>Количество записей:</strong>
        <select name="limit" id="limit">
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="200">200</option>
            <option value="500">500</option>
            <option value="1000">1000</option>
            <option value="5000">5000</option>
        </select>
    </p>
    <p><button type="button" class="button" onclick="ShowStatTable();">Показать</button></p>
    </form>
		<table id="stat_table" class="display">
			<thead>
			<tr>
				<th>Имя</th>
				<th>Дата назначения</th>
				<th>Статья</th>
			</tr>
			</thead>
			<tbody id="table_rows">
			{USER_ROWS}
			</tbody>

		</table>
</div> <!-- End .content-box-content -->