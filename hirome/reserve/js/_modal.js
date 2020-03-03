$(function() 
{
    setModal();
})
var nextID;
function setModal() 
{
    //HTML読み込み時にモーダルウィンドウの位置をセンターに調整
    adjustCenter("div#modal div.container");
    
    //ウィンドウリサイズ時にモーダルウィンドウの位置をセンターに調整
    $(window).resize(function() {
        adjustCenter("div#modal div.container");
    });
    
    //背景がクリックされた時にモーダルウィンドウを閉じる
    $("div#modal div.background").click(function() 
    {
        displayModal(false);
    });
    
    //リンクがクリックされた時にAjaxでコンテンツを読み込む
    $("input.modal").click(function () 
    {
        var keyname = $(this).attr("name");
        var keyword = $("#" + keyname).val();
        
        var new_href = $(this).attr("href") + "&key=" + keyword;
        $("div#modal div.container").load(new_href, onComplete);
        
        return false;
    });
    
    // 入力ダイアログの起動(ディスタッフ用)
    // Divのダブルクリックで入力起動
    $(document).on("dblclick", "div.modalbox", function(event)
    {
        // エリアIDの取得
        var areaid = $(this).attr("id");
var work = 0;
work = areaid.replace('InfoList','');
work -= 0
work += 1;
nextID = $("#InfoList" + work).attr("id");
        // HREFが個別に指定されていればそちらを優先する
        var href = $(this).attr("href");
        if (href == undefined)
        {
            href = modalboxHref;
        }
        
        var new_href = "/" + dir_strings + "online/dialog/" + href + ".php";
        
        var params = new Object();
        $("#" + areaid + " p.param").each(function(i)
        {
            var name = "param" + i;
            var value = $(this).text();
            
            params[name] = value;
        });
        params["areaid"] = areaid;
        $("div#modal div.container").load(new_href, params, onComplete);
        
        return false;
    });



	// 終了時に次へ遷移します。
	$(document).on("click", "input#next", function(event)
	{

		// ***************************************
		// 戻し用のエリアID
		var areaid = $("#areaid").val();

		// コールバック用
		var callback = "";

		// 入力パラメータを配列で取得する
		var params = $(".params").map(function()
		{
		    if (($(this).attr("name") != "areaid") && ($(this).attr("name") != "callback"))
		    {
		        return $(this).val();
		    }
		    else if($(this).attr("name") == "callback")
		    {
		        // コールバックの取得
		        callback = $(this).val();
		    }
		}).get();

		// エリアIDのDIVに値をセット
		var index = 0;
		$("#" + areaid + " p.param").each(function()
		{
		    var value = params[index];
		    
		    $(this).text(value);
		    index++;
		});

		// コールバックが指定されていれば実行する
		if (callback.length > 0)
		{
		    // 引数として呼び出されたエリアIDを格納する
		    var fnc = new Function("objId", "return "+DialogCallback+"(objId)");
		    fnc(areaid);
		}
		$("div#modal").fadeOut(250);


		// ***************************************
		// 次の表示

		// エリアIDの取得
		var areaid = $("#"+nextID).attr("id");

		var work = 0;
		work = areaid.replace('InfoList','');
		work -= 0
		work += 1;
		nextID = $("#InfoList" + work).attr("id");

		var objThis = $("#"+nextID)

		// HREFが個別に指定されていればそちらを優先する
		var href = objThis.attr("href");
		if (href == undefined)
		{
			href = modalboxHref;
		}

		var new_href = "/" + dir_strings + "online/dialog/" + href + ".php";

		var params = new Object();
		$("#" + areaid + " p.param").each(function(i)
		{
			var name = "param" + i;
			var value = $(this).text();
			params[name] = value;
		});

		params["areaid"] = areaid;
		$("div#modal div.container").load(new_href, params, onComplete);

		return false;
    });




    //コンテンツの読み込み完了時にモーダルウィンドウを開く
    function onComplete() 
    {
        displayModal(true);
        $("div#modal div.container input.close").click(function() 
        {
            displayModal(false);
            return false;
        });
        $("input.select").click(function() 
        {
            setValueAndClose(this);
            return false;
        });
        // サブミットボタンによる画面遷移（ディスタッフ用）
        // 入力確認PHP呼出し
        // GETだとS-JISで投げてしまうのでPOSTに変更(loadの第二引数を利用)
        $("input#btnSubmit").click(function() 
        {
            var dispid = $(this).attr("dispid");
            
            var params = new Object();
            $(".params").each(function()
            {
                var name = $(this).attr("name");
                var value = $(this).val();
                
                params[name] = value;
            });
            
            // 画面ID
            params["dispid"] = dispid;
            
            var new_href = "/" + dir_strings + "online/dialog/check.php";
            $("div#modal div.container").load(new_href, params, onComplete);
            return false;
        });
        // 完了ボタンによるメイン画面への入力データ渡し（ディスタッフ用）
        $("input#complete").click(function()
        {
            setInputDataAndClose();
            return false;
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
    
    // textboxならval、それ以外ならtextを使用する
    setElementValue("#keyword"+dialognum, keyword);
    setElementValue("#retword_a"+dialognum, retword_a);
    setElementValue("#retword_b"+dialognum, retword_b);
    setElementValue("#retword_c"+dialognum, retword_c);
    setElementValue("#retword_d"+dialognum, retword_d);
    $("div#modal").fadeOut(250);
}

// 値をセットして戻る（ディスタッフ用）
function setInputDataAndClose()
{
    // 戻し用のエリアID
    var areaid = $("#areaid").val();
    
    // コールバック用
    var callback = "";
    
    // 入力パラメータを配列で取得する
    var params = $(".params").map(function()
    {
        if (($(this).attr("name") != "areaid") && ($(this).attr("name") != "callback"))
        {
            return $(this).val();
        }
        else if($(this).attr("name") == "callback")
        {
            // コールバックの取得
            callback = $(this).val();
        }
    }).get();
    
    // エリアIDのDIVに値をセット
    var index = 0;
    $("#" + areaid + " p.param").each(function()
    {
        var value = params[index];
        
        $(this).text(value);
        index++;
    });
    
    // コールバックが指定されていれば実行する
    if (callback.length > 0)
    {
        // 引数として呼び出されたエリアIDを格納する
        var fnc = new Function("objId", "return "+DialogCallback+"(objId)");
        fnc(areaid);
    }
    $("div#modal").fadeOut(250);
}

//ウィンドウの位置をセンターに調整
function adjustCenter(target) {
    var margin_top = ($(window).height()-$(target).height())/2;
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
