<?php
session_start();
require_once(dirname(__FILE__) . "/../../lib/report/mbfpdf.php");

	// 1ページに表示するレコード件数
	define("REPORT_MAX_ROWS_L1", 33);
	define("REPORT_MAX_ROWS_L2", 16);
	define("REPORT_MAX_ROWS_L3", 11);
	define("REPORT_MAX_ROWS_P1", 50);
	define("REPORT_MAX_ROWS_P2", 25);
	define("REPORT_MAX_ROWS_P3", 16);
	define("REPORT_PROPERTY_AUTHOR", "在庫管理システム");

// 帳票発行(Base)
class CLS_Report_Ex
{
    // リクエストパラメーター
    protected $post = array();
    protected $get = array();
    
    // リソース関連
    protected $ResMessage = array();
    protected $ResWord = array();
    protected $ResDigits = array();
    
    // 言語フラグ
    protected $Lang;
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct()
    {
        // ログインしていなければトップページへ強制転送
        if (strlen($_SESSION["l"]["key"]) == 0)
        {
            header("Location: {$this->dir_offset}");
            exit();
        }
        
        // POST,GETパラメータをローカル変数にセット
        unset($this->post);
        unset($this->get);
        
        foreach($_POST as $key => $val)
        {
            $this->post[$key] = htmlspecialchars($val);
        }
        
        foreach($_GET as $key => $val)
        {
            $this->get[$key] = htmlspecialchars($val);
        }
        
        // メッセージリソースの展開
        $this->ExpansionMessageResource();
        
        // ワードリソースの展開
        $this->ExpansionWordResource();
        
        // 桁数リソースの展開
        $this->ExpansionDigitsResource();
    }
    
    /********************************************************************/
    /* private Method                                                   */
    /********************************************************************/
    // メッセージリソースの展開
    private function ExpansionMessageResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/message.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $Lang)
                {
                    $index = $i;
                }
            }
            
            $this->ResMessage[$ex_line[0]] = $ex_line[$index];
        }
        
        fclose($fp);
    }
    
    // ワードリソースの展開
    private function ExpansionWordResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/word.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $Lang)
                {
                    $index = $i;
                }
            }
            
            $this->ResWord[$ex_line[0]] = $ex_line[$index];
        }
        
        fclose($fp);
    }
    
    // 桁数リソースの展開
    private function ExpansionDigitsResource()
    {
        $filepath = dirname(__FILE__) . "/../../resource/digits.dat";
        $fp = fopen($filepath, "r");
        if ($fp === false)
        {
            return;
        }
        
        while(!feof($fp))
        {
            $line = fgets($fp);
            
            $ex_line = explode(",", $line);
            
            $index = 1;
            $count = count($ex_line);
            for($i = 0; $i < $count; $i++)
            {
                if ($ex_line[$i] == $Lang)
                {
                    $index = $i;
                }
            }
            
            $this->ResDigits[$ex_line[0]] = $ex_line[$index];
        }
        
        fclose($fp);
    }
    
    /********************************************************************/
    /* protected Method                                                 */
    /********************************************************************/
	// タイトルの出力
	protected function put_report_title($pdf, $title) 
	{
		// フォントを設定（フォント名, 文字スタイル）
		$pdf->SetFont(GOTHIC, 'U', 14);

		// 文字を整形して出力（行の高さ, テキスト）
		$pdf->Write8(10, $title);
	}

	// 日付の出力
	protected function put_report_datetime($pdf, $direction) 
	{
		if ($direction == "L")
		{
			// 横
			$x = 240;
		}
		else
		{
			// 縦
			$x = 160;
		}
		$pdf->SetFont(GOTHIC, '', 11);
		$pdf->SetXY($x, 10);
		$pdf->Write8(12, date("Y/m/d H:i:s"));
	}

	// 発行範囲の出力
	protected function put_report_range($pdf, $range) 
	{
		$pdf->SetFont(GOTHIC, '', 11);
		$pdf->SetXY(10, 17);
		$pdf->Write8(12, "{$range}");
	}

	// オプションの出力
	protected function put_report_option($pdf, $range) 
	{
		$pdf->SetFont(GOTHIC, '', 11);
		$pdf->SetXY(10, 22);
		$pdf->Write8(12, $range);
	}

	// ヘッダーの出力
	protected function put_report_header($pdf, $heddertext, $poss, $end_pos) 
	{
		// ヘッダー行数
		$h_rows = count($heddertext);

		// ヘッダーエリア塗りつぶし
		$this->put_report_header_waku($pdf, $poss, $end_pos, $h_rows);

		// ヘッダー行分ループする
		for ($j = 1; $j <= $h_rows; $j++)
		{
			// 出力用データ生成
			$htext_sp = explode(",", $heddertext[$j]);
			$poss_sp = explode(",", $poss[$j]);
			$count = count($htext_sp);

			// ヘッダーテキストの出力
			for ($i = 0; $i < $count; $i++)
			{
				$pdf->SetFont(GOTHIC, '', 11);
				$pdf->SetXY($poss_sp[$i], (5*($j-1)) + 27);
				$pdf->Write8(12, $htext_sp[$i]);
			}
		}
	}

	// ヘッダー枠の出力
	protected function put_report_header_waku($pdf, $poss, $end_pos, $h_rows) 
	{
		for ($i = 0; $i < $h_rows; $i++)
		{
			$pdf->SetFillColor(204, 204, 204);
			$pdf->Rect(10.0, 30.0 + ($i*5), ($end_pos - 10.0),  5.0, 'F');

			$pdf->SetDrawColor(0, 0, 0);
			$pdf->Line(10.0, 30.0 + ($i*5), $end_pos, 30.0 + ($i*5));
			$pdf->Line(10.0, 35.0 + ($i*5), $end_pos, 35.0 + ($i*5));
		}
	}

	// ページ番号の出力
	protected function put_report_pageno($pdf, $page, $max_page, $direction)
	{
		if ($direction == "L")
		{
			// 横
			$x = 240;
		}
		else
		{
			// 縦
			$x = 160;
		}
		
		$pdf->SetFont(GOTHIC, '', 11);
		$pdf->SetXY($x, 17);
		$pdf->Write8(12, "Page {$page}/{$max_page}");
	}

	// データの出力(メイン処理)
	protected function put_report_maindata($pdf, $table_data, $datas, $heddertext, $poss, $end_pos, $title, $range, $special, $direction = "L", $options = "")
	{
		// 中間線の設定(2行以上の場合のみ使用)
		$center_end_pos = $end_pos;
		
		// 1レコードの表示行数を取得
		$disprows = count($poss);

		// 1ページの最大表示行数を取得
		$report_max_rows = constant(REPORT_MAX_ROWS_ . $direction . $disprows);

// オプションを表示する場合は最大行数を1つ減らす
		if ( strlen($options) > 0)
		{
			$report_max_rows = $report_max_rows - 1;
		}

		// ページ数の計算
		$max_count = count($table_data);
		$max_pages = intval($max_count / $report_max_rows);
		if (($max_count % $report_max_rows) > 0)
		{
			$max_pages = $max_pages + 1;
		}

		// 全体のループ(ページ数単位)
		for($page = 0; $page < $max_pages; $page++)
		{
			// 1ページ分の出力行数を判断
			if ($page < ($max_pages - 1))
			{
				$rows = $report_max_rows;
			}
			else
			{
				$rows = $max_count - ($page * $report_max_rows);
			}

			// **************************** //
			// +++   レポート処理開始   +++ //
			// **************************** //
			// ページを新規追加
			$pdf->AddPage();

			// タイトルエリアの出力
			$this->put_report_title($pdf, $title);

			// 日付表示
			$this->put_report_datetime($pdf, $direction);

			// 発行範囲
			$this->put_report_range($pdf, $range);

			// オプションの出力
			if (strlen($options) > 0)
			{
				$this->put_report_option($pdf, $options);
			}

			// ヘッダーの出力
			$this->put_report_header($pdf, $heddertext, $poss, $end_pos);

			// ページ番号の出力
			$this->put_report_pageno($pdf, ($page + 1), $max_pages, $direction);

			// レコードの出力
			// **************************** //
			// +++     データの処理     +++ //
			// **************************** //
			for ($i = 0; $i < $rows; $i++) 
			{
				// データの出力(X軸)
				$datas_count = count($datas_sp);

				for($k = 1; $k <= $disprows; $k++)
				{
					// 出力用のデータ要素名を展開
					$datas_sp = explode(",", $datas[$k]);
					$poss_sp = explode(",", $poss[$k]);
					$poss_count = count($poss_sp);

					for ($j = 0; $j < $poss_count; $j++)
					{
						// 特殊条件フィールドの判定
						// フォント
						if (isset($special[$datas_sp[$j]]["font"]))
						{
							$pdf->SetFont($special[$datas_sp[$j]]["font"], '', 24);
							$pos_x_offset = 6;
							$pos_y_offset = 3;
							
							// 中間線のEndPosを更新する
							$center_end_pos = $poss_sp[$j];
						}
						else
						{
							$pdf->SetFont(GOTHIC, '', 11);
							$pos_x_offset = 0;
							$pos_y_offset = 0;
						}
						
						$dt_pos = $i + ($page * $report_max_rows);
						$value = $table_data[$dt_pos][$datas_sp[$j]];
						
						$pos_x = $poss_sp[$j] + $pos_x_offset;
						$pos_y = 32 + (($disprows-1)*5) + ($i*$disprows*5) + (($k-1)*5) + $pos_y_offset;
						$pdf->SetXY($pos_x, $pos_y);
						$pdf->Write8(12, $value);
					}
				}
			}

			// **************************** //
			// +++      罫線の処理      +++ //
			// **************************** //
			// 行数の位置を計算
			$line_y_pt = ($rows * $disprows * 5.0) + 30.0 + (5.0 * $disprows);
			$line_x_pt = 35.0 + (5.0 * $disprows);

			// 横線
			for ($i = 0; $i <$rows; $i++)
			{
				for($k = 1; $k <= $disprows; $k++)
				{
					if ($k == $disprows)
					{
						// 最終行は実線
						$pdf->SetDrawColor(0, 0, 0);
						$pdf->Line(10.0, $line_x_pt, $end_pos, $line_x_pt);
					}
					else
					{
						// 最終行以外は中間線とする
						$pdf->SetDrawColor(192, 192, 192);
						$pdf->Line(10.0, $line_x_pt, $center_end_pos, $line_x_pt);
					}
					$line_x_pt = $line_x_pt + 5.0;
				}
			}

			// 線の色を実線(黒)にする
			$pdf->SetDrawColor(0, 0, 0);
			// 縦線
			for ($i = 0; $i < $rows+1; $i++) 
			{
				// データの出力(X軸)
				$datas_count = count($datas_sp);

				for($k = 1; $k <= $disprows; $k++)
				{
					// 出力用のデータ要素名を展開
					$datas_sp = explode(",", $datas[$k]);
					$poss_sp = explode(",", $poss[$k]);
					$poss_count = count($poss_sp);

					for ($j = 0; $j < $poss_count; $j++)
					{
						$dt_pos = $i + ($page * $report_max_rows);
						$value = $table_data[$dt_pos][$datas_sp[$j]];

						$pos_x = $poss_sp[$j];
						$pos_y = 30 + ($i*$disprows*5) + (($k-1)*5);
						$pdf->Line($pos_x, $pos_y, $pos_x, $pos_y+5);
					}
				}
			}

			// 右端のライン
			$pdf->Line($end_pos, 30.0, $end_pos, $line_y_pt);
		}
	}
}
