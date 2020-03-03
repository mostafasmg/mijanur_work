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
            if (target.indexOf(refine) >= 0)
            {
                $(this).show();
            }
            else
            {
                $(this).hide();
            }
        });
    });
}