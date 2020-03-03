// 事前チェックなどのVariable Function用
var functions = new Array();

$(function() {
    setModal();
})

function setModal() {
    
    //HTML読み込み時にモーダルウィンドウの位置をセンターに調整
    adjustCenter("div#modal div.container");
    
    //ウィンドウリサイズ時にモーダルウィンドウの位置をセンターに調整
    $(window).resize(function() {
        adjustCenter("div#modal div.container");
    });
    
    //背景がクリックされた時にモーダルウィンドウを閉じる
    $("div#modal div.background").click(function() {
        displayModal(false);
    });
    
    //リンクがクリックされた時にAjaxでコンテンツを読み込む
//    $("").click(function () 
//-- ビデオ画像クリックに改造
    $("a.modal, input.modal").click(function () 
    {
        // PreCheckが指定されていればそちらを先に実行する
        var precheck = $(this).attr("precheck");
        if ( precheck != undefined )
        {
            var ret = functions[precheck]();
            if (ret == false)
            {
                // 処理を中断
                return false;
            }
        }
        
        var keyname = $(this).attr("name");
// 2018.08.02 Modify S: GET値に特殊記号が入ると無効なURLとなって開けなくなるためエスケープさせる
//        var keyword = $("#" + keyname).val();

        var keyword = encodeURIComponent($("#" + keyname).val());
        
// 2018.08.02 Modify E: GET値に特殊記号が入ると無効なURLとなって開けなくなるためエスケープさせる
        
        var new_href = $(this).attr("href") + "&key=" + keyword;
//      $("div#modal div.container").load(new_href, onComplete);
        
        var params = new Object();
        $("." + keyname).each(function(i)
        {
            var name = $(this).attr("name");
            var value = $(this).val();
            
            params[name] = value;
        });
        
        $("div#modal div.container").load(new_href, params, onComplete);
        
        return false;
    });
    
    //コンテンツの読み込み完了時にモーダルウィンドウを開く
    function onComplete(response, status, xhr) {
        displayModal(true);
        $("div#modal div.container input.close").click(function() {
            displayModal(false);
            return false;
        });
        $("input.select").click(function() 
        {
            var CBDispName = $(this).attr("CBDispName");
            if (CBDispName != undefined)
            {
                setValueAndClose(this);
                setInputDataAndClose(CBDispName, $(this).attr("name"));
                $("div#modal").fadeOut(250);
                return false;
            }
            else
            {
                setValueAndClose(this);
                $("div#modal").fadeOut(250);
                return false;
            }
        });
    }
}
 
//モーダルウィンドウを開く
function displayModal(sign) 
{
    if (sign) 
    {
        $("div#modal").fadeIn(500);
    } 
    else 
    {
        $("div#modal").fadeOut(250);
        
//-- コンテンツの廃棄
        $("div#modal div.container").empty();
    }
    
}

// 値をセットして戻る
function setInputDataAndClose(CBDispName, index)
{
    // コールバック用
    var callbackflg = $("#callbackflg").val();
    
    // コールバックが指定されていれば実行する
    if (callbackflg.length > 0)
    {
        // 引数として呼び出されたエリアIDを格納する
        var fnc = new Function("CBDispName", "index", "return "+DialogCallback+"(CBDispName, index)");
        fnc(CBDispName, index);
    }
}


// 値をセットして戻る
function setValueAndClose(obj) 
{
    var dialognum = $("#dialognum").val();
    var index = $(obj).attr("name");
    var keyword = $("#listkey"+index).text();
    var retword_a = $("#listret_a"+index).text();
    var retword_b = $("#listret_b"+index).text();
    var retword_c = $("#listret_c"+index).text();
    var retword_d = $("#listret_d"+index).text();
    var retword_e = $("#listret_e"+index).text();
    
    // textboxならval、それ以外ならtextを使用する
    setElementValue("#keyword"+dialognum, keyword);
    setElementValue("#retword_a"+dialognum, retword_a);
    setElementValue("#retword_b"+dialognum, retword_b);
    setElementValue("#retword_c"+dialognum, retword_c);
    setElementValue("#retword_d"+dialognum, retword_d);
    setElementValue("#retword_e"+dialognum, retword_e);
    $("div#modal").fadeOut(250);
}

//ウィンドウの位置をセンターに調整
function adjustCenter(target) {
    var margin_top = ($(window).height()-$(target).height())/2 - 50;
    var margin_left = ($(window).width()-$(target).width())/2;
    $(target).css({top:margin_top+"px", left:margin_left+"px"});
}

// INPUTタグかそれ以外を判断して値をセット
function setElementValue(objId, value)
{
    if ($(objId).size() > 0)
    {
        if ($(objId)[0].nodeName == "INPUT")
        {
            $(objId).val(value);
        }
        else
        {
            $(objId).text(value);
        }
    }
}
