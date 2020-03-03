<?php
require_once(dirname(__FILE__) . "/../../lib/phpmailer/PHPMailerAutoload.php");

// メール送信クラス
class CLS_Mail_Ex
{
    // システムパラメーター
    protected $dir_offset;
    
    // メール処理用
    protected $mailFrom;
    protected $mailSubject;
    protected $mailMessage;
    protected $mailTo;
    protected $mailCc;
    protected $mailBcc;
    
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/
    // コンストラクタ
    public function __construct($dir_offset)
    {
        // 相対パスの階層
        $this->dir_offset = $dir_offset;
        
        // 継承先クラスでセットされない可能性を考慮して
        // 最低限必要な内容を初期化する
        $this->mailFrom = "xxxxx@xxxx.xxx";
        $this->mailSubject = "";
        $this->mailMessage = "";
        $this->mailTo = "";
        $this->mailCc = "";
        $this->mailBcc = "";
    }
    
    // 送信元メールアドレスのセット処理
    public function setFromMailAddress($mailFromAddress)
    {
        $this->mailFrom = $mailFromAddress;
    }
    
    // 送信先Ccアドレスのセット処理
    public function setCcMailAddress($mailCc)
    {
        $this->mailCc = $mailCc;
    }
    
    // 送信先Bccアドレスのセット処理
    public function setBccMailAddress($mailBcc)
    {
        $this->mailBcc = $mailBcc;
    }
    
    // 送信メールの題名のセット処理
    public function setSubject($subject)
    {
        $this->mailSubject = $subject;
    }
    
    // 送信メッセージの取得処理
    public function getMessage()
    {
        return $this->mailMessage;
    }
    
    // 送信メッセージのセット処理
    public function setMessage($message)
    {
        $this->mailMessage = $message;
    }
    
    // メール送信クラス
    public function sendmail($mailTo)
    {
        // スーパークラスのメール送信処理呼出し
//        $this->_sendMail($mailTo);
        $this->_sendSMTPMail($mailTo);
    }
    
    /********************************************************************/
    /* Protected Method                                                 */
    /********************************************************************/
    // メール本文の取得処理
    protected function setMessageTemplate($tpl_name)
    {
        $template = "{$this->dir_offset}template/tpl/mail/{$tpl_name}";
        $this->mailMessage = file_get_contents($template);
        
        // ついでにフッターテキストを置換する
        $this->setFooterVariable();
        
        return $this->mailMessage;
    }
    
    // フッターテンプレートの置換処理
    protected function setFooterVariable()
    {
        $template = "{$this->dir_offset}template/tpl/mail/footer.txt";
        $footer = file_get_contents($template);
        
        $this->mailMessage = str_replace("++[sms_footer]", $footer, $this->mailMessage);
    }
    
    // メール送信処理
    protected function _sendMail($mailTo)
    {
        $this->mailTo = $mailTo;
        
        // テキストタイプ
        $additional_headers = "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
        $additional_headers .= "From: {$this->mailFrom}\n";
        
        // Bccのセットを行う場合
        if (strlen($this->mailBcc) > 0)
        {
            $additional_headers .= "Bcc: {$this->mailBcc}\n";
        }
        
        mb_language("Japanese");
        mb_internal_encoding("UTF-8");
        
        $retFlag = mb_send_mail($this->mailTo, $this->mailSubject, $this->mailMessage, $additional_headers );
        
        return $retFlag;
    }
    
    // メール送信処理(SMTP)
    protected function _sendSMTPMail($mailTo)
    {
        // メールサーバ情報(暫定)
        $smtp_user = "mailtest@imprest.co.jp";
        $smtp_password = "a_12345678";
        
        // メールクラスのインスタンス生成
        $clsMail = new PHPMailer();
        $clsMail->IsSMTP();
        $clsMail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
        $clsMail->SMTPAuth = true;
        $clsMail->CharSet = 'UTF-8';
        $clsMail->Host = "imprestweb.sakura.ne.jp";
        $clsMail->Port = 587;
        $clsMail->IsHTML(false);
        $clsMail->Username = $smtp_user;
        $clsMail->Password = $smtp_password; 
        $clsMail->SetFrom("info@binbar.jp");
        $clsMail->From     = "info@binbar.jp";
        $clsMail->Subject = $this->mailSubject;
        $clsMail->Body = $this->mailMessage;
        $clsMail->AddAddress($mailTo);
        
        // ヘッダー情報
        $clsMail->XMailer = "IMPREST-SYSTEM";
        
        if( !$clsMail->Send() )
        {
            $message  = "Message was not sent<br/ >";
            $message .= "Mailer Error: " . $mailer->ErrorInfo;
        } 
        else 
        {
            $message  = "Message has been sent";
        }
        
//        echo $message;
        return true;
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
}
