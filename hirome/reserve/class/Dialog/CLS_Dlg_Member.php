<?php
require_once(dirname(__FILE__) . "/../Ex/CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_MstMember.php");

//ダイアログクラス(会員)
class CLS_Dlg_Member extends CLS_Page_Ex
{
	public function index()
	{
		// システム変数の置換
		$contents = $this->replace_template_sys("tpl/dialog/member.tpl");
		
		$index = $this->get["index"];
		
		// DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        $objMstCustomer= new CLS_Db_MstMember($conn);
        
        $table = $objMstCustomer->GetTableSearch("","1" );
        
        
        // ===========================
		// リストの処理
		// ===========================
		// リスト部分のテンプレート取得
		$block_start = "<!-- LIST_START -->";
		$block_end   = "<!-- LIST_END -->";
		preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
		$row_template = ltrim($matches[1], "\r\n");
		
		
		// DB検索処理
		$count = count($table);
		$data_list = "";
		
		
		for ($i = 0; $i < $count; $i ++)
		{
			// テンプレート読み込み
			$row = $row_template;

			$company_name = trim($table[$i]["company_name"]);
			$staff_name = trim($table[$i]["staff_name"]);
			$id = trim($table[$i]["id"]);
			// データ置換
			$row = str_replace("++[loopcnt]", $i, $row); // 必須
			$row = str_replace("++[lst_company_name]", $company_name, $row);
			$row = str_replace("++[lst_staff_name]", $staff_name, $row);
			$row = str_replace("++[lst_id]", $id, $row);

			// データを溜め込む
			$data_list .= $row;
		}
	    
		// 画面初期化
		$contents = str_replace("++[index]", $index, $contents);
		$contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $data_list, $contents);
        
		
		echo $contents;
	}
}
