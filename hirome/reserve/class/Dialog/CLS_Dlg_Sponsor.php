<?php
require_once(dirname(__FILE__) . "/../Ex/CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_MstSponsor.php");

//ダイアログクラス(スポンサー)
class CLS_Dlg_Sponsor extends CLS_Page_Ex
{
	public function index()
	{
		// システム変数の置換
		$contents = $this->replace_template_sys("tpl/dialog/sponsor.tpl");
		
		$index = $this->get["index"];
		
		// DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        $objMstSponsor= new CLS_Db_MstSponsor($conn);
        
        $table = $objMstSponsor->GetTableALL();
        
        
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

			$sponsor_name = trim($table[$i]["sponsor_name"]);
			$id = trim($table[$i]["id"]);
			// データ置換
			$row = str_replace("++[loopcnt]", $i, $row); // 必須
			$row = str_replace("++[lst_sponsor_name]", $sponsor_name, $row);
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
