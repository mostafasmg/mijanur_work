<?php
session_start();
require_once(dirname(__FILE__) ."/../../define.php");

/********************************************************************/
/* Ver.20151222                                                     */
/* Ver.20180523                                                     */
/*   １．$Lang変数を参照できていない（バグ）                        */
/*   ２．チェックボックスの連想配列対応                             */
/*   ３．スクロールリストテンプレートのdir_offset対応               */
/* Ver.20190212                                                     */
/*   １．ユーザーエージェント判断等最新版反映                       */
/* Ver.20190617                                                     */
/*    DateTimeクラスを採用。PHP5.2以下で動作不可能。                */
/* Ver.20190725                                                     */
/*    モバイル対応の処理を変更                                      */
/* ---------------------------------------------------------------- */
/* ◆ ◆ ◆ 編 集 禁 止 ◆ ◆ ◆                                    */
/* 編集する場合は上長の承認が必要です。                             */
/* ---------------------------------------------------------------- */
/* This file is not editable.                                       */
/********************************************************************/
// ページクラス(Base)
class CLS_Page_Ex
{
    // リクエストパラメーター
    protected $post = array();
    protected $get = array();
    
    // システムパラメーター
    protected $dir_offset;
    
    // リソース関連
    protected $ResMessage = array();
    protected $ResWord = array();
    protected $ResDigits = array();
    
    // 言語フラグ
    protected $Lang;
    
    // モベイルフラグ(Ver.20190725 UPDATE)
    protected $Mobile;
    
    // エラーテキスト表示フォーマット
    private $ErrTextFormat;
    
    // リンクタグ設定フォーマット
    private $LinkTagFormat;
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($needlogin = 1)
    {
        // 相対パスの階層を分析
        $this->dir_offset = $this->get_rootpath_period($_SERVER["PHP_SELF"]);
        
        // ログインが必要なページか判断
        if ($needlogin == 1)
        {
            // ログインしていなければトップページへ強制転送
            if (strlen($_SESSION["l"]["key"]) == 0)
            {
                header("Location: {$this->dir_offset}");
                exit();
            }
        }
        
        // POST,GETパラメータをローカル変数にセット
        unset($this->post);
        unset($this->get);
        
        foreach($_POST as $key => $val)
        {
            // チェックボックス対応
            if (is_array($_POST[$key]))
            {
                foreach($_POST[$key] as $key2 => $val2)
                {
                    $this->post[$key][$key2] = htmlspecialchars($val2);
                }
            }
            else
            {
                $this->post[$key] = htmlspecialchars($val);
            }
        }
        
        foreach($_GET as $key => $val)
        {
            $this->get[$key] = htmlspecialchars($val);
        }
        
        // メッセージリソースの展開
        $this->ExpansionMessageResource();
        
        // ワードリソースの展開
        $this->ExpansionWordResource();
        
        // 桁数リソースの展開
        $this->ExpansionDigitsResource();
        
        // エラー表示フォーマットの読込み
        $this->ReadErrStyleFile();
        
        // リンクタグフォーマットの読込み
        $this->ReadLinkStyleFile();
        
        // モバイル用フラグ処理(Ver.20190725 UPDATE)
        if ($this->isUserAgentMobile())
        {
            // モバイルテンプレート用にフラグをオン
            $this->Mobile = "_mobile/";
        }
    }
    
    // tooltip.php
    public function tooltip()
    {
        $template = "{$this->dir_offset}template/tpl/tags/tooltip.tpl";
        $tooltip_tag = file_get_contents($template);
        
        $index = $this->post["tooltip_no"];
        
        // タイトルとデータを置換
        $tooltip_tag = str_replace("++[lcl_tabledata]", $_SESSION["tooltip"][$index], $tooltip_tag);
        
        // 結果を出力
        echo $tooltip_tag;
    }
    
    /********************************************************************/
    /* Protected Method                                                 */
    /********************************************************************/
    // ページのシステム変数の置換
    protected function replace_template_sys($temp_path, $temp_num = "")
    {
        // (Ver.20190725 UPDATE)
        // ファイル読込み時にモバイルを考慮するように改造
        // モバイル用ファイルが存在しない場合はPC版ファイルを読み直す
        // ===========================================================
        
        // テンプレートファイル読み込み
        $template = "{$this->dir_offset}template/{$this->Mobile}{$temp_path}";
        $contents = @file_get_contents($template);
        if ($contents === false)
        {
            // PCテンプレートを読み直し
            $template = "{$this->dir_offset}template/{$temp_path}";
            $contents = file_get_contents($template);
        }
        
        // テンプレートファイル読み込み(ヘッダータグ)
        $template = "{$this->dir_offset}template/{$this->Mobile}tpl/headtag.tpl";
        $headtag = @file_get_contents($template);
        if ($headtag === false)
        {
            // PCテンプレートを読み直し
            $template = "{$this->dir_offset}template/tpl/headtag.tpl";
            $headtag = file_get_contents($template);
        }
        
        // テンプレートファイル読み込み(ヘッダー)
        $template = "{$this->dir_offset}template/{$this->Mobile}tpl/header{$temp_num}.tpl";
        $header = @file_get_contents($template);
        if ($headtag === false)
        {
            // PCテンプレートを読み直し
            $template = "{$this->dir_offset}template/tpl/header{$temp_num}.tpl";
            $header = file_get_contents($template);
        }
        
        // テンプレートファイル読み込み(フッター)
        $template = "{$this->dir_offset}template/{$this->Mobile}tpl/footer.tpl";
        $footer = @file_get_contents($template);
        if ($footer === false)
        {
            // PCテンプレートを読み直し
            $template = "{$this->dir_offset}template/tpl/footer.tpl";
            $footer = file_get_contents($template);
        }
        
        // データの置換(ヘッダータグ)
        $block_start = "<!-- HEADTAG_START -->";
        $block_end   = "<!-- HEADTAG_END -->";
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $headtag, $contents);
        
        // データの置換(ヘッダー)
        $block_start = "<!-- HEADER_START -->";
        $block_end   = "<!-- HEADER_END -->";
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $header, $contents);
        
        // データの置換(フッター)
        $block_start = "<!-- FOOTER_START -->";
        $block_end   = "<!-- FOOTER_END -->";
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $footer, $contents);
        
        // ページタイトルの置換
        $contents = str_replace("++[sys_titletag]", $this->ResWord["titletag"], $contents);
        
        // ログインユーザーの置換
        if (isset($_SESSION["l"]))
        {
            $contents = str_replace("++[sys_loginname]",   $_SESSION["l"]["name"], $contents);
        }
        
        // システム変数の置換
        $contents = str_replace("++[sys_systemroot]", $this->dir_offset, $contents);
        
// 2019.07 S:Del 現バージョンで未使用のため廃止
//        // 共通ワードリソースの置換
//        $contents = str_replace("++[wrd_header_logout]", $this->ResWord["header_logout"], $contents);
//        $contents = str_replace("++[wrd_header_home]", $this->ResWord["header_home"], $contents);
//        $contents = str_replace("++[wrd_header_systemname]", $this->ResWord["header_systemname"], $contents);
//        $contents = str_replace("++[wrd_header_company_name]", $this->ResWord["header_company_name"], $contents);
//        $contents = str_replace("++[wrd_footer_text]", $this->ResWord["footer_text"], $contents);
//        
//        // 共通画面メッセージリソースの置換
//        $contents = str_replace("++[mes_dispmes00001]", $this->GetResMessage("DISPLAY_MESSAGE_00001", "xxxxx"), $contents);
// 2019.07 E:Del 現バージョンで未使用のため廃止
        
        // デバッグフラグ関連の置換
        $debugmode = "";
        if (DEBUG_FLG == 1)
        {
            $debugmode = "(DEBUG MODE) ";
        }
        $contents = str_replace("++[sys_debugmode]", $debugmode, $contents);
        
        // カテゴリタブの置換
        $contents = $this->CustomTabReplace($contents);
        
        return $contents;
    }
    
    // スクロールリストを置換する
    protected function replace_template_scrolllist($contents, $sclistname)
    {
        // (Ver.20190725 UPDATE)
        // ファイル読込み時にモバイルを考慮するように改造
        // モバイル用ファイルが存在しない場合はPC版ファイルを読み直す
        // ===========================================================
        
        // テンプレートファイル読み込み
        $sclist_path = "{$this->dir_offset}template/{$this->Mobile}tpl/sclist/{$sclistname}.tpl";
        $scrolllist = @file_get_contents($sclist_path);
        if ($scrolllist === false)
        {
            // PCテンプレートを読み直し
            $sclist_path = "{$this->dir_offset}template/tpl/sclist/{$sclistname}.tpl";
            $scrolllist = file_get_contents($sclist_path);
        }
        
        // システム変数の置換
        $scrolllist = str_replace("++[sys_systemroot]", $this->dir_offset, $scrolllist);
        
        // コンテンツにスクロールリストテンプレートの置換
        $contents = str_replace("++[scl_{$sclistname}]", $scrolllist, $contents);
        
        return $contents;
    }
    
    // メッセージリソースからメッセージの取得
    protected function GetResMessage($key, $arg1 = "", $arg2 = "", $arg3 = "")
    {
        $message = $this->ResMessage[$key];
        
        if (strlen($arg1) > 0)
        {
            $message = str_replace("{1}", $arg1, $message);
        }
        
        if (strlen($arg2) > 0)
        {
            $message = str_replace("{2}", $arg2, $message);
        }
        
        if (strlen($arg3) > 0)
        {
            $message = str_replace("{3}", $arg3, $message);
        }
        
        return $message;
    }
    
    // 日付コントロールを表示
    protected function show_public_date_input($index, $showdate, $format="ymd") 
    {
        $year = "";
        $month = "";
        $day = "";
        if (strlen($showdate) > 0)
        {
            $date = explode("-", $showdate);
            $year = $date[0];
            $month = $date[1];
            $day = $date[2];
        }
        
        $count = strlen($format) - 1;
        $selectbox = "";
        if (false!==strpos($format, "y"))
        {
// 2018/05/14 S: 年月日に応じたクラスを渡して幅調整するように変更
            $classname = "select_year";
            $selectbox .= $this->show_select_box("year" . $index, "y", $year, $classname);
            //$selectbox .= $this->show_select_box("year" . $index, "y", $year);
// 2018/05/14 E: 年月日に応じたクラスを渡して幅調整するように変更
            if ($count > 0)
            {
                $count--;
                $selectbox .= "&nbsp;／&nbsp;";
            }
        }
        if (false!==strpos($format, "m"))
        {
// 2018/05/14 S: 年月日に応じたクラスを渡して幅調整するように変更
            $classname = "select_month";
            $selectbox .= $this->show_select_box("month" . $index, "m", $month, $classname);
            //$selectbox .= $this->show_select_box("month" . $index, "m", $month);
// 2018/05/14 E: 年月日に応じたクラスを渡して幅調整するように変更
            if ($count > 0)
            {
                $count--;
                $selectbox .= "&nbsp;／&nbsp;";
            }
        }
        if (false!==strpos($format, "d"))
        {
// 2018/05/14 S: 年月日に応じたクラスを渡して幅調整するように変更
            $classname = "select_day";
            $selectbox .= $this->show_select_box("day" . $index, "d", $day, $classname);
            //$selectbox .= $this->show_select_box("day" . $index, "d", $day);
// 2018/05/14 E: 年月日に応じたクラスを渡して幅調整するように変更
        }
        
        return $selectbox;
    }
    
    // 日付コントロールを表示
    protected function show_public_time_input($index, $showtime) 
    {
        $hour = "";
        $minutes = "";
        $second = "";
        if (strlen($showtime) > 0)
        {
            $time = explode(":", $showtime);
            $hour = $time[0];
            $minutes = $time[1];
            $second = $time[2];
        }
        
        $selectbox = "";
        $selectbox .= $this->show_select_box("hour" . $index, "h", $hour, "select_hour");
        $selectbox .= "&nbsp;：&nbsp;";
        $selectbox .= $this->show_select_box("minutes" . $index, "i", $minutes, "select_minutes");
        $selectbox .= "&nbsp;：&nbsp;";
        $selectbox .= $this->show_select_box("second" . $index, "s", $second, "select_seconds");
        return $selectbox;
    }
    
    // エラーテキストをフォーマット化して返す(Pタグ)
    protected function SetErrTextTagP($errtext)
    {
        $formatmes = str_replace("++[sys_errtext]", $errtext, $this->ErrTextFormat);
        return $formatmes;
    }
    
    // リンクタグをフォーマット化して返す(Aタグ)
    protected function SetLinkTagA($href, $target, $text)
    {
        $formattag = $this->LinkTagFormat;
        $formattag = str_replace("++[sys_href]", $href, $formattag);
        $formattag = str_replace("++[sys_target]", $target, $formattag);
        $formattag = str_replace("++[sys_text]", $text, $formattag);
        
        return $formattag;
    }
    
    // 範囲置換変数を空文字に置き換えてエリアを非表示にする
    protected function PregMatchAreaHidden($contents, $areaname)
    {
        // 置換エリアの取得
        $block_start = "<!-- {$areaname}_START -->";
        $block_end   = "<!-- {$areaname}_END -->";
        
        // データの置換
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", "", $contents);
        
        return $contents;
    }
    
    // エラーオブジェクトをセットする
    protected function SetDispMesError($ex)
    {
        $_SESSION["dispmes"]["dispflg"] = true;
        $_SESSION["dispmes"]["dispkbn"] = "error";
        
        // 発生したExceptionによってメッセージの取得先が異なる
        if (isset($ex->errorInfo))
        {
            // PDOException
            $_SESSION["dispmes"]["message"] = $ex->getMessage();
//            $_SESSION["dispmes"]["message"] = $ex->errorInfo[2];
            $_SESSION["dispmes"]["err_info"] = $ex->errorInfo;
        }
        else
        {
            // Exception
            $_SESSION["dispmes"]["message"] = sprintf("%s:%s", $ex->getCode(), $ex->getMessage());
        }
        
    }
    
    // 警告オブジェクトをセットする
    protected function SetDispMesWarning($message)
    {
        $_SESSION["dispmes"]["dispflg"] = true;
        $_SESSION["dispmes"]["dispkbn"] = "error";
        $_SESSION["dispmes"]["message"] = $message;
    }
    
    // インフォオブジェクトをセットする
    protected function SetDispMesInfo($message)
    {
        $_SESSION["dispmes"]["dispflg"] = true;
        $_SESSION["dispmes"]["dispkbn"] = "info";
        $_SESSION["dispmes"]["message"] = $message;
    }
    
    // メッセージエリアの表示
    protected function ShowDisplayMessage($contents)
    {
        // 表示が必要なければ抜ける
        if (!isset($_SESSION["dispmes"]["dispflg"]))
        {
            return $contents;
        }
        if ($_SESSION["dispmes"]["dispflg"] != true)
        {
            return $contents;
        }
        
        $message = $_SESSION["dispmes"]["message"];
        $contents = $this->SetDisplayMessage($contents, $message, $_SESSION["dispmes"]["dispkbn"]);
        
        // エラーオブジェクトの初期化
        unset($_SESSION["dispmes"]);
        
        return $contents;
    }
    
    // 子ウィンドウの終了
    protected function CloseChildWindow($href="")
    {
        $contents = $this->replace_template_sys("close_child.html");
        
        // 親ウィンドウ転送先の指定
        $contents = str_replace("++[lcl_href]", $href, $contents);
        
        echo $contents;
        exit();
    }
    
    // ユーザーエージェント判断
    protected function isUserAgentMobile()
    {
        $mobileflg = false;
        
        $ua = $_SERVER["HTTP_USER_AGENT"];
        if((strpos($ua, "iPhone") !== false)||((strpos($ua, "Android") !== false) && (strpos($ua, "Mobile") !== false)))
        {
            // モバイル
            $mobileflg = true;
        }
        
        return $mobileflg;
    }
    
// 2019.05.30 S:ADD HEIJI ツールチップの処理を追加
    // ツールチップデータの初期化
    protected function InitializeToolTip()
    {
        unset($_SESSION["tooltip"]);
    }
    
    // ツールチップデータをセットする
    protected function appendTooltipData($index, $title, $data)
    {
        $template = "{$this->dir_offset}template/tpl/tags/tooltip_details.tpl";
        $tooltip_tag = file_get_contents($template);
        
        // タイトルとデータを置換
        $tooltip_tag = str_replace("++[lcl_title]", $title, $tooltip_tag);
        $tooltip_tag = str_replace("++[lcl_data]", $data, $tooltip_tag);
        
        // データを蓄積
        $_SESSION["tooltip"][$index] .= $tooltip_tag;
    }
// 2019.05.30 E:ADD HEIJI ツールチップの処理を追加
    
// 2019.06.17 S:ADD HEIJI 暫定的に追加、日付を日本語フォーマット化して返す
    // 日付を日本語フォーマット化して返す
    protected function getDateJapanFormat($date, $option = false, $delimiter = "-")
    {
        // 編集用日付のセット
        $date_work = $date;
        
        // 時間付きなら日付だけ取り出す
        $datetimes = explode(" ", $date);
        $count = count($datetimes);
        if ($count >= 1)
        {
            $date_work = $datetimes[0];
        }
        
        // 日付型かチェックする
        $date_format = sprintf("Y%sm%sd", $delimiter, $delimiter);
        $tempDt = DateTime::createFromFormat($date_format, $date_work);
        if ($tempDt === false)
        {
            // 日付として不成立(文字列など日付フォーマットへの変換NG)
            header("Content-Type: text/html;charset=UTF-8");
            throw new Exception("日付ではない値がセットされました。(CLS_Page_Ex->getDateJapanFormat)", -8930);
            exit();
        }
        
        // 日付の整合性チェック(2月31日など)
        if ($tempDt->format($date_format) !== $date_work)
        {
            // 日付として不成立(存在しない日付)
            header("Content-Type: text/html;charset=UTF-8");
            throw new Exception("日付として成立しない値がセットされました。(CLS_Page_Ex->getDateJapanFormat)", -8930);
            exit();
        }
        
        $temp_dates = explode($delimiter, $date_work);
        $japan_format_date = sprintf("%s年%s月%s日", $temp_dates[0], $temp_dates[1], $temp_dates[2]);
        
        // 曜日を付与するか？
        if ($option)
        {
            $dayofweeks = array("日","月","火","水","木","金","土");
            $japan_format_date .= sprintf(" (%s)", $dayofweeks[$tempDt->format("w")]);
        }
        
        return $japan_format_date;
    }
// 2019.06.17 E:ADD HEIJI 暫定的に追加、日付を日本語フォーマット化して返す
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
    // 階層を判断してシステムルートへの相対パスを返す
    private function get_rootpath_period($php_self)
    {
        $split = explode("/", $php_self);
        $count = count($split);
        
        $retstr = "";
        for($i=1; $i<($count - DIRECTORY_LEVEL); $i++)
        {
            $retstr .= "../";
        }
        
        return $retstr;
    }
    
    // メッセージリソースの展開
    private function ExpansionMessageResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/message.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $this->Lang)
                {
                    $index = $i;
                }
            }
            
            // コメント文字対応
            if (strlen($line) > 0 && substr($line, 0, 1) != "#")
            {
                $this->ResMessage[$ex_line[0]] = $ex_line[$index];
            }
        }
        
        fclose($fp);
    }
    
    // ワードリソースの展開
    private function ExpansionWordResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/word.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $this->Lang)
                {
                    $index = $i;
                }
            }
            
            // コメント文字対応
            if (strlen($line) > 0 && substr($line, 0, 1) != "#")
            {
                $this->ResWord[$ex_line[0]] = $ex_line[$index];
            }
        }
        
        fclose($fp);
    }
    
    // 桁数リソースの展開
    private function ExpansionDigitsResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/digits.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $this->Lang)
                {
                    $index = $i;
                }
            }
            
            // コメント文字対応
            if (strlen($line) > 0 && substr($line, 0, 1) != "#")
            {
                $this->ResDigits[$ex_line[0]] = $ex_line[$index];
            }
        }
        
        fclose($fp);
    }
    
    // セレクトボックス
    private function show_select_box($name, $type, $default, $classname = "") 
    {
        $aaa = "";
        $aaa .= "<select name='{$name}' id='{$name}' class='{$classname}'>";
        
        if ($type == "y")
        {
            //$min = 2010;
            $min = 1930;
            $max = 2030;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = date("Y");
            }
        }
        else if ($type == "m")
        {
            $min = 1;
            $max = 12;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = date("m");
            }
        }
        else if ($type == "d")
        {
            $min = 1;
            $max = 31;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = date("d");
            }
        }
        else if ($type == "h")
        {
            $min = 0;
            $max = 23;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = date("H");
            }
        }
        else if ($type == "i")
        {
            $min = 0;
            $max = 59;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = date("i");
            }
        }
        else if ($type == "s")
        {
            $min = 0;
            $max = 59;
            
            $now = $default;
            if (strlen($now) == 0)
            {
                $now = 0;
            }
        }
        for($i = $min; $i <= $max; $i ++)
        {
            if ($i == $now)
            {
                $nowflg = "selected";
            }
            else
            {
                $nowflg = "";
            }
            $aaa .= "<option $nowflg value='{$i}'>{$i}</option>";
        }
        
        $aaa .= "</select>";
        
        return $aaa;
    }
    
    // エラースタイルの読込み
    private function ReadErrStyleFile()
    {
        $template = dirname(__FILE__) . "/../../template/tpl/tags/errstyle_p.tpl";
        $this->ErrTextFormat = file_get_contents($template);
    }
    
    // リンクスタイルの読込み
    private function ReadLinkStyleFile()
    {
        $template = dirname(__FILE__) . "/../../template/tpl/tags/linkstyle_a.tpl";
        $this->LinkTagFormat = file_get_contents($template);
    }
    
    // カテゴリタブの置換
    private function CustomTabReplace($contents)
    {
        $block_start = "<!-- CATEGORYTAB_START -->";
        $block_end   = "<!-- CATEGORYTAB_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
// 2019.07 S:Add エラー表示厳格化に伴いカテゴリタブが不要ならファンクションを即抜けする
        if (count($matches) == 0)
        {
            return $contents;
        }
// 2019.07 E:Add エラー表示厳格化に伴いカテゴリタブが不要ならファンクションを即抜けする
        $template = ltrim($matches[1], "\r\n");
        
        $rows = "";
        $count = count($_SESSION["l"]["customtab"]);
        for ($i = 0; $i < $count; $i++)
        {
            $tabtitle = $_SESSION["l"]["customtab"][$i]["dispname"];
            $catid = $_SESSION["l"]["customtab"][$i]["categoryid"];
            
            $row = $template;
            $row = str_replace("++[sys_catid]", $catid, $row);
            $row = str_replace("++[sys_tabtitle]", $tabtitle, $row);
            
            // データを溜め込む
            $rows .= $row;
        }
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $contents);
        
        return $contents;
    }
    
    // メッセージエリアを置換する
    private function SetDisplayMessage($contents, $message, $areaname)
    {
        // テンプレートファイル読み込み(メッセージエリア)
        $template = "{$this->dir_offset}template/tpl/tags/message_area_{$areaname}.tpl";
        $message_area = file_get_contents($template);
        
        // メッセージの置換
        $message_area = str_replace("++[lcl_message]", $message, $message_area);
        
        // データの置換(メッセージエリア)
        $block_start = "<!-- MESSAGEAREA_START -->";
        $block_end   = "<!-- MESSAGEAREA_END -->";
        $contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $message_area, $contents);
        
        return $contents;
    }
}
