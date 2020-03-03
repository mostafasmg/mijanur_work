<?php
require_once(dirname(__FILE__) . "/../Db/CLS_Db_ScrollList.php");

// スクロールリスト(Base)
class CLS_ScrollList_Ex
{
	public $scrolllist = "";	// スクロールリスト
	private $listID;			// どのリストを取得するのか。
	private $flg;				// arrayフラグ
	private $objScroll;			// データベース接続
	private $scdata_struct;		// スクロールリストの構造
	private $scdata_data;		// スクロールリストのデータ
	
	// コンストラクタ
	public function __construct( $para_listID )
	{
		$this->listID = $para_listID;
		$this->objScroll = new CLS_Db_ScrollList();
		$this->SetScrollList();
		$this->setScrollListVariable();
	}
	
	/********************************************************************/
	/* Public Method                                                   */
	/********************************************************************/
	// スクロールリストのテンプレート作成・取得
	private function SetScrollList()
	{
		// 出力テンプレートの取得
		$tempScrollList = file_get_contents(dirname(__FILE__) . "/../../template/tpl/sclist/base.tpl");

		// リスト構造の取得(データベースより)
		$this->scdata_struct = $this->objScroll->GetScrollList_detail( $this->listID );

		// テンプレート
		$base_col_header = '<th class="++[sys_list_class]">++[sys_list_name]</th>';
		$base_col_data_link = '<td class="++[sys_list_class]"><a style="++[sys_disp_class_++[sys_list_lst]]" href="++[sys_list_href]">++[sys_list_name]</a></td>';
		$base_col_data = '<td class="++[sys_list_class]">++[sys_list_data]</td>';

		// 変数
		$temp_col_header;
		$temp_col_data;
		
		$tmpHeader = "";
		$tmpData = "";
		$rep_array = array( "lst" , "name" , "class" , "href");
		$cnt = count($this->scdata_struct);
		
		// リスト構造よりテンプレート作成
		for( $index = 0 ; $cnt > $index ; $index++ ){

			$temp_col_header = $base_col_header;

			if( empty($this->scdata_struct[$index]['href']) ){
				$temp_col_data = $base_col_data;
			}else{
				$temp_col_data = $base_col_data_link;
			}
			
			foreach( $rep_array as $value ){
				$temp_col_header = str_replace("++[sys_list_{$value}]", $this->scdata_struct[$index][$value] , $temp_col_header );
				$temp_col_data = str_replace("++[sys_list_{$value}]", $this->scdata_struct[$index][$value] , $temp_col_data );
				
				if( $value === "lst" ){
					$temp_col_data = str_replace("++[sys_list_data]", "++[lst_".$this->scdata_struct[$index][$value]."]" , $temp_col_data );
				}
			}
			$tmpHeader .= "\r\n".$temp_col_header;
			$tmpData .= "\r\n".$temp_col_data;
//var_dump("---",$tmpHeader,$tmpData);
		}

		// データの置換
		$tempScrollList = str_replace("++[SC_HEADER]", $tmpHeader."\r\n", $tempScrollList );
		$tempScrollList = str_replace("++[SC_DATA]", $tmpData."\r\n", $tempScrollList );

		// クラス変数に格納。
		$this->scrolllist = $tempScrollList;
//var_dump($this->scrolllist);
//var_dump($this->flg);

	}
	
	// スクロールリストの置換(テンプレートにデータ挿入)
	private function setScrollListVariable( )
	{
		$tempScrollList = $this->scrolllist;
		
		// データ取得
		$this->scdata_data = $this->objScroll->GetScrollListDate( $this->listID );
		
		// リスト部分のテンプレート取得
		$number_start = "<!-- SC_NUMBER_START -->";
		$number_end   = "<!-- SC_NUMBER_END -->";
		preg_match("/".$number_start."(.*?)".$number_end."/s", $tempScrollList, $matches);
		$row_number = ltrim($matches[1], "\r\n");

		$block_start = "<!-- SC_DATA_START -->";
		$block_end   = "<!-- SC_DATA_END -->";
		preg_match("/".$block_start."(.*?)".$block_end."/s", $tempScrollList, $matches);
		$row_template = ltrim($matches[1], "\r\n");

		$row_nums = "";
		$rows = "";
		$count = count($this->scdata_data);
		for ($i = 0; $i < $count; $i++)
		{
			$row_num = $row_number;
			$row = $row_template;

			$row_num = str_replace("++[sc_number]", $index+1, $row_num);

			// データ置換
			$cnt = count($this->scdata_struct);
			for( $j = 0 ; $cnt > $j ; $j++ ){
				// データ表示
				$col = $this->scdata_struct[$j]["col"];
				$data = $this->scdata_data[$i][$col];
				$row = str_replace("++[lst_{$col}]", $data, $row);

				// 表示・非表示
				$lst = $this->scdata_struct[$j]["lst"];
				$rep_col = "++[sys_disp_class_" . $lst . "]";
				$disp_flg_col = $this->scdata_struct[$j]["disp_flg_col"];
				if( empty($disp_flg_col) ){
					// true = "1" ,false = "0"
					$disp_flg = "1";
				}else{
					$disp_flg = $this->scdata_data[$i][$disp_flg_col];
				}
				if( $disp_flg === "0" ){
					$row = str_replace($rep_col, "display:none;", $row);
				}else{
					$row = str_replace($rep_col, "", $row);
				}
			}

			// データを溜め込む
			$row_nums .= $row_num;
			$rows .= $row;
		}

		// データ置換(リスト)
		$tempScrollList = preg_replace("/".$number_start."(.*?)".$number_end."/s", $row_nums, $tempScrollList);
		$tempScrollList = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $tempScrollList);

		$this->scrolllist = $tempScrollList;
//var_dump($this->scrolllist);

	}

	// フラグビット処理
	// flg(0) → array( 0=>0 , 1=>0 )
	// flg(1) → array( 0=>1 , 1=>0 )
	// flg(2) → array( 0=>0 , 1=>2 )
	// flg(3) → array( 0=>1 , 1=>2 )
	// cnt(1) → array( ?=>? )
	// cnt(3) → array( ?=>? , ?=>? , ?=>? )
	private function bit_flg( $flg, $cnt = 2){
		if( is_numeric($flg) and is_numeric($cnt) ){
			if( $cnt >= 32 ){
				return false;
			}

			$arr;
			$powData = 1;
			for( $index = 0 ; $cnt > $index ; $index ++ ){
				$arr[$index] = $flg & $powData; // int & int → bit
				$powData << 1;	// 1, 2, 4, 8, 16, 32, 64...
			}
			return $arr;

		}else{
			return false;

		}

	}

}













