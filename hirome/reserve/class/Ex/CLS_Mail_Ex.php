<?php
// メール送信クラス
class CLS_Mail_Ex
{
    /********************************************************************/
    /* public Method                                                    */
    /********************************************************************/

    
    // 予約メール
    public function SendOrder($addressTo,$order)
    {
        // 予約メール送信
        $blnRet = $this->SendInformationMail($addressTo,$order);
        
        return $blnRet;
    }
    
    // ユーザーへの仮登録確認メールメール
    public function SendUser($addressTo,$order)
    {
        // 予約メール送信
        $blnRet = $this->SendUsernMail($addressTo,$order);
        
        return $blnRet;
    }
    
    /********************************************************************/
    /* Private Method                                                   */
    /********************************************************************/
    
    // 予約確定メール
    private function SendInformationMail($addressTo, $order)
    {
        $retFlag = true;
        $errCode = 1;
        

        // from 
        $from = "info@hiromezen.co.jp";
        
        $message = "*****************************************************" . "\n";
    	$message .= "" . "\n";
    	$message .= "予約システムより注文予約がありました。" . "\n";
    	$message .= "内容をお確かめください。" . "\n";
    	$message .= "" . "\n";
    	$message .= "*****************************************************" . "\n";
		$message .= "" . "\n";
		
		
    	$message .= "＜お客様情報＞" . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "【お名前】" . $_SESSION["i"]["name"] . " 様" . "\n";
    	$message .= "【かな】" . $_SESSION["i"]["kana"] . " 様" . "\n";
    	$message .= "【お届け先郵便番号】" . $_SESSION["i"]["zipcode"] . "\n";
    	$message .= "【お届け先ご住所】" . $_SESSION["i"]["address"] . "\n";
    	$message .= "【お届け日時】" . $_SESSION["i"]["delivery_date_disp"] . " " . $_SESSION["i"]["delivery_time_disp"] . "\n";
    	$message .= "【メールアドレス】" . $_SESSION["i"]["mail"] . "\n";
    	$message .= "【お電話番号】" . $_SESSION["i"]["tel"] . "\n";
    	$message .= "【ご依頼事項】" . "\n" . $_SESSION["i"]["note"] . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "" . "\n";
    	$message .= "＜注文内容＞" . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= $order;
    	$message .= "" . "\n";
    	$message .= "【合計金額】" . "\n" . $_SESSION["i"]["total_price"] ."円"  . "\n";
    	$message .= "【税込合計金額】" . "\n" . $_SESSION["i"]["total_taxprice"] ."円"  . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "" . "\n";
    	$message .= "" . "\n";
    	
        // 

        $additional_headers = "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
        $additional_headers .= "From: {$from}\n";
        

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        if( $retFlag === true)
        {
            $retFlag = mb_send_mail($addressTo, 'HP予約システムよりご注文が入りました', $message, $additional_headers );
        }

        return $retFlag;
    }
    
    // ユーザー返信メール
    private function SendUsernMail($addressTo, $order)
    {
        $retFlag = true;
        $errCode = 1;
        

        // from 
        $from = "info@hiromezen.co.jp";
        
        $message = "----------------------------------------------------------------------" . "\n";
        $message .= "このメールはお客様の注文に関する大切なメールです。" . "\n";
        $message .= "お取引が完了するまで大切に保存してください。" . "\n";
        $message .= "----------------------------------------------------------------------" . "\n";
        $message .= "" . "\n";
        
        $message .= "********************************************************************************************" . "\n";
       	$message .= "" . "\n";
    	$message .= "※注文はまだ確定されておりません" . "\n";
    	$message .= "  ご注文内容に関しまして、こちらから確認の電話後、注文が確定となります。" . "\n";
    	$message .= "" . "\n";
    	$message .= "  なにかご不明な点などございましたら、お電話でお問い合わせ下さい。" . "\n";
    	$message .= "  TEL 0120-51-2941 (受付時間 午前9:00 ～ 午後5:00)" . "\n";
    	$message .= "" . "\n";
    	$message .= "*******************************************************************************************" . "\n";
		$message .= "" . "\n";
		
		
    	$message .= "＜お客様情報＞" . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "【お名前】" . $_SESSION["i"]["name"] . " 様" . "\n";
    	$message .= "【かな】" . $_SESSION["i"]["kana"] . " 様" . "\n";
    	$message .= "【お届け先郵便番号】" . $_SESSION["i"]["zipcode"] . "\n";
    	$message .= "【お届け先ご住所】" . $_SESSION["i"]["address"] . "\n";
    	$message .= "【お届け日時】" . $_SESSION["i"]["delivery_date_disp"] . " " . $_SESSION["i"]["delivery_time_disp"] . "\n";
    	$message .= "【メールアドレス】" . $_SESSION["i"]["mail"] . "\n";
    	$message .= "【お電話番号】" . $_SESSION["i"]["tel"] . "\n";
    	$message .= "【ご依頼事項】" . "\n" . $_SESSION["i"]["note"] . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "" . "\n";
    	$message .= "＜注文内容＞" . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= $order;
    	$message .= "" . "\n";
    	$message .= "【合計金額】" . "\n" . $_SESSION["i"]["total_price"] ."円"  . "\n";
    	$message .= "【税込合計金額】" . "\n" . $_SESSION["i"]["total_taxprice"] ."円"  . "\n";
    	$message .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━" . "\n";
    	$message .= "" . "\n";
    	$message .= "" . "\n";
    	
        // 

        $additional_headers = "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n";
        $additional_headers .= "From: {$from}\n";
        

        mb_language("Japanese");
        mb_internal_encoding("UTF-8");

        if( $retFlag === true)
        {
            $retFlag = mb_send_mail($addressTo, '【ヒロメ】ご注文仮確定メール ※こちらからの電話確認にて注文が確定となります', $message, $additional_headers );
        }

        return $retFlag;
    }
}
