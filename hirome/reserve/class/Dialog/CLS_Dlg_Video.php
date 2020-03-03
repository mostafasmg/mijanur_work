<?php
require_once(dirname(__FILE__) . "/../Ex/CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_TblCustomerFile.php");

//ダイアログクラス(VIDEO)
class CLS_Dlg_Video extends CLS_Page_Ex
{
    public function index()
    {
        // システム変数の置換
        $contents = $this->replace_template_sys("tpl/dialog/video.tpl");
        
        // GETパラメータ取得
        $file_id = $this->get["file_id"];
        
        // DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        // 動画情報取得
        $objTblCustomerFile = new CLS_Db_TblCustomerFile($conn);
        $table = $objTblCustomerFile->GetTableDetailsById($file_id);
        
        // データ用意
        $video_path = sprintf("http://%s%s", $table[0]["access_url"], $table[0]["file_name"]);
        
        // データ置換
        $contents = str_replace("++[lcl_video_path]", $video_path, $contents);
        
        echo $contents;
    }
}
