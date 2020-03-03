<?php
// ファイルアップロードクラス(Base)
class CLS_FileUpd_Ex
{
    // システムパラメーター
    protected $dir_offset;
    
    // アクション後の値取得用
    public $orgname;        // オリジナルファイル名
    public $savedir;        // アップロードディレクトリ
    public $filename;       // リネーム後ファイル名
    public $ext;            // 拡張子
    public $errcode;        // エラーコード
    
    // エラーコード
    protected $err01 = "アップロードファイル無し";
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($dir_offset)
    {
        $this->dir_offset = $dir_offset;
    }
    
    // ファイルアップロード処理
    public function UploadFile($tagname)
    {
        $sysdate = date("YmdHis");
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
        
        // 保存先のパス取得
        $savedir = $this->GetSaveDirectory();
        
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
    
    // ファイル削除
    public function FileDelete($filename)
    {
        // 保存先のパス取得
        $savedir = $this->GetSaveDirectory();
        
        $delpath = $savedir . $filename;
        if (file_exists($delpath))
        {
            unlink($delpath);
        }
    }
    
    /********************************************************************/
    /* Protected Method                                                   */
    /********************************************************************/
    protected function GetSaveDirectory()
    {
        // アップロード先の基準Root判断
        if (UPFILE_BASE_ROOT == "ROOT")
        {
            // ROOT基準
            $strurl = "";
            for ($i = 0; $i < (DIRECTORY_LEVEL - 1); $i++)
            {
                $strurl .= "../";
            }
            $savedir = "{$strurl}{$this->dir_offset}" . UPFILE_SAVE_PATH;
        }
        else
        {
            // SYSTEMROOT基準
            $savedir = "{$this->dir_offset}" . UPFILE_SAVE_PATH;
        }
        
        return $savedir;
    }
}
