<?php
require_once("Ex/CLS_Page_Ex.php");

require_once("CLS_InputChecker.php");
require_once("CLS_MailContact.php");

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
        $this->dir_offset = "{$this->dir_offset}management/";
    }
    // index.php
    public function index()
    {
        
        echo $contents;
    }
    
   
    // check.php
    public function check()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("online/contact/check.html");
        
        // セッション変数にフォームから取得した値をセットする
        $this->SetSessionVariableForm();
        
        $ret = $this->InputCheck();
        
        if (!$ret)
        {
            // 入力チェックでエラーが発生している場合はsubmitボタンを非表示にする
            $contents = $this->PregMatchAreaHidden($contents, "SUBMIT");
        }
        
        
//        // 登録メール送信
//        $objMailContact = new CLS_MailContact($this->dir_offset);
//        
//        $gender = $_SESSION["i"]["gender"];
//        $name = $_SESSION["i"]["name"];
//        $kana = $_SESSION["i"]["kana"];
//        $company = $_SESSION["i"]["company"];
//        $department = $_SESSION["i"]["department"];
//        $official_position = $_SESSION["i"]["official_position"];
//        $tel = $_SESSION["i"]["tel"];
//        $mail = $_SESSION["i"]["mail"];
//        $address = $_SESSION["i"]["address"];
//        $detail = $_SESSION["i"]["detail"];
//        
//        // メッセージ動的部分の作成
//        $objMailContact->createMessage($gender, $name,  $kana, $company, $department, $official_position, $tel, $mail, $address, $detail);
//        
//        $mail = $objMailContact::TOADDRESS;
        
        // メール送信
//        $objMailContact->sendmail($mail);
        
        // ローカル変数の置換
        $contents = $this->SetLclInputVariable($contents, "check");
        
        echo $contents;
         
         
    }
    
    // send.php
    public function send()
    {
        
        // 登録メール送信
        $objMailContact = new CLS_MailContact($this->dir_offset);
        
        $gender = $_SESSION["i"]["gender"];
        $name = $_SESSION["i"]["name"];
        $kana = $_SESSION["i"]["kana"];
        $company = $_SESSION["i"]["company"];
        $department = $_SESSION["i"]["department"];
        $official_position = $_SESSION["i"]["official_position"];
        $tel = $_SESSION["i"]["tel"];
        $mail = $_SESSION["i"]["mail"];
        $address = $_SESSION["i"]["address"];
        $detail = $_SESSION["i"]["detail"];
        
        // メッセージ動的部分の作成
        $objMailContact->createMessage($gender, $name,  $kana, $company, $department, $official_position, $tel, $mail, $address, $detail);
        
        $mail = $objMailContact::TOADDRESS;
        
        // メール送信
        $objMailContact->sendmail($mail);
        
        
        unset($_SESSION["i"]);
        
        header("LOCATION: ./thankyou.html" );
        exit();
         
         
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
        }
        
        // check
        if ($page == "send_check")
        {
        }
        
        // check
        if ($page == "check")
        {
            $contents = str_replace("++[lcl_gender]", $_SESSION["i"]["gender_disp"], $contents);
            $contents = str_replace("++[lcl_name]", $_SESSION["i"]["name_disp"], $contents);
            $contents = str_replace("++[lcl_kana]", $_SESSION["i"]["kana_disp"], $contents);
            $contents = str_replace("++[lcl_company]", $_SESSION["i"]["company"], $contents);
            $contents = str_replace("++[lcl_address]", $_SESSION["i"]["address_disp"], $contents);
            $contents = str_replace("++[lcl_department]", $_SESSION["i"]["department"], $contents);
            $contents = str_replace("++[lcl_official_position]", $_SESSION["i"]["official_position"], $contents);
            $contents = str_replace("++[lcl_tel]", $_SESSION["i"]["tel_disp"], $contents);
            $contents = str_replace("++[lcl_mail]", $_SESSION["i"]["mail_disp"], $contents);
            $contents = str_replace("++[lcl_detail]", $_SESSION["i"]["detail_disp"], $contents);
        }
        return $contents;
    }
    
    // セッション変数にフォームから取得した値をセットする
    private function SetSessionVariableForm()
    {
        $_SESSION["i"]["gender"] = $this->post["gender"];
        $_SESSION["i"]["name"] = $this->post["name"];
        $_SESSION["i"]["kana"] = $this->post["kana"];
        $_SESSION["i"]["company"] = $this->post["company"];
        $_SESSION["i"]["department"] = $this->post["department"];
        $_SESSION["i"]["official_position"] = $this->post["official_position"];
        $_SESSION["i"]["tel"] = $this->post["tel"];
        $_SESSION["i"]["mail"] = $this->post["mail"];
        $_SESSION["i"]["address"] = $this->post["address"];
        $_SESSION["i"]["detail"] = $this->post["detail"];
        
    }
    
    // 入力チェック
    private function InputCheck()
    {
        $retflg = true;
        
        $objIcheck = new CLS_InputChecker();
        
        $gender   = $_SESSION["i"]["gender"];
        $name     = $_SESSION["i"]["name"];
        $kana     = $_SESSION["i"]["kana"];
        $tel      = $_SESSION["i"]["tel"];
        $address  = $_SESSION["i"]["address"];
        $mail     = $_SESSION["i"]["mail"];
        $detail   = $_SESSION["i"]["detail"];
        

        do
        {
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $gender);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["gender_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["gender"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["gender_disp"] = $this->ResWord["gender_{$gender}"];
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
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $kana);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["kana_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["kana"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["kana_disp"] = $kana;
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
            $ret = $objIcheck->MultiCheck(array(CLS_InputChecker::REQUIRED), $address);
            if ($ret != 0)
            {
                //エラーなら表示変数にエラー内容セット
                $_SESSION["i"]["address_disp"] = $this->SetErrTextTagP($this->GetResMessage("input_checker{$ret}", $this->ResWord["address"]));
                $retflg = false;
                break;
            }
            
            // 正常ならそのまま内容をセット
            $_SESSION["i"]["address_disp"] = $address;
        }while(0);
        
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
