<div class="fix modal_title">
    <p class="f-left">スポンサー 一覧</p>
    <p class="f-right"><input type="button" class="close" value="閉じる" /></p>
</div>
<div class="modaldisplay">
    <div class="refineArea fix">
        <p class="f-left"><input type="text" class="w200" id="txtRefine" value="" /></p>
        <p class="f-left"><input type="button" class="dialog_search" id="btnRefine" value="絞込" /></p>
    </div>
    <div class="inner">
        <input name="callbackflg" type="hidden" id="callbackflg" value="true" />
        <input name="dialognum4" id="dialognum4" type="hidden" value="++[index]" />
        <input name="dialognum" id="dialognum" type="hidden" value="++[index]" />
        <table class="tbl_list">
            <tr>
                <th>選択</th>
                <th class="customer_name">スポンサー名</th>
            </tr>
<!-- LIST_START -->
            <tr class="data">
                <td><input name="++[loopcnt]" type="button" value="選択" class="select" /></td>
                <td id="listkey++[loopcnt]" align="left">++[lst_sponcor_name]</td>
            </tr>
<!-- LIST_END -->
        </table>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function()
{
    // 絞込みjs読込み
    setRefine();
});
</script>
