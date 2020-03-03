function setRefine() 
{
    $("#btnRefine").click(function()
    {
        var refine = $("#txtRefine").val();
        
        // 未入力なら全表示
        if (refine.length == 0)
        {
            $(".tbl_list tr.data").show();
            return;
        }
        
        $(".tbl_list tr.data").each(function()
        {
// ==========================================================
// 下のパターンで漏れが出るようならこちらを生かす
//            var dispflg = false;
//            
//            $(this).children("td").each(function()
//            {
//                var target = $(this).text();
//                if (target.indexOf(refine) >= 0)
//                {
//                    dispflg = true;
//                }
//            });
//            
//            if (dispflg)
//            {
//                $(this).show();
//            }
//            else
//            {
//                $(this).hide();
//            }
// ==========================================================
            
            // こちらで問題なければ越したことはない
            var target = $(this).children("td").text();
// 2017-02-27:S 大文字小文字を区別しないように改造
//            if (target.indexOf(refine) >= 0)
            if (target.toLowerCase().indexOf(refine.toLowerCase()) >= 0)
// 2017-02-27:E
            {
                $(this).show();
            }
            else
            {
                $(this).hide();
            }
        });
    });

    $("#txtRefine").keypress(function(e)
    {
        // 絞込テキストエリア内のEnterキーでクリックイベントを起こす
        if (e.keyCode === 13)
        {
            $("#btnRefine").trigger("click");
        }
    });
}