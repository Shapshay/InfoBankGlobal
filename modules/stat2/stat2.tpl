<!-- Start Content Box -->

<div class="content-box-content">
    <p>
        <form method="post" name="s_s">
    <p><strong>Дата начала статистики</strong><br>
        <input type="text" name="date_start" id="date_start" value="{EDT_DATE_START}" readonly="readonly" class="text-input small-input">
    </p>
    <p><strong>Дата окончания статистики</strong><br>
        <input type="text" name="date_end" id="date_end" value="{EDT_DATE_END}" readonly="readonly" class="text-input small-input">
    </p>

    <p><button type="button" class="button" onclick="ShowStatTable();">Показать</button></p>
    </form>
		<table id="stat_table" class="display">
			<thead>
			<tr>
				<th>Имя</th>
				<th>Количество назначенных статей</th>
			</tr>
			</thead>
			<tbody id="table_rows">
			{USER_ROWS}
			</tbody>

		</table>
</div> <!-- End .content-box-content -->