// 階層がルートで動作しない場合にモーダルダイアログが動かないので
// かなりよくないが、ここでグローバル変数をもって暫定対応とする
// Rootからのパスを記述する
var dir_strings = "";

$(function() 
{
    // ENTERイベントで次のテキストエリアをフォーカス
    setInputKeyPress();
    
    // タイトル戻るボタン
    setTitleBackEvent();
    
    // メッセージエリアの処理
    setMessageClickHideEvent();
    
// ==============================================================
// 2019.05.28 S:DEL HEIJI スクロールリスト処理用JSファイルへ移動
// ==============================================================
//    // スクロールリストの処理
//    setScrollList();
// ==============================================================
// 2019.05.28 E:DEL HEIJI スクロールリスト処理用JSファイルへ移動
// ==============================================================
});

// ==============================================================
// 2019.05.28 S:DEL HEIJI スクロールリスト処理用JSファイルへ移動
// ==============================================================
//function setScrollList() 
//{
//    // スクロールリストのマウスオーバー
//    $("#sl_main").find("#data").find("table tr").hover(
//        function()
//        {
//// 2019.05.28 S:Modify HEIJI rownumを使用しなくても良いように改造
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val == undefined)
//            {
//                $(this).attr("orgcolor", $(this).find("td").css("background-color"));
//                $(this).find("td").css("background-color", "#AFEEEE");
//            }
//// 2019.05.28 E:Modify HEIJI rownumを使用しなくても良いように改造
//        },
//        function()
//        {
//// 2019.05.28 S:Modify HEIJI rownumを使用しなくても良いように改造
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val == undefined)
//            {
//                $(this).find("td").css("background-color", $(this).attr("orgcolor"));
//            }
//// 2019.05.28 E:Modify HEIJI rownumを使用しなくても良いように改造
//        }
//    );
//    
//    // スクロールリストのクリック
//    $("#sl_main").find("#data").find("table tr").click(function()
//    {
//// 2019.05.28 S:Delete HEIJI rownumを使用しなくても良いように改造
//        // 他の行の背景色を戻す
//        $("#sl_main").find("#data").find("table tr").each(function()
//        {
//            var attr_val = $(this).attr("selected_key");
//            if (attr_val != undefined)
//            {
//                $(this).find("td").css("background-color", $(this).attr("selected_key"));
//            }
//        });
//        
//        // 全てのselected_keyを外す
//        $("#sl_main").find("#data").find("table tr").removeAttr("selected_key");
//        
//        // 選択行にselected_key属性を付与
//        $(this).attr("selected_key", $(this).attr("orgcolor"));
//        
//        // 選択行の色を変える
//        $(this).find("td").css("background-color", "#00BFFF");
//// 2019.05.28 E:Delete HEIJI rownumを使用しなくても良いように改造
//    });
//}
// ==============================================================
// 2019.05.28 E:DEL HEIJI スクロールリスト処理用JSファイルへ移動
// ==============================================================

// 数値をカンマ区切りで返す
function CurrencyFormat(num, opt_yenmk_flg)
{
    // 円マークフラグが立っていたら先頭に\をつけて返す
    var yenmk_flg = 0;
    if (yenmk_flg != undefined)
    {
        yenmk_flg = opt_yenmk_flg;
    }
    
    // カンマ処理
    var afterStr = String(num).replace( /(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    
    if (yenmk_flg == 1)
    {
        // \マークつきで戻す
        return AddYenMark(afterStr);
    }
    else
    {
        // \マーク無しで戻す
        return afterStr;
    }
}

// 文字列の先頭に\マークをつけて返す
function AddYenMark(str)
{
    return "\\" + str;
}

// ENTERイベントで次のテキストエリアをフォーカス
function setInputKeyPress()
{
    $("input").keypress(function(e)
    {
        if (e.keyCode === 13)
        {
            // テキストでなければ抜ける
            var type = $(this).attr("type");
            if (type != "text")
            {
                // SUBMIT、若しくはBUTTONならENTERを通す
                if (type == "submit" || type == "button")
                {
                    return;
                }
                return false;
            }
            
            var nextIndex = $("input[type=text]").index(this) + 1;
            
            if ($("input[type=text]").eq(nextIndex).length === 0)
            {
                nextIndex = 0;
            }
            
            $("input[type=text]").eq(nextIndex).focus();
            return false;
        }
    });
}

// タイトル戻るボタンの実装
function setTitleBackEvent()
{
    $("#btnTitleBack").click(function()
    {
        var pathname = location.pathname;
        var params = pathname.split("/");
        
        var paramlen = params.length;
        var filename = params[paramlen-1];
        if (filename.length > 0 && filename.indexOf("index") < 0)
        {
            // 前の画面に戻す
            history.back();
        }
        else
        {
            // ホーム画面に戻す
            location.href = "../../";
        }
    });
}

// メッセージエリアの処理
function setMessageClickHideEvent()
{
    $("#MessageArea").click(function()
    {
        
        $(this).find("p").toggle("slow");
    });
}

// スクロールリストの高さを拡張する
function setScrollListHeight(new_height)
{
    // Baseの高さは300px、263px、278px
    var sl_main_height = $("#sl_main").css("height").replace("px", "");
    var header_v_height = $("#header_v").css("height").replace("px", "");
    var data_height = $("#data").css("height").replace("px", "");
    
    var diff_height = new_height - sl_main_height;
    
    // 計算後の値に変更
    sl_main_height = parseInt(sl_main_height) + parseInt(diff_height);
    header_v_height = parseInt(header_v_height) + parseInt(diff_height);
    data_height = parseInt(data_height) + parseInt(diff_height);
    
    // 再セット
    $("#sl_main").css("height", sl_main_height);
    $("#header_v").css("height", header_v_height);
    $("#data").css("height", data_height);
}
