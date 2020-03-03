<?php
require_once("Ex/CLS_FileUpd_Ex.php");
require_once("Db/CLS_Db_MstFileserver.php");

// ファイルアップロードクラス(ファイルサーバ対応)
class CLS_FileUpd extends CLS_FileUpd_Ex
{
    // ファイルタイプ
    const FILE_TYPE_TEMPORARY = 1;      // テンポラリー
    const FILE_TYPE_THUMBNAIL = 100;    // サムネイル画像
    const FILE_TYPE_VIDEO     = 200;    // 動画、画像
    const FILE_TYPE_PDF       = 300;    // PDFなどのファイル
    
    // アクション後の値取得用
    public $fileserver_id;              // ファイルサーバーID
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($dir_offset)
    {
        parent::__construct($dir_offset);
    }
    
    // ファイルアップロード処理
    public function UploadFile($tagname, $customer_id = "x")
    {
        // テンポラリー領域取得
        $tbl_fileserver = $this->getServerData(self::FILE_TYPE_TEMPORARY);
        if (count($tbl_fileserver) == 0)
        {
            // レコードが取得できなければアップロード不可能なのでエラーとする
            return false;
        }
        
        // マルチバイト文字ファイル対策(Windows, Unix)
        setlocale(LC_CTYPE, 'Japanese_Japan.932', 'ja_JP.UTF-8');
        
        $sysdate = $customer_id . "_" . date("YmdHis");
        $filenames = explode(".", basename($_FILES[$tagname]["name"]));
        if (count($filenames) > 1)
        {
            // 拡張子あり
            $ext = $filenames[count($filenames)-1];
            $savefilename = "{$sysdate}.{$ext}";
        }
        else
        {
            // 拡張子なし
            $ext = "";
            $savefilename = "{$sysdate}";
        }
        
        // 保存先のパスを生成
        $savedir = sprintf("%s", $tbl_fileserver[0]["local_path"]);
        
        if (is_uploaded_file($_FILES[$tagname]["tmp_name"]))
        {
            move_uploaded_file($_FILES[$tagname]["tmp_name"], $savedir . $savefilename);
            
            // 取得値セット
            $this->orgname = basename($_FILES[$tagname]["name"]);
            $this->savedir = $savedir;
            $this->filename = $savefilename;
            $this->ext = $ext;
            $this->errcode = "";
            
            return true;
        }
        else
        {
            $this->errcode = $err01;
            return false;
        }
    }
    
    // テンポラリーパスから本パスに移動する
    public function MoveFile($temporary_filepath, $filename, $file_type)
    {
        // 保存領域取得
        $tbl_fileserver = $this->getServerData($file_type);
        if (count($tbl_fileserver) == 0)
        {
            // レコードが取得できなければアップロード不可能なのでエラーとする
            return false;
        }
        
        // テンポラリーパスから本パスに移動する
        $main_filepath = sprintf("%s%s", $tbl_fileserver[0]["local_path"], $filename);
        rename($temporary_filepath, $main_filepath);
        
        // 取得値のセット
        $this->fileserver_id = $tbl_fileserver[0]["id"];
        
        return true;
    }
    
    // ファイル削除
    public function FileDelete($filepath)
    {
        if (file_exists($filepath))
        {
            unlink($filepath);
        }
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
    // データ保存先サーバ情報の取得
    private function getServerData($file_type)
    {
        // DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        // DBクラスのインスタンス生成
        $objMstFileserver = new CLS_Db_MstFileserver($conn);
        
        // データの取得
        $table = $objMstFileserver->GetTableActiveRecodeByFiletype($file_type);
        
        return $table;
    }
}
