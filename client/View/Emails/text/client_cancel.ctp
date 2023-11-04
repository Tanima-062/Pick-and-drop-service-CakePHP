<?php echo $last_name.'　'.$first_name; ?> 様

この度はスカイチケットレンタカーをご利用いただき、誠にありがとうございます。

下記内容のご予約をキャンセルいたしましたのでご連絡いたします。
※このメールは自動配信のため、ご返信いただきましてもレンタカー会社へは届きません。

※キャンセル料に関する注意事項
● キャンセル料はレンタカー会社により異なります。
  詳細はマイページにてご確認ください。
https://<?php echo $domain; ?>/rentacar/mypages/login/?hash=<?php echo $reservation_hash; ?>

● 受取日当日のキャンセルの場合は直接レンタカー会社へのご連絡もお願いいたします。


■予約番号
　<?php echo $reservation_key; ?>


■ご利用者名
　<?php echo $last_name.'　'.$first_name; ?> 様

■レンタカー会社名
　<?php echo $client_name; ?>


■プラン名
　<?php echo $commodity_name; ?>


■ご利用日時
　受取：<?php echo $rent_date."(".$rent_week.") ".$rent_time; ?>

　返却：<?php echo $return_date."(".$return_week.") ".$return_time; ?>


■料金
　<?php echo number_format($amount); ?>円

■お支払い方法：<?php echo $from_step1 ? '現地精算' : 'WEB事前決済'; ?>

<?php if ($from_step1) { ?>
※現地精算でのご予約の場合は、ご利用料金の決済は発生しておりませんので、弊社およびレンタカー会社からご返金はございません。

また、弊社からキャンセル料や取消手続料の請求はございませんが、
レンタカー会社よりキャンセル料の請求がある可能性がございますので
請求の詳細についてはご利用のレンタカー店へお問い合わせいただきますようお願いいたします。

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
●ご利用のレンタカー会社
　店舗名：<?php echo $client_name.'　'.$rent_office_name; ?>

　連絡先：<?php echo $rent_office_tel; ?>

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
<?php } ?>

■キャンセルポリシー
<?php if ($from_step1) { ?>
<?php echo $cancel_policy; ?>


<?php echo $client_cancel_policy; ?>

<?php } else { ?>
〈キャンセル料〉
<?php echo $cancel_policy; ?>

・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。

■キャンセルポリシーに関するお知らせ
<?php echo $client_cancel_policy; ?>

<?php } ?>

またのご利用を心からお待ち申し上げております。

スカイチケットレンタカー
―――――――――――――――――――――――――――――
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?>

（観光庁長官登録旅行業第2035号）

【お問い合せ先】
<?php echo RENTACAR_CONTACT_URL; ?>


―――――――――――――――――――――――――――――