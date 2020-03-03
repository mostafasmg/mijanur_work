<?php
require_once("CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_Login.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_TblAccesslog.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_MstMember.php");
// ログインクラス(Base)
class CLS_Login_Ex extends CLS_Page_Ex
{
    const ROLE_MEMBER = 3;
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
// 2018.04.29 S:Add
            $_SESSION["l"]["member_id"] = $table[0]["member_id"];
            $_SESSION["l"]["company_name"] = $table[0]["company_name"];
// 2018.04.29 E:Add

// 2018.04.30 S: Add  会員のログイン履歴を落とす
            if($table[0]["roleid"] == self::ROLE_MEMBER)
            {
                $ret = $this->setMemberLog($conn);
                
                // エラーが発生していればトップへ
                if(!$ret)
                {
                    header("Location: ./index.php");
                    exit();
                }
// 2018.06.04 S: 退会状態チェック
                $objMstMember = new CLS_Db_MstMember($conn);
                $ret = $objMstMember->CheckStatus($_SESSION["l"]["member_id"]);
                
                // 退会済みならログインさせない
                if(!$ret)
                {
                    // コネクションのクローズ
                    $conn = CLS_Db::CloseConnection();
                    
                	$this->SetDispMesWarning($this->GetResMessage("login_err1"));
                            
                    header("Location: ./index.php");
                    exit();
                }
// 2018.06.04 E: 退会状態チェック
                
// 2018.09.08 S: 会員はコンテンツ一覧ページ送りにする
                header("Location: online/menu/contents_view/");
                exit();
// 2018.09.08 E: 会員はコンテンツ一覧ページ送りにする

            }
            
// 2018.04.30 E: Add  会員のログイン履歴を落とす
            
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
// 2018.04.30 S: Add  会員のログイン履歴を落とす
    // 会員のログイン情報を専用テーブルに記録する
	private function setMemberLog($conn)
	{
	    $ret = true;
	    
	    // インスタンス生成
		$objTblAccesslog = new CLS_Db_TblAccesslog($conn);
		
		// 登録データセット
		$access_date = date("Y-m-d H-i-s");
		$login_id = $_SESSION["l"]["key"];
		$access_type = $objTblAccesslog::LOGIN;
		
		
		// トランザクションの開始
		CLS_Db::BeginTransaction($conn);
		
		try
		{
			// UPDATE
			$objTblAccesslog->InsertTableNewData( $access_date , $login_id, $access_type);

			// コミット
			CLS_Db::Commit($conn);

		}
		catch (PDOException $ex)
		{
			// ロールバック
			CLS_Db::Rollback($conn);

			// エラーオブジェクトをセット
			$this->SetDispMesError($ex);

		}
        
		return $ret;
	}
// 2018.04.30 E: Add  会員のログイン履歴を落とす
}
