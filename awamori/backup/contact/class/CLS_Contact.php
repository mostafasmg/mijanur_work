<?php
require_once("Ex/CLS_Page_Ex.php");

require_once("CLS_InputChecker.php");
require_once("CLS_MailContact.php");
require_once("CLS_MailContactUser.php");

// お問い合わせページクラス
class CLS_Contact extends CLS_Page_Ex
{
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    
    // コンストラクタ
    public function __construct()
    {
        parent::__construct(0);
        
        // 本クラスはmanagementディレクトリより上位の階層から呼び出されるので
        // dir_offsetの更新
        //$this->dir_offset = "{$this->dir_offset}contact/";
    }
    // input.php
    public function input()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("input.html");
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "input");
        
        echo $contents;
    }
    
    // check.php
    public function check()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("check.html");
        
        // セッション変数にフォームから取得した値をセットする
        $this->SetSessionVariableForm();
        
        $ret = $this->InputCheck();
        
        if (!$ret)
        {
            // 入力チェックでエラーが発生している場合はsubmitボタンを非表示にする
            $contents = $this->PregMatchAreaHidden($contents, "SUBMIT");
        }
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "check");
        
        echo $contents;
         
         
    }
    
    // send.php
    public function send()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("end.html");
        
        // セッションが無かったら不正扱いでお問い合わせページへ戻す
        if(strlen($_SESSION["i"]["name"]) < 1)
        {
            unset($_SESSION["i"]);
            
            header("LOCATION: ./input.php" );
            exit();
        }
        
        $name     = $_SESSION["i"]["name"];
        $sex     = $_SESSION["i"]["sex"];
        $prefectures     = $_SESSION["i"]["prefectures"];
        $age   = $_SESSION["i"]["age"];
        $tel      = $_SESSION["i"]["tel"];
        $ticket_qty  = $_SESSION["i"]["ticket_qty"];
        $ticket_qty2  = $_SESSION["i"]["ticket_qty2"];
        $mail     = $_SESSION["i"]["mail"];
        $detail   = $_SESSION["i"]["detail"];
        
        $sex_disp = $this->ResWord["sex_{$sex}"];
        $prefectures_disp = $this->ResWord["prefectures_{$prefectures}"];
        $age_disp = $this->ResWord["age_{$age}"];
        
        // 管理側へ登録メール送信
        $objMailContact = new CLS_MailContact($this->dir_offset);
        
        // メッセージ動的部分の作成
        $objMailContact->createMessage( $name,  $sex_disp, $age_disp, $prefectures_disp,  $tel, $mail, $ticket_qty, $ticket_qty2, $detail);
        
        $mailto = CLS_MailContact::TOADDRESS;
        
        // メール送信
        $objMailContact->sendmail($mailto);
        
        // ユーザーへもメール送信
        $objMailContactUser = new CLS_MailContactUser($this->dir_offset);
        
        // メッセージ動的部分の作成
        $objMailContactUser->createMessage( $name,  $sex_disp, $age_disp, $prefectures_disp,  $tel, $mail, $ticket_qty, $ticket_qty2,  $detail);
        
        // メール送信
        $objMailContactUser->sendmail($mail);
        
        unset($_SESSION["i"]);
        
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
        }
        
        // input
        if ($page == "input")
        {
            $contents = str_replace("++[lcl_name]", $_SESSION["i"]["name"], $contents);
            $contents = str_replace("++[lcl_sex]", $_SESSION["i"]["sex"], $contents);
            $contents = str_replace("++[lcl_company]", $_SESSION["i"]["company"], $contents);
            $contents = str_replace("++[lcl_age]", $_SESSION["i"]["age"], $contents);
            $contents = str_replace("++[lcl_prefectures]", $_SESSION["i"]["prefectures"], $contents);
            $contents = str_replace("++[lcl_ticket_qty]", $_SESSION["i"]["ticket_qty"], $contents);
            $contents = str_replace("++[lcl_ticket_qty2]", $_SESSION["i"]["ticket_qty2"], $contents);
            $contents = str_replace("++[lcl_tel]", $_SESSION["i"]["tel"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail"], $contents);
            $contents = str_replace("++[lcl_detail]", $_SESSION["i"]["detail"], $contents);
        }
        
        // check
        if ($page == "check")
        {
            $contents = str_replace("++[lcl_name]", $_SESSION["i"]["name_disp"], $contents);
            $contents = str_replace("++[lcl_sex]", $_SESSION["i"]["sex_disp"], $contents);
            $contents = str_replace("++[lcl_company]", $_SESSION["i"]["company"], $contents);
            $contents = str_replace("++[lcl_age]", $_SESSION["i"]["age_disp"], $contents);
            $contents = str_replace("++[lcl_prefectures]", $_SESSION["i"]["prefectures_disp"], $contents);
            $contents = str_replace("++[lcl_ticket_qty]", $_SESSION["i"]["ticket_qty_disp"], $contents);
            $contents = str_replace("++[lcl_ticket_qty2]", $_SESSION["i"]["ticket_qty2_disp"], $contents);
            $contents = str_replace("++[lcl_tel]", $_SESSION["i"]["tel_disp"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail_disp"], $contents);
            $contents = str_replace("++[lcl_detail]", $_SESSION["i"]["detail_disp"], $contents);
        }
        return $contents;
    }
    
    // セッション変数にフォームから取得した値をセットする
    private function SetSessionVariableForm()
    {
        $_SESSION["i"]["name"] = $this->post["name"];
        $_SESSION["i"]["sex"] = $this->post["sex"];
        $_SESSION["i"]["age"] = $this->post["age"];
        $_SESSION["i"]["prefectures"] = $this->post["prefectures"];
        $_SESSION["i"]["tel"] = $this->post["tel"];
        $_SESSION["i"]["mail"] = $this->post["mail"];
        $_SESSION["i"]["ticket_qty"] = $this->post["ticket_qty"];
        $_SESSION["i"]["ticket_qty2"] = $this->post["ticket_qty2"];
        $_SESSION["i"]["detail"] = $this->post["detail"];
        
        if(strlen($_SESSION["i"]["ticket_qty"]) == 0)
        {
            $_SESSION["i"]["ticket_qty"] = 0;
        }
        
        if(strlen($_SESSION["i"]["ticket_qty2"]) == 0)
        {
            $_SESSION["i"]["ticket_qty2"] = 0;
        }
        
    }
    
    // 入力チェック
    private function InputCheck()
    {
        $retflg = true;
        
        $objIcheck = new CLS_InputChecker();
        
        
        $name     = $_SESSION["i"]["name"];
        $sex     = $_SESSION["i"]["sex"];
        $prefectures     = $_SESSION["i"]["prefectures"];
        $age   = $_SESSION["i"]["age"];
        $tel      = $_SESSION["i"]["tel"];
        $ticket_qty  = $_SESSION["i"]["ticket_qty"];
        $ticket_qty2  = $_SESSION["i"]["ticket_qty2"];
        $mail     = $_SESSION["i"]["mail"];
        $detail   = $_SESSION["i"]["detail"];
        

        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $sex);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["sex_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["sex"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["sex_disp"] = $this->ResWord["sex_{$sex}"];
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $prefectures);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["prefectures_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["prefectures"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["prefectures_disp"] = $this->ResWord["prefectures_{$prefectures}"];
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $name);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["name_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["name"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["name_disp"] = $name;
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $age);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["age_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["age"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["age_disp"] = $this->ResWord["age_{$age}"];
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED, CLS_InputChecker::NUMBER), $tel);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["tel_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["tel"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["tel_disp"] = $tel;
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::NUMBER), $ticket_qty);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["ticket_qty_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["ticket_qty"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["ticket_qty_disp"] = "2枚綴り(1,500円) " . $ticket_qty . "枚";
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::NUMBER), $ticket_qty2);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["ticket_qty2_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["ticket_qty"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["ticket_qty2_disp"] = "3枚綴り(2,100円) " . $ticket_qty2 . "枚";
        }while(0);
        
        if($ticket_qty <= 0 && $ticket_qty2 <= 0)
        {
            //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["ticket_qty_disp"] = $this->SetErrTextTagP($this->GetResMessage("qty_err1"));
                $_SESSION["i"]["ticket_qty2_disp"] = "";
                $retflg = false;
        }
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $mail);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["mail_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["mail"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["mail_disp"] = $mail;
        }while(0);
        
        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $detail);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["detail_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["detail"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["detail_disp"] = $detail;
        }while(0);
        

        return $retflg;
    }
}
