<?php
require_once("Ex/CLS_Online_Ex.php");

// オンラインクラス
class CLS_Online extends CLS_Online_Ex
{
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    // index.php
    public function index()
    {
        // 画面用セッション変数初期化
        unset($_SESSION["n"]);
        unset($_SESSION["i"]);
        
        parent::index();
    }
    
    // badrequest.php
    public function badrequest()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("online/badrequest.html");
        echo $contents;
    }
}
