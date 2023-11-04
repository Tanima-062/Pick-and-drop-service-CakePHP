<?php echo $Reservation['last_name'].' '.$Reservation['first_name']; ?>様

スカイチケットレンタカーでございます。
この度はご利用いただき誠にありがとうございます。

レンタカーご予約内容変更につき追加のご入金が必要となります。
本メールを必ず最後までご確認の上、期限内にご入金手続きをお願いいたします。

■ お支払い期限

<?php echo $payment_limit_datetime; ?>


■ お支払い金額・お支払い方法

▼下記マイページより金額をご確認のうえ、ご入金のお手続きをお願いいたします。
https://<?php echo $domain; ?>/rentacar/mypages/login/?hash=<?php echo $Reservation['reservation_hash']; ?>


■ ご注意
※ お支払い期限を過ぎると、ご予約は自動的にキャンセルとなります。  
※ ご入金方法はクレジットカードでの決済のみとなります。
※ ご入金手続きが完了した時点で、追加代金についてもキャンセル手数料の対象となります。

※ このメールは自動配信されています。
　 このメールに返信してのお問い合わせ等にはお応えできません。


お客様からのご入金お手続きをお待ちしております。

スカイチケットレンタカー
―――――――――――――――――――――――――――――
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?>

（観光庁長官登録旅行業第2035号）

【お問い合せ先】
<?php echo RENTACAR_CONTACT_URL; ?>


―――――――――――――――――――――――――――――