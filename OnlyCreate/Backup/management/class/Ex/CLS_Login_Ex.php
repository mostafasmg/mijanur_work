<?php
require_once("CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_Login.php");

/********************************************************************/
/* Ver.20151215                                                     */
/* ---------------------------------------------------------------- */
/* ◆ ◆ ◆ 編 集 禁 止 ◆ ◆ ◆                                    */
/* 編集する場合は上長の承認が必要です。                             */
/* ---------------------------------------------------------------- */
/* This file is not editable.                                       */
/********************************************************************/
// ログインクラス(Base)
class CLS_Login_Ex extends CLS_Page_Ex
{
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    // index.php
    public function index()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("login.html");
        
        // エラーメッセージの置換
        $contents = $this->ShowDisplayMessage($contents);
        
        echo $contents;
    }
    
    // login.php
    public function login()
    {
        // DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        $objLogin = new CLS_Db_Login($conn);
        $table = $objLogin->IsLoginTbl($this->post["user"], $this->post["pass"]);
        
        if (count($table) > 0)
        {
            // ログイン成功
            $_SESSION["l"]["key"] = $table[0]["key"];
            $_SESSION["l"]["name"] = $table[0]["name"];
            $_SESSION["l"]["authority"] = $table[0]["authority"];
            $_SESSION["l"]["roleid"] = $table[0]["roleid"];
            
            // ログインロールを使用する場合はロール名を取得する
            if ($_SESSION["l"]["roleid"] != 0)
            {
                $_SESSION["l"]["rolename"] = $objLogin->GetRoleName($_SESSION["l"]["roleid"]);
            }
            
            // カテゴリ一覧を取得しておく
            $objMenu = new CLS_Db_CustomMenu($conn);
            $CateTbl = $objMenu->GetCustomMenuCategory($_SESSION["l"]["roleid"]);
            $_SESSION["l"]["customtab"] = $CateTbl;
            
            header("Location: online/");
            exit();
        }
        else
        {
            // コネクションのクローズ
            $conn = CLS_Db::CloseConnection();
            
        	$this->SetDispMesWarning($this->GetResMessage("login_err1"));
            
            // ログイン失敗
            header("Location: ./index.php");
            exit();
        }
    }
    
    // logout.php
    public function logout()
    {
        unset($_SESSION["l"]);
        header("Location: ./");
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
}
