<div class="modaldisplay">
	<p class="f14">担当 一覧</p>
	<div class="fix">
    	<p class="f-left"><input type="text" class="w200" id="txtRefine" value="" /></p>
    	<p class="f-left"><input type="button" class="w30" id="btnRefine" value="絞込" /></p>
		<p class="f-right"><input type="button" class="w100 close" value="閉じる" /></p>
	</div>
	<hr class="m10">
	<div class="inner m10">
		<input name="dialognum" id="dialognum" type="hidden" value="++[index]" />
		<table class="tbl_list">
			<tr>
				<th>選択</th>
				<th>社員ID</th>
				<th>社員名</th>
			</tr>
<!-- LIST_START -->
			<tr class="data">
				<td><input name="++[loopcnt]" type="button" value="選択" class="w60 select" /></td>
				<td id="listkey++[loopcnt]" align="left">++[emp_id]</td>
				<td id="listret++[loopcnt]" align="left">++[emp_name]</td>
			</tr>
<!-- LIST_END -->
		</table>
	</div>
	<p>++[dlg_message]</p>
</div>
<script type="text/javascript">
$(document).ready(function()
{
    // 絞込みjs読込み
    setRefine();
});
</script>
