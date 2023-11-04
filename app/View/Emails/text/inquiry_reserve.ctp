<?php echo $last_name.'　'.$first_name; ?>様

スカイチケットレンタカーでございます。
下記の内容でお問合せを承りました。
<?php echo $client_name; ?>様のご返信をお待ち下さい。

<?php if (isset($contents)) { ?>
▼お問い合わせ内容
<?php echo $reservation_email; ?>


<?php } ?>
▼ご予約内容の確認はこちら
https://<?php echo $domain; ?>/rentacar/mypages/login/?hash=<?php echo $reservation_hash; ?>


スカイチケットレンタカー
―――――――――――――――――――――――――――――
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?>

（観光庁長官登録旅行業第2035号）

【お問い合せ先】
<?php echo RENTACAR_CONTACT_URL; ?>


―――――――――――――――――――――――――――――