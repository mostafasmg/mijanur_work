<?php
require_once("Ex/CLS_Mail_Ex.php");

// お問い合わせメール(ユーザー向け）送信クラス
class CLS_MailContactUser extends CLS_Mail_Ex
{
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($dir_offset)
    {
        // スーパークラスのコンストラクタ呼出し
        parent::__construct($dir_offset);
        
        // メールの題名
        $this->mailSubject = "【琉球びんがた泡盛ストリート】お問い合わせありがとうございます。";
        
        // メールの本文
        $this->setMessageTemplate("contact_user.txt");
        
        // 送信元アドレスを個別に変更する必要がある場合はここで上書き
        // 呼出元クラスからメソッド呼出しで更新しても良い
        $this->setFromMailAddress("info@binbar.jp");
    }
    
    // メール送信クラス
    public function sendmail($mailTo)
    {
        parent::sendmail($mailTo);
    }
    
    // メッセージ生成処理
    public function createMessage( $name,  $sex_disp, $age_disp, $prefectures_disp,  $tel, $mail, $ticket_qty, $ticket_qty2, $detail)
    {
        // メッセージの取得
        $message = $this->getMessage();
        
        // 氏名
        $message = str_replace("++[sms_name]", $name, $message);
        
        // 性別
        $message = str_replace("++[sms_sex]", $sex_disp, $message);
        
        // 年代
        $message = str_replace("++[sms_age]", $age_disp, $message);
        
        // お住まいの地域
        $message = str_replace("++[sms_prefectures]", $prefectures_disp, $message);
        
        // 連絡先
        $message = str_replace("++[sms_tel]", $tel, $message);
        
        // メールアドレス
        $message = str_replace("++[sms_mail]", $mail, $message);
        
        // チケット申し込み枚数
        $message = str_replace("++[sms_ticket_qty]", $ticket_qty, $message);
        $message = str_replace("++[sms_ticket_qty2]", $ticket_qty2, $message);
        
        // 内容
        $message = str_replace("++[sms_detail]", $detail, $message);
        
        // メッセージの更新
        $this->setMessage($message);
    }
}
