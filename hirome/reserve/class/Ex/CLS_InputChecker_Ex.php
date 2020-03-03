<?php
// 入力チェッククラス(Base)
class CLS_InputChecker_Ex
{
    // ステータス番号
    const OK = 0;                   // 正常
    const ERR_REQUIRED = 1;         // 未入力
    const ERR_DIGITS = 2;           // 桁数異常
    const ERR_EISU = 3;             // タイプ異常(英数)
    const ERR_NUMBER = 4;           // タイプ異常(数値)
    const ERR_EISU_SYMBOL = 5;      // タイプ異常(英数+一部記号)
    const ERR_NUMBER_SYMBOL = 6;    // タイプ異常(数値+一部記号)
    const ERR_PROSCRIPTION = 7;     // 禁止文字
    const ERR_NUMBER_PERIOD = 8;    // タイプ異常(数値+ピリオド)
    const ERR_MAILADDRESS = 9;      // メールアドレス異常
    const ERR_URL = 10;             // URL異常
    
    // チェック種別
    const REQUIRED = "isInputCharacter";        // 必須入力
    const DIGITS = "isDigitsRange";             // 桁数チェック
    const EISU = "isTypeEisu";                  // 英数チェック
    const NUMBER = "isTypeNumber";              // 数値チェック
    const EISU_SYMBOL = "isTypeEisuSymbol";     // 英数チェック(一部記号を許可)
    const NUMBER_SYMBOL = "isTypeNumberSymbol"; // 数値チェック(一部記号を許可)
    const PROSCRIPTION = "isProscriptionChar";  // 禁止文字チェック
    const NUMBER_PERIOD = "isTypeNumberPeriod"; // 数値チェック(数値+ピリオド)
    const MAILADDRESS = "isTypeMailAddress";    // メールアドレス異常
    const URL = "isTypeURL";                    // URLチェック
    
    
    
    // チェック用フィールド変数
    protected $statusNumber;    // 各種メソッド実行後のステータス番号
    private $inputString;       // チェック対象文字列
    private $digits;            // チェック桁数
    private $ReturnLength;      // 戻り値の0埋め桁数(コンストラクタで指定)
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($length = 0)
    {
        // マルチチェックの戻り値を書式指定する
        $ret = $this->isTypeNumber($length);
        if ($ret)
        {
            // 数値なら桁数のセット
           $this->ReturnLength = $length;
        }
        else
        {
            // 数値ではないので0をセット
            $this->ReturnLength = 0;
        }
    }
    
    // マルチチェック
    // 引数で指定されたチェックを一括で行いステータス番号を返す
    // $checksはarray、桁数チェックを行う場合は$digitsをセットする
    public function MultiCheck($checks, $inputString, $digits = 0)
    {
        // エラー番号の初期化
        $RetNumber = CLS_InputChecker_Ex::OK;
        
        // チェック用フィールド変数へのセット
        $this->inputString = $inputString;
        $this->digits = $digits;
        
        $count = count($checks);
        for ($i = 0; $i < $count; $i++)
        {
            // チェック処理(variable function)
//            $ret = $this->$checks[$i]();
            $function_name = $checks[$i];
            $ret = $this->$function_name();
            if (!$ret)
            {
                // エラー発生なので処理を終える
                $RetNumber = $this->statusNumber;
                break;
            }
        }
        
        return $this->setReturnNumber($RetNumber);
    }
    
    // 未入力チェック
    // 文字列長が0文字ならFALSEを返す
    public function isInputCharacter($str = "")
    {
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
        // チェック処理
        if (strlen(trim($work_str)) == 0) 
        {
            $this->statusNumber = CLS_InputChecker_Ex::ERR_REQUIRED;
            return false;
        }
        else
        {
            return true;
        }
    }
    
    // 桁数チェック
    // 文字列長が指定桁数を超える場合はFALSEを返す
    public function isDigitsRange($str = "", $digits = 0)
    {
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        $work_digits = $this->setWorkDigits($digits);
        
        // チェック処理
        $sjis_str = mb_convert_encoding($work_str, "SJIS", "UTF-8");
        if ((strlen(trim($sjis_str))) > $work_digits) 
        {
            $this->statusNumber = CLS_InputChecker_Ex::ERR_DIGITS;
            return false;
        }
        else
        {
            return true;
        }
    }
    
	// 入力チェック(タイプチェック_英数字)
	// 文字列が英数以外ならFALSEを返す
	public function isTypeEisu($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[a-zA-Z0-9]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_EISU;
			return false;
		}
		else
		{
			return true;
		}
	}
    
	// 入力チェック(タイプチェック_数字)
	// 文字列が数値以外ならFALSEを返す
	public function isTypeNumber($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[0-9]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_NUMBER;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 入力チェック(タイプチェック_英数字+一部の記号)
	// 文字列が英数若しくは一部記号以外ならFALSEを返す
	// ベース処理では-(ハイフン)を許可している
	public function isTypeEisuSymbol($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[-a-zA-Z0-9]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_EISU_SYMBOL;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 入力チェック(タイプチェック_数字+一部の記号)
	// 文字列が数値若しくは一部記号以外ならFALSEを返す
	// ベース処理では-(ハイフン)を許可している
	public function isTypeNumberSymbol($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[-0-9]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_NUMBER_SYMBOL;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 入力チェック(タイプチェック_数字+ピリオドを許可)
	// 文字列が数値若しくは一部記号以外ならFALSEを返す
	public function isTypeNumberPeriod($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[.0-9]+$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_NUMBER_PERIOD;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 入力チェック(メールアドレスチェック)
	// メールアドレスのフォーマットチェック
	public function isTypeMailAddress($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]+@[a-zA-Z0-9-]+(?:\.[a-zA-Z0-9-]+)*$/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_MAILADDRESS;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 禁止文字チェック
	// 禁止文字が含まれる場合はFALSEを返す
	// ベース処理では*%&_を禁止している
	public function isProscriptionChar($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
		
		// 該当文字を含む場合にNGとする     2017-02-15暫定で\禁止
		if (preg_match("/[*%&_\\\]/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_PROSCRIPTION;
			return false;
		}
		else
		{
			return true;
		}
	}
	
	// 入力チェック(URLチェック)
	// 許可する記号が英数シンボルより多い
	public function isTypeURL($str = "")
	{
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
		// 未入力チェックは他でやるのでここではOKとする
		if (strlen(trim($work_str)) == 0)
		{
			return true;
		}
		
		if (!preg_match("/^[a-zA-Z0-9.!#$%&'*+\/=?^_`{|}~-]/", $work_str)) 
		{
            $this->statusNumber = CLS_InputChecker_Ex::ERR_MAILADDRESS;
			return false;
		}
		else
		{
			return true;
		}
	}
	
    /********************************************************************/
    /* protected Method                                                 */
    /********************************************************************/
    // チェック文字列の判断
    protected function setWorkString($str)
    {
        $work_str = $str;
        if (strlen($work_str) == 0)
        {
            $work_str = $this->inputString;
        }
        
        return $work_str;
    }
    
    // チェック桁数の判断
    protected function setWorkDigits($digits)
    {
        $work_digits = $digits;
        if ($work_digits <= 0)
        {
            $work_digits = $this->digits;
        }
        
        return $work_digits;
    }
    
    // 戻り値の生成
    protected function setReturnNumber($number)
    {
        $retNumber = $number;
        if ($this->ReturnLength > 0)
        {
            // 書式化して戻す
            $format = "%0{$this->ReturnLength}d";
            $retNumber = sprintf($format, $number);
        }
        
        return $retNumber;
    }
}
