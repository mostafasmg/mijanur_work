<?php
require_once("Ex/CLS_InputChecker_Ex.php");

// 入力チェッククラス
class CLS_InputChecker extends CLS_InputChecker_Ex
{
    // ステータス番号(100番台を使用すること)
    const ERR_EXAMPLE_SYMBOL = 101;    // 記述例
    
    // チェック種別
    const EXAMPLE_SYMBOL = "isExampleSymbol";     // チェック作成例
     
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
    
    // 入力チェック追加例
    public function isExampleSymbol($str = "")
    {
        // チェック値をセット
        $work_str = $this->setWorkString($str);
        
        // 該当文字を含む場合にNGとする
        if (preg_match("/[.]/", $work_str)) 
        {
            $this->statusNumber = CLS_InputChecker_Ex::ERR_EXAMPLE_SYMBOL;
            return false;
        }
        else
        {
            return true;
        }
    }
}
