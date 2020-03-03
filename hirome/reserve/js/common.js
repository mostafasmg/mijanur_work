// 階層がルートで動作しない場合にモーダルダイアログが動かないので
// かなりよくないが、ここでグローバル変数をもって暫定対応とする
// Rootからのパスを記述する
var dir_strings = "";

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

$(function() 
{
    // ENTERイベントで次のテキストエリアをフォーカス
    setInputKeyPress();
    
    // タイトル戻るボタン
    setTitleBackEvent();
    
    // メッセージエリアの処理
    //setMessageClickHideEvent();
});
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
        if (filename.length > 0 && filename.indexOf("index") < 0) // index以外からはHistoryBack
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