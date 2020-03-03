<?php
require_once("Ex/CLS_Page_Ex.php");
require_once("Ex/CLS_Mail_Ex.php");
require_once("CLS_InputChecker.php");

// 予約クラス
class CLS_Reserve extends CLS_Page_Ex
{
    private $categoryArray = array();
    private $dishArray = array();
    private $countArray = array();
    
    // 税率を定数で管理
    const TAX = 1.08;
    
    // それぞれのカテゴリにメニューがいくつあるかを定数で管理する
    const CATEGORY1_COUNT = 13;
    const CATEGORY2_COUNT = 10;
    const CATEGORY3_COUNT = 3;
    const CATEGORY4_COUNT = 2;
    const CATEGORY5_COUNT = 13;
    const CATEGORY6_COUNT = 3;
    const CATEGORY7_COUNT = 2;
    
    // 客先の注文受付アドレス
    const ORDER_MAILADDRESS = "hp_order@hiromezen.co.jp";
    
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    
    // コンストラクタ
    public function __construct()
    {
        parent::__construct(0);
        
        $this->categoryArray[1] = $this->ResWord["category1"];
        $this->categoryArray[2] = $this->ResWord["category2"];
        $this->categoryArray[3] = $this->ResWord["category3"];
        $this->categoryArray[4] = $this->ResWord["category4"];
        $this->categoryArray[5] = $this->ResWord["category5"];
        $this->categoryArray[6] = $this->ResWord["category6"];
        $this->categoryArray[7] = $this->ResWord["category7"];
        
        $this->countArray[0] = self::CATEGORY1_COUNT;
        $this->countArray[1] = self::CATEGORY2_COUNT;
        $this->countArray[2] = self::CATEGORY3_COUNT;
        $this->countArray[3] = self::CATEGORY4_COUNT;
        $this->countArray[4] = self::CATEGORY5_COUNT;
        $this->countArray[5] = self::CATEGORY6_COUNT;
        $this->countArray[6] = self::CATEGORY7_COUNT;
    }
    
    // index.php
    public function index()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("index.html");
        
        // エラーメッセージの置換
        $contents = $this->ShowDisplayMessage($contents);
        
        
        // 料理情報を配列にセットする
        
        // カテゴリごとにセットする
        $count = count($this->categoryArray);
        
        for ($i = 0; $i < $count; $i++)
        {
            // $aaa[1][1]["name"] のような形で配列を作成
            for ($j = 0; $j < $this->countArray[$i]; $j++)
            {
                $dishNo = sprintf("%d_%d", ($i+1), ($j+1));
                
                $this->dishArray[$i+1][$j+1]["name"] = $this->ResWord["dish{$dishNo}"];
                $this->dishArray[$i+1][$j+1]["price"] = $this->ResWord["price{$dishNo}"];
                $this->dishArray[$i+1][$j+1]["detail"] = $this->GetResMessage("dish{$dishNo}");
                
                // 初期値として個数は0をセット
                if(count($_SESSION["i"]["dish_qtys"]) == 0)
                {
                    $this->dishArray[$i+1][$j+1]["qty"] = 0;
                }
                else
                {
                    // idを生成する
                    $id = sprintf("%d_%d", ($i+1), ($j+1));
                    //$id = ($i+1) . "_". ($j+1);
                    $this->dishArray[$i+1][$j+1]["qty"] = $_SESSION["i"]["dish_qtys"][$id];
                }
            }
        }
        
        // 並び替えの処理用にソート順をカンマ区切りでセット 順番のメンテナンス時はこの文字列を変更する
        $sort_array = array();
        $sort_array["ShidashiArea"] = "3,1,9,6,8,13,12,2,7,10,4,5,11";
        $sort_array["ZenArea"] = "6,7,9,1,5,3,4,8,10,2";
        $sort_array["SyojinArea"] = "1,2,3";
        $sort_array["PartyArea"] = "2,1";
        $sort_array["TensinArea"] = "3,2,1";
        $sort_array["SidemenuArea"] = "2,3,5,1,4,11,6,7,9,10,8,12,13";
        $sort_array["ChildArea"] = "1,2";
        
        // 料理のエリアを動的に生成する
        $contents = $this->SetShidashiAreaVariable($contents, $this->dishArray[1], $sort_array["ShidashiArea"]);
        $contents = $this->SetZenAreaVariable($contents, $this->dishArray[2], $sort_array["ZenArea"]);
        $contents = $this->SetSyojinAreaVariable($contents, $this->dishArray[3], $sort_array["SyojinArea"]);
        $contents = $this->SetPartyAreaVariable($contents, $this->dishArray[4], $sort_array["PartyArea"]);
        $contents = $this->SetTensinAreaVariable($contents, $this->dishArray[6], $sort_array["TensinArea"]);
        $contents = $this->SetSidemenuAreaVariable($contents, $this->dishArray[5], $sort_array["SidemenuArea"]);
        $contents = $this->SetChildAreaVariable($contents, $this->dishArray[7], $sort_array["ChildArea"]);
        
        
        // execタイプの設定
        $_SESSION["i"]["exectype"] = "index";
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "index");
        
        echo $contents;
    }
    
    
    
     // input.php
    public function input()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("input.html");
        
        
        
        // 戻りページ対策
        if($_SESSION["i"]["exectype"] != "check")
        {
            // セッション変数にフォームから取得した値をセットする
            $this->SetSessionVariableForm();
        }
        
        
        // 金額を計算
        $table = $_SESSION["i"]["dish_qtys"];
        
        foreach($table as $key => $val)
        {
            
            // 1個以上注文がある料理のみ表示する
            if($table[$key] > 0)
            {
                
                // データの用意
                
                $price = $this->ResWord["price{$key}"] * $table[$key];
                
                $total_price = $total_price + $price;
            }
        }
        
        $_SESSION["i"]["total_price"] = $total_price;
        
        //チェック処理
        $errflg = $this->InputCheck();
        
        if ($errflg == false)
        {
            
            header("LOCATION: ./index.php" );
            exit();
        }
        
        // エラーメッセージの置換
        $contents = $this->ShowDisplayMessage($contents);
        
        // execタイプの設定
        $_SESSION["i"]["exectype"] = "input";
        
        // 予約可能最小日と最大日をセットする
        $_SESSION["i"]["delivery_min_date"] = date("Y-m-d", strtotime("+2 day", time()));
        $_SESSION["i"]["delivery_max_date"] = date("Y-m-d", strtotime("+2 month", time()));
        
        if($_SESSION["i"]["delivery_date"] == "")
        {
            $_SESSION["i"]["delivery_date"] = date("Y-m-d", strtotime("+2 day", time()));
            $_SESSION["i"]["delivery_time"] = date("08:00:00");
        }
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "input");
        
        echo $contents;
    }
    
    // check.php
    public function check()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("check.html");
        
        $_SESSION["i"]["exectype"] = "check";
        
        // セッション変数にフォームから取得した値をセットする
        $this->SetSessionVariableFormCustomer();
        
        // 入力チェック
        $errflg = $this->InputCheckCustomer();
        
        if (!$errflg)
        {
            // 入力チェックでエラーが発生している場合は同一ページ戻し
            header("LOCATION: ./input.php" );
            exit();
        }
        
        $contents = $this->SetCheckOrderAreaVariable($contents, $_SESSION["i"]["dish_qtys"]);
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "check");
        
        echo $contents;
    }
    
    // order.php
    public function order()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("end.html");
        
        // セッションが生きていれば処理する
        if (!isset($_SESSION["i"]["name"]))
        {
            // トップページへ戻す
           $contents = $this->replace_template_sys("error.html");
           unset($_SESSION["i"]);
           echo $contents;
           exit();
        }
        
        $order = "";
        
        $table = $_SESSION["i"]["dish_qtys"];
        
        // 注文内容テキスト生成
        foreach($table as $key => $val)
        {
            // 1個以上注文がある料理のみ表示する
            if($table[$key] > 0)
            {
                
                $order = $order .$this->ResWord["mail_dish{$key}"]. " ". $table[$key]. "個"."\n";
            }
        }
        

        // 送信先アドレス指定
        $mail =  self::ORDER_MAILADDRESS;
        
        // メール送信
        $objMail = new CLS_Mail_Ex();
        $blnRet = $objMail->SendOrder($mail, $order);
        if(!$blnRet)
        {
            // メール送信失敗
            $contents = $this->replace_template_sys("error.html");
            unset($_SESSION["i"]);
            echo $contents;
            exit();
        }
        
        // 注文者への予約確認メール
        $blnRet = $objMail->SendUser($_SESSION["i"]["mail"], $order);
        if(!$blnRet)
        {
            // メール送信失敗
            $contents = $this->replace_template_sys("error.html");
            unset($_SESSION["i"]);
            echo $contents;
            exit();
        }
        
        // リロードによる多重登録を防ぐためにセッションをクリア
        unset($_SESSION["i"]);
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "end");
        
        echo $contents;
    }

    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
    // 入力項目の置換
    private function SetLclInputVariable($contents, $page)
    {
        // index
        if ($page == "index")
        {
            $contents = str_replace("++[lcl_exectype]", $_SESSION["i"]["exectype"], $contents);
            
            $contents = str_replace("++[lcl_id]", $_SESSION["i"]["id"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail"], $contents);
        }
        
        if ($page == "input")
        {
            $contents = str_replace("++[lcl_exectype]", $_SESSION["i"]["exectype"], $contents);
            
            $contents = str_replace("++[lcl_name]", $_SESSION["i"]["name"], $contents);
            $contents = str_replace("++[lcl_zipcode]", $_SESSION["i"]["zipcode"], $contents);
            $contents = str_replace("++[lcl_kana]", $_SESSION["i"]["kana"], $contents);
            $contents = str_replace("++[lcl_address]", $_SESSION["i"]["address"], $contents);
            $contents = str_replace("++[lcl_tel]", $_SESSION["i"]["tel"], $contents);
            $contents = str_replace("++[lcl_delivery_date]", $_SESSION["i"]["delivery_date"], $contents);
            $contents = str_replace("++[lcl_delivery_min_date]", $_SESSION["i"]["delivery_min_date"], $contents);
            $contents = str_replace("++[lcl_delivery_max_date]", $_SESSION["i"]["delivery_max_date"], $contents);
            $contents = str_replace("++[lcl_delivery_time]", $_SESSION["i"]["delivery_time"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail"], $contents);
            $contents = str_replace("++[lcl_note]", $_SESSION["i"]["note"], $contents);
        }
        
        if ($page == "check")
        {
            $contents = str_replace("++[lcl_exectype]", $_SESSION["i"]["exectype"], $contents);
            
            $contents = str_replace("++[lcl_name]", $_SESSION["i"]["name"], $contents);
            $contents = str_replace("++[lcl_zipcode]", $_SESSION["i"]["zipcode"], $contents);
            $contents = str_replace("++[lcl_kana]", $_SESSION["i"]["kana"], $contents);
            $contents = str_replace("++[lcl_address]", $_SESSION["i"]["address"], $contents);
            $contents = str_replace("++[lcl_tel]", $_SESSION["i"]["tel"], $contents);
            $contents = str_replace("++[lcl_delivery_date]", $_SESSION["i"]["delivery_date_disp"], $contents);
            $contents = str_replace("++[lcl_delivery_min_date]", $_SESSION["i"]["delivery_min_date"], $contents);
            $contents = str_replace("++[lcl_delivery_max_date]", $_SESSION["i"]["delivery_max_date"], $contents);
            $contents = str_replace("++[lcl_delivery_time]", $_SESSION["i"]["delivery_time_disp"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail"], $contents);
            $contents = str_replace("++[lcl_note]", $_SESSION["i"]["note"], $contents);
        }

        return $contents;
    }
    
    // セッション変数にフォームから取得した値をセットする
    private function SetSessionVariableForm()
    {
        // サブタイトルの判断
        $subtile = "subtitle-" . $_SESSION["i"]["exectype"];
        $_SESSION["i"]["subtitle"] = $this->ResWord[$subtile];
        
        $_SESSION["i"]["id"] = $this->post["id"];
        $_SESSION["i"]["dish_qtys"] = $this->post["dish_qty"];
    }
    
    // セッション変数にフォームから取得した値をセットする(お客様情報)
    private function SetSessionVariableFormCustomer()
    {
        // サブタイトルの判断
        $subtile = "subtitle-" . $_SESSION["i"]["exectype"];
        $_SESSION["i"]["subtitle"] = $this->ResWord[$subtile];
        
        $_SESSION["i"]["name"] = $this->post["name"];
        $_SESSION["i"]["kana"] = $this->post["kana"];
        $_SESSION["i"]["zipcode"] = $this->post["zipcode"];
        $_SESSION["i"]["address"] = $this->post["address"];
        $_SESSION["i"]["tel"] = $this->post["tel"];
        $_SESSION["i"]["delivery_date"] = $this->post["delivery_date"];
        $_SESSION["i"]["delivery_time"] = $this->post["delivery_time"];
        $_SESSION["i"]["mail"] = $this->post["mail"];
        $_SESSION["i"]["note"] = $this->post["note"];
        
        // 日時を漢数字フォーマット化
        $dates = explode("-" , $_SESSION["i"]["delivery_date"]);
        $times = explode(":" , $_SESSION["i"]["delivery_time"]);
        
        $_SESSION["i"]["delivery_date_disp"] = sprintf("%s年%s月%s日", $dates[0], $dates[1], $dates[2]);
        $_SESSION["i"]["delivery_time_disp"] = sprintf("%s時%s分", $times[0], $times[1]);
    }
    
    // 仕出弁当エリア置換
    private function SetShidashiAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH1_START -->";
        $block_end   = "<!-- DISH1_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
                $path = $this->ResWord["path1"] . $index  . ".jpg";

                // インプットタグに名前を振る
                $input_id = "1_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $input_id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // 善エリア置換
    private function SetZenAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH2_START -->";
        $block_end   = "<!-- DISH2_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "2_" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
                $path = $this->ResWord["path2"] . $index  . ".jpg";
                
                // インプットタグに名前を振る
                $input_id = "2_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // お子さまエリア置換
    private function SetChildAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH7_START -->";
        $block_end   = "<!-- DISH7_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "7_" . $index;
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
                $path = $this->ResWord["path7"] . $this->ResWord["path7_{$index}"]  . ".jpg";
                
                // インプットタグに名前を振る
                $input_id = "7_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // 精進エリア置換
    private function SetSyojinAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH3_START -->";
        $block_end   = "<!-- DISH3_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "3_" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
// 2019.07.26 S:HTML側が番号どおりの並びでないためリソースをつかって強制対応
//                $path = $this->ResWord["path3"] . $i  . ".jpg";
                $path = $this->ResWord["path3"] . $this->ResWord["path3_{$index}"]  . ".jpg";
// 2019.07.26 E:HTML側が番号どおりの並びでないためリソースをつかって強制対応
                
                // インプットタグに名前を振る
                $input_id = "3_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // 点心エリア置換
    private function SetTensinAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH6_START -->";
        $block_end   = "<!-- DISH6_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "6_" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
// 2019.07.26 S:HTML側が番号どおりの並びでないためリソースをつかって強制対応
//                $path = $this->ResWord["path6"] . $i  . ".jpg";
                $path = $this->ResWord["path6"] . $this->ResWord["path6_{$index}"]  . ".jpg";
// 2019.07.26 E:HTML側が番号どおりの並びでないためリソースをつかって強制対応
                
                // インプットタグに名前を振る
                $input_id = "6_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        else
        {
            $rows  = "<th>" .$this->GetResMessage("no_entry"). "</th>";
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // オードブルエリア置換
    private function SetPartyAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH4_START -->";
        $block_end   = "<!-- DISH4_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "4_" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                // 画像パス 
                $path = $this->ResWord["path4"] . $this->ResWord["perty{$index}"]. ".JPG";
                
                // インプットタグに名前を振る
                $input_id = "4_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // サイドメニューエリア置換
    private function SetSidemenuAreaVariable($contents, $table, $sortnos)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- DISH5_START -->";
        $block_end   = "<!-- DISH5_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = count($table) +1 ;
        
        $sortnoArray = explode(",", $sortnos);
        
        if($count > 0)
        {
            for ($i = 1; $i < $count; $i++)
            {
                $row = $row_template;
                
                $index = $sortnoArray[$i-1];
                
                // データの用意
                $id = "5_" . $index;
                $customer_id = $table[$index]["customer_id"];
                $dishname = $table[$index]["name"];
                $price = $table[$index]["price"];
                $detail = $table[$index]["detail"];
                $qty = $table[$index]["qty"];
                
                
                // インプットタグに名前を振る
                $input_id = "5_" . $id;
               
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $dishname, $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_price]", $price, $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $qty, $row);
                
                // データを溜め込む
                $rows .= $row;
            }
        }
        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // 注文確認エリア置換
    private function SetCheckOrderAreaVariable($contents, $table)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- ORDER_START -->";
        $block_end   = "<!-- ORDER_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $row_nums = "";
        $rows = "";
        $count = 0 ;
        $total_price = 0;
        
        foreach($table as $key => $val)
        {
            $row = $row_template;
            $title = "";
            
            // 1個以上注文がある料理のみ表示する
            if($table[$key] > 0)
            {
                
                // データの用意
                
                $price = $this->ResWord["price{$key}"] * $table[$key];
                
                // データ置換
                $row = str_replace("++[lst_id]", $id, $row);
                $row = str_replace("++[lst_path]", $path, $row);
                $row = str_replace("++[lst_dishname]", $this->ResWord["dish{$key}"], $row);
                $row = str_replace("++[lst_price_disp]", number_format($price), $row);
                $row = str_replace("++[lst_detail]", $detail, $row);
                $row = str_replace("++[lst_qty]", $table[$key], $row);
                
                if($count == 0)
                {
                    $title = "ご注文内容";
                }
                
                $row = str_replace("++[lst_title]", $title, $row);
                
                // データを溜め込む
                $rows .= $row;
                
                $total_price = $total_price + $price;
                $count = $count + 1;
            }
        }

        // データ置換(リスト)
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        // 合計金額もここで置換
        $contents = str_replace("++[lcl_total_price]", number_format($total_price), $contents);
        $contents = str_replace("++[lcl_total_taxprice]", number_format($total_price * self::TAX), $contents);
        
        // メール送信用に値を保持
        $_SESSION["i"]["total_price"] = number_format($total_price);
        $_SESSION["i"]["total_taxprice"] = number_format($total_price * self::TAX);
        
        return $contents;
    }
    
    // 入力チェック
    private function InputCheck()
    {
        $retflg = true;
        
        $objIcheck = new CLS_InputChecker();
        
        $qtys   = $_SESSION["i"]["dish_qtys"];
        
        // 注文数の配列の内容でループ
        foreach($qtys as $key => $val)
        {
            // 入力数
            do
            {
                $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::NUMBER), $qtys[$key], $this->ResDigits["order_qty"]);
                if ($ret != 0)
                {
                    //エラーなら抜ける
                    // 入力チェックでエラーが発生している場合は同一画面に戻す
                    $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["dish{$key}"]));
                    $retflg = false;
                    break;
                }
                
                // 全てOKなら金額チェック
                if($_SESSION["i"]["total_price"] < 10000)
                {
                    // 入力チェックでエラーが発生している場合は同一画面に戻す
                    $this->SetDispMesWarning($this->GetResMessage("price_err"));
                    $retflg = false;
                    break;
                }
                
            }while(0);
        }
        
        
        
        return $retflg;
    }
    // お客様情報の入力チェック
    private function InputCheckCustomer()
    {
        $retflg = true;
        
        $objIcheck = new CLS_InputChecker();
        
        $name  = $_SESSION["i"]["name"];
        $kana  = $_SESSION["i"]["kana"];
        $zipcode  = $_SESSION["i"]["zipcode"];
        $address  = $_SESSION["i"]["address"];
        $tel  = $_SESSION["i"]["tel"];
        $delivery_date  = $_SESSION["i"]["delivery_date"];
        $delivery_time  = $_SESSION["i"]["delivery_time"];
        $mail  = $_SESSION["i"]["mail"];
        $note  = $_SESSION["i"]["note"];
        
        // 日時を漢数字フォーマット化
        $dates = explode("-" , $_SESSION["i"]["delivery_date"]);
        $times = explode(":" , $_SESSION["i"]["delivery_time"]);
        
        $date = sprintf("%s%s%s", $dates[0], $dates[1], $dates[2]);
        $time = sprintf("%s%s", $times[0], $times[1]);
        
        // 日時を漢数字フォーマット化
        
        //　同一ページ戻しでエラーメッセージを表示するため、ひとつでもエラーが発生したら処理を抜ける
        do
        {
            // お客様名
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED), $name, $this->ResDigits["name"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["name"]));
                $retflg = false;
                break;
            }
            
        
        
            // かな

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED), $kana, $this->ResDigits["kana"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["kana"]));
                $retflg = false;
                break;
            }
            
            // メールアドレスのチェック（必須、桁数）

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED,CLS_InputChecker::DIGITS, CLS_InputChecker::MAILADDRESS),$mail,$this->ResDigits["mail"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["mail"]));
                $retflg = false;
                break;
            }
            
            // 郵便番号

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED, CLS_InputChecker::NUMBER), $zipcode, $this->ResDigits["zipcode"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["zipcode"]));
                $retflg = false;
                break;
            }
            
            // 住所

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED), $address, $this->ResDigits["address"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["address"]));
                $retflg = false;
                break;
            }
            
            
            // 日時のチェック 
            if ($date < date("Y-m-d", strtotime("+2 day", time())) || $date > date("Y-m-d", strtotime("+2 month", time())))
            {
                $this->SetDispMesWarning($this->GetResMessage("date_err"));
                $retflg = false;
                break;
            }
            
            if ($time < "0800" ||  $time > "1800" )
            {
                $this->SetDispMesWarning($this->GetResMessage("time_err"));
                $retflg = false;
                break;
            }
            
            // tel

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS, CLS_InputChecker::REQUIRED, CLS_InputChecker::NUMBER), $tel, $this->ResDigits["tel"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["tel"]));
                $retflg = false;
                break;
            }
        
            // お客様名

            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::DIGITS), $name, $this->ResDigits["note"]);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $this->SetDispMesWarning($this->GetResMessage("input_checker{$ret}", $this->ResWord["note"]));
                $retflg = false;
                break;
            }
        
        }while(0);
        
        return $retflg;
    }
}
