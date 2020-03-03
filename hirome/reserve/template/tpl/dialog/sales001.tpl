<div class="modaldisplay">
    <div class="fix">
        <p class="f-left f14">業務清算書情報入力</p>
        <p class="f-right"><input type="button" class="w100 close" value="閉じる" /></p>
    </div>
    <div class="inner">
        <div class="innerbox">
            <div class="fix">
                <div id="leftArea">
                    <p>++[lcl_col1_name]</p>
                    <p>内容</p>
                    <p>金額(税抜)</p>
                    <p>備考</p>
                </div>
                <div id="rightArea">
                    <p><input type="text" name="company" class="params w150 ++[lcl_disabled]" value="++[lcl_company]" /></p>
                    <p><input type="text" name="detail" class="params w300 ++[lcl_disabled]" value="++[lcl_detail]" /></p>
                    <p><input type="text" name="fee" class="params w100 ++[lcl_disabled]" value="++[lcl_fee]" /></p>
                    <p><input type="text" name="addinfo" class="params w200" value="++[lcl_addinfo]" /></p>
                </div>
            </div>
            <div class="fix">
                <p class="f-right t-right"><input type="button" value="確定" class="w100" id="btnSubmit" dispid="1" /></p>
<!-- DALETE_START -->
                <form method="post" name="fdata2" id="fdata2" action="./delete_sales001.php">
                    <input type="hidden" name="dispid" value="1" />
                    <p class="f-right t-right" style="margin-right:20px;"><input type="button" value="削除" class="w100" id="btnDelete" /></p>
                </form>
<!-- DALETE_END -->
            </div>
            <!--エリア区分受け渡しダミー-->
            <input type="hidden" name="area_kbn" class="params" value="++[lcl_area_kbn]" />
            <!--表示順受け渡しダミー-->
            <input type="hidden" name="sort_num" class="params" value="++[lcl_sort_num]" />
            <!--日付ダミー-->
            <input type="hidden" name="dateval" class="params" value="++[lcl_date_val]" />
            <!--コスト区分ダミー-->
            <input type="hidden" name="cost_kbn" class="params" value="++[lcl_cost_kbn]" />
        </div>
    </div>
    <p>++[lcl_message]</p>
</div>
<script type="text/javascript">
$(document).ready(function()
{
    // 削除ボタンの処理
    $("#btnDelete").click(function()
    {
        var message = "この行の内容を削除します。よろしいですか？";
        ret = confirm(message);
        if (ret)
        {
            document.fdata2.method = "post";
            document.fdata2.submit();
        }
    });
});
</script>
