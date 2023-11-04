<?php echo $Reservation['last_name'].'　'.$Reservation['first_name']; ?>様

格安レンタカー予約サイトスカイチケットでございます。
この度は、レンタカーをご予約いただき誠にありがとうございます。

ご予約のレンタカー利用開始日が「３日後」となりましたので、
確認のためレンタカー情報と、ご利用方法を再度ご連絡いたします。

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
●ご利用のレンタカー会社
　店舗名：<?php echo $Client['name']; ?>　<?php echo $RentOffices['name']; ?>

　連絡先：<?php echo $RentOffices['tel']; ?>

<?php if (!empty($RentOffices['rent_meeting_info'])) { ?>

●受取時の送迎や待ち合わせについて
<?php echo $RentOffices['rent_meeting_info']; ?>

<?php } ?>

<?php if (!empty($RentOffices['rent_office_notification'])) { ?>
●ご利用の店舗からのご案内
<?php echo $RentOffices['rent_office_notification']; ?>

<?php } ?>

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
※このメールは自動配信となっております。
　レンタカーに関するお問合せは、ご利用予定の店舗へご連絡をお願いします。

※以下、スカイチケットにてご予約いただいた内容となります。
　ご予約後に直接レンタカー会社にご連絡いただいている場合、
　変更内容が反映されていない場合がございます。予めご了承ください。

▼ご予約確認はこちら
https://<?php echo $domain; ?>/rentacar/mypages/login?hash=<?php echo $Reservation['reservation_hash']; ?>

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝

■予約番号
　<?php echo $Reservation['reservation_key']; ?>

■ご利用者名
　<?php echo $Reservation['last_name'].'　'.$Reservation['first_name']; ?>様

■ご利用日時
　受取：<?php echo $Reservation['rent_date']."(".$Reservation['rent_week'].") ".$Reservation['rent_time']; ?>

　返却：<?php echo $Reservation['return_date']."(".$Reservation['return_week'].") ".$Reservation['return_time']; ?>

■レンタカー会社名
　<?php echo $Client['name']; ?>

■プラン名
　<?php echo $Commodity['name']; ?>

■車両タイプ
　<?php echo $CarType['name']; ?>

　<?php if (isset($Commodity['smoking_flg'])) {
    switch($Commodity['smoking_flg']) {
      case 0:
        echo '禁煙車';
        break;
      case 1:
        echo '喫煙車';
        break;
      case 2:
        echo '禁煙・喫煙の指定なし';
        break;
      }
    }
?>

■ご利用人数
　大人<?php echo $Reservation['adults_count']; ?>名
<?php  if (!empty($Reservation['children_count'])) { ?>
　子供<?php echo $Reservation['children_count']; ?>名
<?php  } ?>
<?php  if (!empty($Reservation['infants_count'])) { ?>
　幼児<?php echo $Reservation['infants_count']; ?>名
<?php  } ?>

 ■受取店舗・連絡先／住所
　店舗名：<?php echo $Client['name']; ?>　<?php echo $RentOffices['name']; ?>

　連絡先：<?php echo $RentOffices['tel']; ?>

　営業時間：<?php echo date('G:i',strtotime($RentOffices['office_hours_from'])).'~'.date('G:i',strtotime($RentOffices['office_hours_to'])); ?>

<?php if (!empty($RentOffices['address'])) { ?>
　住所：<?php echo $RentOffices['address']; ?>

<?php } ?>
<?php if (!empty($RentOffices['access_dynamic'])) { ?>
　アクセス：<?php echo $RentOffices['access_dynamic']; ?>

<?php } ?>
<?php if (!empty($RentOffices['rent_meeting_info'])) { ?>

【受取時の送迎や待ち合わせについて】
<?php echo $RentOffices['rent_meeting_info']; ?>

<?php } ?>

■返却店舗・連絡先／住所
　店舗名：<?php echo $Client['name']; ?>　<?php echo $ReturnOffices['name']; ?>

　連絡先：<?php echo $ReturnOffices['tel']; ?>

　営業時間：<?php echo date('G:i',strtotime($ReturnOffices['office_hours_from'])).'~'.date('G:i',strtotime($ReturnOffices['office_hours_to'])); ?>

<?php if (!empty($ReturnOffices['address'])) { ?>
　住所：<?php echo $ReturnOffices['address']; ?>

<?php } ?>
<?php if (!empty($ReturnOffices['access_dynamic'])) { ?>
　アクセス：<?php echo $ReturnOffices['access_dynamic']; ?>

<?php } ?>
<?php if (!empty($ReturnOffices['return_meeting_info'])) { ?>

【返却時の送迎や待ち合わせについて】
<?php echo $ReturnOffices['return_meeting_info']; ?>

<?php } ?>

■予約車両台数
　<?php echo $Reservation['cars_count']; ?>台
■標準装備
　<?php echo $equipment_text; ?>

<?php if (!empty($ReservationChildSheet) || !empty($ReservationPrivilege) || !empty($ReservationDetail)) { ?>
■オプション
<?php   if (!empty($ReservationDetail)) { ?>
　<?php   foreach ($ReservationDetail as $value) { ?>
<?php echo $value; ?>
<?php     } ?>

<?php   } ?>
<?php   if (!empty($ReservationChildSheet)) { ?>
　<?php   foreach ($ReservationChildSheet as $childSheet) { ?>
<?php echo $childSheet.'台'; ?>
<?php     } ?>

<?php   } ?>
<?php   if (!empty($ReservationPrivilege)) { ?>
　<?php   foreach ($ReservationPrivilege as $privilege) { ?>
<?php echo $privilege; ?>
<?php     } ?>

<?php   } ?>
<?php } ?>

■予約時合計金額
　<?php echo number_format($Reservation['amount']); ?>円

■お支払い情報
<?php if ($Reservation['fromStep1']) { ?>
支払い方法：当日店舗でお支払いください
<?php } else { ?>
支払い方法：WEB事前決済
ご入金状況：入金済み
ご入金金額：<?php echo number_format($Reservation['amount']); ?>円
<?php } ?>
<?php if (!empty($Reservation['arrival_flight_number'])) { ?>

■ご到着便
　<?php echo $Reservation['arrival_flight_number']; ?>

<?php } ?>
<?php if (!empty($Reservation['departure_flight_number'])) { ?>

■ご出発便
　<?php echo $Reservation['departure_flight_number']; ?>

<?php } ?>
<?php if (isset($ReservationMail['contents'])) { ?>

■備考
<?php echo $ReservationMail['contents']; ?>

<?php } ?>

--------------------------------------
<?php if (!empty($Client['reservation_content'])) { ?>

■<?php echo $Client['name']; ?>からのご案内
<?php echo $Client['reservation_content']; ?>

<?php } ?>

■キャンセルポリシー
<?php if ($Reservation['fromStep1']) { ?>
<?php echo $CancelPolicy; ?>

・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
<?php echo $Client['cancel_policy']; ?>

<?php } else { ?>
〈キャンセル料〉
<?php echo $CancelPolicy; ?>

・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。

■キャンセルポリシーに関するお知らせ
<?php echo $Client['cancel_policy']; ?>

<?php } ?>

▼ご予約のキャンセル／お問い合わせはこちら
https://<?php echo $domain; ?>/rentacar/mypages/login?hash=<?php echo $Reservation['reservation_hash']; ?>


スカイチケットレンタカー
―――――――――――――――――――――――――――――
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?>

（観光庁長官登録旅行業第2035号）

【お問い合せ先】
<?php echo RENTACAR_CONTACT_URL; ?>


―――――――――――――――――――――――――――――