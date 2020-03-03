<?php
require_once("Ex/CLS_Mail_Ex.php");

// お問い合わせメール送信クラス
class CLS_MailContact extends CLS_Mail_Ex
{
//    CONST TOADDRESS = "infoonly@onlycreate.net";  // メールの送信先
     CONST TOADDRESS = "sin.angya@gmail.com";  // テストメールの送信先
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($dir_offset)
    {
        // スーパークラスのコンストラクタ呼出し
        parent::__construct($dir_offset);
        
        // メールの題名
        $this->mailSubject = "【お問い合わせフォーム】よりお問い合わせがございました。";
        
        // メールの本文
        $this->setMessageTemplate("contact_info.txt");
        
        // 送信元アドレスを個別に変更する必要がある場合はここで上書き
        // 呼出元クラスからメソッド呼出しで更新しても良い
        $this->setFromMailAddress("info@imprest.co.jp");
    }
    
    // メール送信クラス
    public function sendmail($mailTo)
    {
        parent::sendmail($mailTo);
    }
    
    // メッセージ生成処理
    // このメソッドを送信クラスごとに改造する
    // 呼出元クラスでセットしても良いが。
    public function createMessage($gender, $name,  $kana, $company, $department, $official_position, $tel, $mail, $address, $detail)
    {
        // メッセージの取得
        $message = $this->getMessage();
        
        // 種別
        $message = str_replace("++[sms_gender]", $gender, $message);
        
        // 氏名
        $message = str_replace("++[sms_name]", $name, $message);
        
        // kana
        $message = str_replace("++[sms_kana]", $kana, $message);
        
        // 会社
        $message = str_replace("++[sms_company]", $company, $message);
        
        // 部署
        $message = str_replace("++[sms_department]", $department, $message);
        
        // 役職
        $message = str_replace("++[sms_official_position]", $official_position, $message);
        
        // 電話番号
        $message = str_replace("++[sms_tel]", $tel, $message);
        
        // メールアドレス
        $message = str_replace("++[sms_mail]", $mail, $message);
        
        // 住所
        $message = str_replace("++[sms_address]", $address, $message);
        
        // 内容
        $message = str_replace("++[sms_detail]", $detail, $message);
        
        // メッセージの更新
        $this->setMessage($message);
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
    // ループ処理
    private function SetLoopVariable($message, $table)
    {
        // リスト部分のテンプレート取得
        $block_start = "<!-- LOOP_START -->";
        $block_end   = "<!-- LOOP_END -->";
        preg_match("/".$block_start."(.*?)".$block_end."/s", $message, $matches);
        $row_template = ltrim($matches[1], "\r\n");
        
        $tobal_price = 0;
        $rows = "";
        $count = count($table);
        for ($i = 0; $i < $count; $i++)
        {
            $row = $row_template;
            
            // データの用意
            $itemname = $table[$i]["itemname"];
            $price = $table[$i]["price"];
            
            // データ置換
            $row = str_replace("++[sms_itemname]", $itemname, $row);
            $row = str_replace("++[sms_price]", $price, $row);
            
            $tobal_price = $tobal_price + $price;
            
            // データを溜め込む
            $rows .= $row;
        }
        
        // データ置換(リスト)
        $message = preg_replace("/".$block_start."(.*?)".$block_end."/s", $rows, $message);
        
        // トータル金額
        $message = str_replace("++[sms_total_price]", $tobal_price, $message);
        
        // リスト部分の置換文字列を削除
        $message = str_replace("<!-- LOOP_START -->", "", $message);
        $message = str_replace("<!-- LOOP_END -->", "", $message);
        
        return $message;
    }
}
