<?php
require_once("Ex/CLS_InputChecker_Ex.php");

// 入力チェッククラス
class CLS_InputChecker extends CLS_InputChecker_Ex
{
    // ステータス番号
    const ERR_ADDRESS_SYMBOL = 8;      // タイプ異常(英数+一部記号)
    const ERR_CHECK_DATE = 9;		   // 日付異常(存在しない日付)
    const ERR_DECIMAL = 10;		       // 小数点異常(数値+「.」)
    
    // チェック種別
    const ADDRESS_SYMBOL = "isTypeAddressSymbol";     // 英数チェック(一部記号を許可)
    const CHECK_DATE = "isTypeCheckDate";			  // 日付チェック(存在している日付か確認)
    const DECIMAL = "isTypeDecimal";
     
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
	// 入力チェック(タイプチェック_英数字+一部の記号)
	// 文字列が英数若しくは一部記号以外ならFALSEを返す
	public function isTypeEisuSymbol($str = "")
	{
	    return parent::isTypeEisuSymbol($str);
	    
	    // 案件対応が必要な場合は上記をコメント化して記述
    }
    
	// 入力チェック(タイプチェック_数字+ハイフン)
	// 文字列が数値若しくは一部記号以外ならFALSEを返す
	public function isTypeNumberSymbol($str = "")
	{
	    return parent::isTypeNumberSymbol($str);
	    
	    // 案件対応が必要な場合は上記をコメント化して記述
	}
	
	// 禁止文字チェック
	// 禁止文字が含まれる場合はFALSEを返す
	public function isProscriptionChar($str = "")
	{
	    return parent::isProscriptionChar($str);
	    
	    // 案件対応が必要な場合は上記をコメント化して記述
	}
	// 入力チェック(数値、英語、｢-_+/@:.｣以外は許可しない)
	// 文字列が数値若しくは一部記号以外ならFALSEを返す
	public function isTypeAddressSymbol($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[a-z0-9_@:\.\-\+\/]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker::ERR_ADDRESS_SYMBOL;
			return false;
		}
		else
		{
			return true;
		}
	}
	//存在する日付かチェック
	public function isTypeCheckDate($str = "")
	{
		// チェック値をセット
		$work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		//年月日に分割
		$ymd = explode("-",$work_str);
		$year = $ymd[0];
		$month = $ymd[1];
		$day = $ymd[2];
        
		// 0が入ってきたらとりあえずOKとする。
		if ($year == 0 or $month == 0  or $day == 0 )
		{
			return true;
		}
		
		//日付チェック
		$checkdate = checkdate($month ,$day ,$year);
		
		//チェック後処理
		if(!$checkdate)
		{
			$this->statusNumber = CLS_InputChecker::ERR_CHECK_DATE;
			return false;
		}
		else
		{
			return true;
		}
	}
	//小数点かチェック
	public function isTypeDecimal($str = "")
	{
		// チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^([1-9]\d*|0)(\.\d+)?$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_NUMBER;
			return false;
		}
		else
		{
			return true;
		}
	}
}
