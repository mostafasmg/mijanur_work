<?php
require_once("CLS_Page_Ex.php");

// ユーザーマスタメンテナンスクラス(Base)
class CLS_BasicUser_Ex extends CLS_Page_Ex
{
    /********************************************************************/
    /* Public Method                                                   */
    /********************************************************************/
    // index.php
    public function index()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("online/basic/user/index.html");
        
        echo $contents;
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
}
