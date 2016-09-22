<!-- Start Content Box -->
<div class="content-box-content">
	<form method="post" enctype="multipart/form-data" name="s_s">
		<fieldset>
			<p>
				<input type="hidden" id="num_art" value="1">
				<label>Статья</label>
				<select name="comp_id" id="comp_id" class="small-input" onchange="changeComp();">
					{COMP_SEL}
				</select>
			</p>
            <div id="ArtSelDiv">

            </div>
            <p>
                <input type="button" value="+" class="button" onclick="addArtSel();">
                <input type="button" value="-" class="button" onclick="delArtSel();">
            </p>
		<p><input type="button" value="Добавить" class="button" onclick="addUComp();"></p>
		<p>
			<input type="checkbox" class="checkAll"><label>Назначить всем</label>
		</p>
		<table id="stat_table" class="display">

			<thead>
			<tr>
				<th>ID</th>
				<th>Имя</th>
				<th>Логин</th>
				<th>Дата назначения</th>
				<th>Назначить</th>
			</tr>
			</thead>
			<tbody id="u_table">
			{USER_ROWS}
			</tbody>

		</table>
		<p><input type="button" value="Добавить" class="button" onclick="addUComp();"></p>
		</fieldset>

		<div class="clear"></div><!-- End .clear -->

	</form>
</div> <!-- End .content-box-content -->