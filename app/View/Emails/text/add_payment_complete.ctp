<?php echo $last_name.' '.$first_name; ?>様

格安レンタカー予約サイトスカイチケットでございます。
この度は、レンタカーをご予約いただき誠にありがとうございます。

追加代金のご入金が確認できましたのでご連絡申しあげます。
つきましては、改めてご予約のレンタカー情報と、ご利用方法をお送りいたします。

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
●ご利用のレンタカー会社
　店舗名：<?php echo $client_name; ?>　<?php echo $rent_office_name; ?>

　連絡先：<?php echo $rent_office_tel; ?>

<?php if (!empty($rent_meeting_info)) { ?>

●受取時の送迎や待ち合わせについて
<?php echo $rent_meeting_info; ?>

<?php } ?>

<?php if (!empty($rent_office_notification)) { ?>
●ご利用の店舗からのご案内
<?php echo $rent_office_notification; ?>

<?php } ?>

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝
※このメールは自動配信となっております。
　レンタカーに関するお問合せは、ご予約確認ページから、
　もしくはご利用予定の店舗へご連絡をお願いします。

▼ご予約確認はこちら
https://<?php echo $domain; ?>/rentacar/mypages/login/?hash=<?php echo $reservation_hash; ?>

＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝

■予約番号
　<?php echo $reservation_key; ?>

■ご利用者名
　<?php echo $last_name.' '.$first_name; ?>様

■ご利用日時
　受取：<?php echo html_entity_decode($rent_date)."(".$rent_week.") ".html_entity_decode($rent_time); ?>

　返却：<?php echo html_entity_decode($return_date)."(".$return_week.") ".html_entity_decode($return_time); ?>

■レンタカー会社名
　<?php echo $client_name; ?>

■プラン名
　<?php echo $commodity_name; ?>

■車両タイプ
　<?php echo $car_type; ?>

　<?php if (isset($car_smoking_flg)) {
    switch($car_smoking_flg) {
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
　大人<?php echo $adults_count; ?>名
<?php if (!empty($children_count)) { ?>
　子供<?php echo $children_count; ?>名
<?php } ?>
<?php if (!empty($infants_count)) { ?>
　幼児<?php echo $infants_count; ?>名
<?php } ?>

■受取店舗・連絡先／住所
　店舗名：<?php echo $client_name; ?>　<?php echo $rent_office_name; ?>

　連絡先：<?php echo $rent_office_tel; ?>

　営業時間：<?php echo date('G:i',strtotime($rent_office_hours_from)).'~'.date('G:i',strtotime($rent_office_hours_to)); ?>

　住所：<?php echo $rent_office_address; ?>

　アクセス：<?php echo $rent_office_access; ?>

<?php if (!empty($rent_meeting_info)) { ?>

【受取時の送迎や待ち合わせについて】
<?php echo $rent_meeting_info; ?>

<?php } ?>

■返却店舗・連絡先／住所
　店舗名：<?php echo $client_name; ?>　<?php echo $return_office_name; ?>

　連絡先：<?php echo $return_office_tel; ?>

　営業時間：<?php echo date('G:i',strtotime($return_office_hours_from)).'~'.date('G:i',strtotime($return_office_hours_to)); ?>

　住所：<?php echo $return_office_address; ?>

　アクセス：<?php echo $return_office_access; ?>

<?php if (!empty($return_meeting_info)) { ?>

【返却時の送迎や待ち合わせについて】
<?php echo $return_meeting_info; ?>

<?php } ?>

■予約車両台数
　<?php echo $car_count; ?>台
■標準装備
　<?php echo html_entity_decode($equipment_text); ?>

■オプション
　<?php echo html_entity_decode($option_list); ?>

■合計料金
　<?php echo number_format($amount); ?>円

■お支払い情報
支払い方法：WEB事前決済
ご入金状況：入金済み（追加徴収分決済済み）
ご入金金額：<?php echo number_format($amount); ?>円
<?php if (!empty($arrival_flight_number)) { ?>

■ご到着便
　<?php echo $arrival_flight_number; ?>

<?php } ?>
<?php if (!empty($departure_flight_number)) { ?>

■ご出発便
　<?php echo $departure_flight_number; ?>

<?php } ?>
<?php if (!empty($client_reservation_content)) { ?>

■<?php echo $client_name; ?>からのご案内
<?php echo $client_reservation_content; ?>

<?php } ?>

■キャンセルポリシー
〈キャンセル料〉
<?php echo $cancel_policy; ?>

・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。
・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。

■キャンセルポリシーに関するお知らせ
<?php echo $client_cancel_policy; ?>

<?php if (strncmp($advertising_cd, "dtravel", 7) == 0){ ?>

【レンタカー利用でdポイントがもらえる！】
ご利用額の1％ポイント進呈中！詳しくはこちら
https://travel.dmkt-sp.jp/sp/info/point_cpn/car_sky.html?utm_source=skyticket&utm_medium=bcpage&utm_content=car&utm_campaign=skyticket&p1=<?php echo $reservation_key; ?>

<?php } ?>

▼ご予約のキャンセル／お問い合わせはこちら
https://<?php echo $domain; ?>/rentacar/mypages/login/?hash=<?php echo $reservation_hash; ?>


スカイチケットレンタカー
―――――――――――――――――――――――――――――
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?>

（観光庁長官登録旅行業第2035号）

【お問い合せ先】
<?php echo RENTACAR_CONTACT_URL; ?>


―――――――――――――――――――――――――――――