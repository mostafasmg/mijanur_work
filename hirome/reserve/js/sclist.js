var localSt = localStorage;

$(function() 
{
    var sortFlg = $("#sl_main").attr("sortflg");
    if (sortFlg == "true")
    {
        SetScrolSort();
    }
    
    // スクロールリストが存在する場合は横幅可変の処理を読み込む
    if ($("div.ScrollArea").size() > 0)
    {
        // 横幅の可変オン
        SetScrolValiable();
        
        // 保存された横幅の読込み
        SetSaveScrolValiable();
    }
    
//    var localSt = localStorage;
////    alert(localSt.getItem("testa"));
////    localSt.setItem("testa", "1234");
//    localSt.clear();
})

function SetScrolSort()
{
    $("#header_h").find(".sl_table").find("th").click(function()
    {
        // スクロールリスト全データ取得
        var ArrayRows = new Array();
        var ArraySort = new Array();
        
        // オーダー入替ターゲットのヘッダを取得
        var SortType = $(this).attr("sorttype");
        var SortTarget = 0;
        var SortObject = $(this).html();
        
        // ソート用クラスが指定されている場合はクラスを優先する
        var SortClass = $(this).attr("sort");
        
        $("#header_h").find(".sl_table").find("tr").find("th").each(function(col)
        {
            if (SortClass === undefined)
            {
                // ソートクラスの指定がなければクリックされたヘッダ
                if (SortObject == $(this).html())
                {
                    SortTarget = col;
                }
            }
            else
            {
                // ソートクラスの指定があればクラスが一致するヘッダ
                if ($(this).attr("class").indexOf(SortClass) >= 0)
                {
                    SortTarget = col;
                }
            }
        });
        
        // オリジナルデータの取得
        $("#data").find(".sl_table").find("tr").each(function(row, obj)
        {
            // TRの退避(thisで取得できないので生成する)
            var tagName = $(this).prop("tagName");
            var tagbuf = "<" + tagName + " ";
            for (i = 0; i < obj.attributes.length; i++)
            {
                var targetName = obj.attributes[i].nodeName;
                var targetVal = obj.attributes[i].nodeValue;
                
                tagbuf += targetName + "=\"" + targetVal + "\" ";
            }
            
            tagbuf += ">";
            tagbuf += $(this).html();
            tagbuf += "</" + tagName + ">";
            
            ArrayRows[row] = tagbuf;
            
            // 並び替え判断用データの取得
            $(this).find("td").each(function(col, obj)
            {
                if (SortTarget == col)
                {
                    ArraySort[row] = new Array();
                    ArraySort[row]["index"] = row;
                    ArraySort[row]["text"] = $(this).text();
                }
            });
        });
        
        // ソート順のマーク
        var OrderByValA;
        var OrderByValB;
        if ($(this).attr("orderby") == "ASC")
        {
            $(this).attr("orderby", "DESC");
            OrderByValA = 1;
            OrderByValB = -1;
        }
        else
        {
            // undefinedも含む
            $(this).attr("orderby", "ASC");
            OrderByValA = -1;
            OrderByValB = 1;
        }
        
        // 昇順降順入替
        ArraySort.sort(function(a, b)
        {
            var CompareA = a.text;
            var CompareB = b.text;
            
            // ソートタイプの指定があれば変換する
            if (SortType == "int")
            {
                CompareA = parseInt(CompareA, 10);
                CompareB = parseInt(CompareB, 10);
            }
            
            if (CompareA < CompareB) return OrderByValA;
            if (CompareA > CompareB) return OrderByValB;
            return 0;
        });
        
        $("#data").find(".sl_table").empty();
        
        for (i = 0; i < ArrayRows.length; i++)
        {
            $("#data").find(".sl_table").append(ArrayRows[ArraySort[i]["index"]]);
        }
        
        // CallBackが指定されていれば並び替え後に実行する
        var callback = $(this).parents("table").attr("sortcallback");
        if ( callback != undefined )
        {
            var ret = functions[callback]();
            if (ret == false)
            {
                // 処理を中断
                return false;
            }
        }
    });
}

function SetScrolValiable()
{
    // オリジナルの幅と加減算の値を取得する
    var ArrayBaseWidths = new Array();
    var ArrayHeadObjects = new Array();
    var ArrayDataObjects = new Array();
    $("#header_h").find(".sl_table").find("th").each(function(index)
    {
        $(this).attr("th_index", index);
        ArrayBaseWidths[index] = $(this).css("width").replace("px", "");
        ArrayHeadObjects[index] = $(this);
    });
    $("#data").find(".sl_table").find("tr").each(function()
    {
        $(this).find("td").each(function(index)
        {
            var ClassName = "variable_width" + index;
            $(this).addClass(ClassName);
            ArrayDataObjects[index] = ClassName;
        });
    });
    
    var target_index;
    $("#header_h").find(".sl_table").find("th").bind("contextmenu", function(e)
    {
        $("#contextmenu").css("left", e.pageX);
        $("#contextmenu").css("top", e.pageY);
        $("#contextmenu").show();
        
        target_index = $(this).attr("th_index");
        return false;
    });
    
    $("#ct_big").mousedown(function()
    {
        var target_width = $(ArrayHeadObjects[target_index]).css("width").replace("px", "");
        var add_width = ArrayBaseWidths[target_index] / 10 * 2;
        var new_width = parseInt(target_width) + parseInt(add_width);
        $(ArrayHeadObjects[target_index]).css("width", new_width);
        $("." + ArrayDataObjects[target_index]).css("width", new_width);
        
        // 変更値を保存
        var keyname = GetKeyName(target_index);
        localSt.setItem(keyname, new_width);
    });
    
    $("#ct_small").mousedown(function()
    {
        // 基本サイズよりは小さくさせない
        var target_width = $(ArrayHeadObjects[target_index]).css("width").replace("px", "");
        if (parseInt(ArrayBaseWidths[target_index]) < parseInt(target_width))
        {
            var add_width = ArrayBaseWidths[target_index] / 10 * 2;
            var new_width = parseInt(target_width) - parseInt(add_width);
            $(ArrayHeadObjects[target_index]).css("width", new_width);
            $("." + ArrayDataObjects[target_index]).css("width", new_width);
            
            // 変更値を保存
            var keyname = GetKeyName(target_index);
            localSt.setItem(keyname, new_width);
        }
    });
    
    $("#ct_default").mousedown(function()
    {
        $(ArrayHeadObjects[target_index]).css("width", ArrayBaseWidths[target_index]);
        $("." + ArrayDataObjects[target_index]).css("width", ArrayBaseWidths[target_index]);
        
        // 変更値を保存
        var keyname = GetKeyName(target_index);
        localSt.setItem(keyname, ArrayBaseWidths[target_index]);
    });
    
    $("#ct_close").mousedown(function()
    {
        $("#contextmenu").hide();
    });
}

function SetSaveScrolValiable()
{
    var keyname = GetDisplayName();
    $("#header_h").find(".sl_table").find("th").each(function(index)
    {
        var value = localSt.getItem(keyname+index);
        if (value != null)
        {
            $(this).css("width", value);
        }
    });
    $("#data").find(".sl_table").find("tr").each(function()
    {
        $(this).find("td").each(function(index)
        {
            var value = localSt.getItem(keyname+index);
            if (value != null)
            {
                $(this).css("width", value);
            }
        });
    });
}

function GetKeyName(column)
{
    var pathname = location.pathname;
    var params = pathname.split("/");
    
    var paramlen = params.length;
    var filename = params[paramlen-2];
    
    return filename + column;
}

function GetDisplayName()
{
    var pathname = location.pathname;
    var params = pathname.split("/");
    
    var paramlen = params.length;
    var filename = params[paramlen-2];
    
    return filename;
}
