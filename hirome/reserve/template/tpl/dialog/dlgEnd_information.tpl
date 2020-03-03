<div class="modaldisplay">
    <div class="fix">
        <p class="f-left f14">入力完了</p>
    </div>
    <div class="inner">
        <div class="innerbox">
            <p class="t-center">入力を正常に完了しました。</p>
            <form action"++[lcl_action]">
            	<p class="t-center" style="margin-top:20px;"><input type="submit" class="w100" value="閉じる" /></p>
            </form>
<!-- BUTTON_NEXT
            <p class="t-center" style="margin-top:20px;"><input type="button" class="w100" id="next" value="次の入力" /></p>
-->
            <input type="hidden" name="areaid" id="areaid" value="++[lcl_areaid]" />
<!-- OUTPARAMS_START -->
            <input type="hidden" name="++[lst_pname]" class="params" value="++[lst_pvalue]" />
<!-- OUTPARAMS_END -->
        </div>
    </div>
    <p>++[lcl_message]</p>
</div>
<script>
$(document).ready(function(){
	$("#completea").click(function(){
	document.fdata.target = "_top";
	document.fdata.method = "post";
	document.fdata.action = "./input.php";
	document.fdata.submit();
	});
});

</script>