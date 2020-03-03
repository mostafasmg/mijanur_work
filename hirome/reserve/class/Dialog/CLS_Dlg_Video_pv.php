<?php
require_once(dirname(__FILE__) . "/../Ex/CLS_Page_Ex.php");
require_once(dirname(__FILE__) . "/../Db/CLS_Db_TblPvinfomation.php");

//ダイアログクラス(VIDEO)
class CLS_Dlg_Video_pv extends CLS_Page_Ex
{
	public function index()
	{
		// システム変数の置換
		$contents = $this->replace_template_sys("tpl/dialog/video_pv.tpl");
		
		$id = $this->get["id"];
		
		$path = $this->get["path"];
		
		// DBコネクションの生成
        $conn = CLS_Db::OpenConnection();
        
        $objTblPvinfo = new CLS_Db_TblPvinfomation($conn);
        
        $table = $objTblPvinfo->GetTableById( $id );
        
        $video_path = $path. $table[0]["video_path"];
        
        $contents = str_replace("++[lcl_video_path]", $video_path, $contents);
        
        $contents = str_replace("++[lcl_path]", $path, $contents);
        
        
        // ===========================
		// リストの処理
		// ===========================
		// リスト部分のテンプレート取得
		$block_start = "<!-- LIST_START -->";
		$block_end   = "<!-- LIST_END -->";
		preg_match("/".$block_start."(.*?)".$block_end."/s", $contents, $matches);
		$row_template = ltrim($matches[1], "\r\n");
		
		
		// DB検索処理
		//$table = $objFmbusyo->GetTableByBscode();
// テストデータ
		$table = array();
		
		$table[0]["video_path"] = "2017_11_12_10_39_41.mp4";
		$table[0]["thum_path"] = "thum_img001.png";
		
		$table[1]["video_path"] = "DJI_0001.MOV";
		$table[1]["thum_path"] = "thum_img002.png";
		
		$table[2]["video_path"] = "DJI_0016.MOV";
		$table[2]["thum_path"] = "thum_img003.png";
// テストデータ		
		$count = count($table);
		$data_list = "";
		for ($i = 0; $i < $count; $i ++)
		{
			// テンプレート読み込み
			$row = $row_template;

			$thum_path = trim($table[$i]["thum_path"]);
			$video_path = trim($table[$i]["video_path"]);
			// データ置換
			$row = str_replace("++[loopcnt]", $i, $row); // 必須
			$row = str_replace("++[lst_thum_path]", $thum_path, $row);
			$row = str_replace("++[lst_video_path]", $video_path, $row);

			// データを溜め込む
			$data_list .= $row;
		}
	    
		// 画面初期化
		$contents = preg_replace("/".$block_start."(.*?)".$block_end."/s", $data_list, $contents);
        
		
		echo $contents;
	}
}
