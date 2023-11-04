<h3>
以下の内容でメールを再送しますか？
</h3>
<br/>
<div>
	<?php
	echo $this->Html->link('予約完了メールを再送する','/Reservations/again_mail/'.$this->params->pass[0],array('class'=>'btn btn-success','style'=>'margin-right:35px;'));
	?>


	<button onclick="history.back();" class="btn btn-warning"> 戻る </button>
</div>
<br/>
件名：【再送】【skyticket】レンタカー予約完了のお知らせ
<br/>
ーーーーーーーーーーーーーーーーーーーーーーーーーーーー本文ーーーーーーーーーーーーーーーーーーーーーーーーーーーー<br/>
<?php echo $params['Reservation']['last_name'].'　'.$params['Reservation']['first_name']; ?>様<br/>
<br/>
格安レンタカー予約サイトスカイチケットでございます。<br/>
この度は、レンタカーをご予約いただき誠にありがとうございます。<br/>
<br/>
つきましては、ご予約のレンタカー情報と、ご利用方法をお送りいたします。<br/>
<br/>
＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝<br/>
●ご利用のレンタカー会社<br/>
　店舗名：<?php echo $params['Client']['name']; ?>　<?php echo $params['RentOffices']['name']; ?><br/>
　連絡先：<?php echo $params['RentOffices']['tel']; ?><br/>
<?php if (!empty($params['RentOffices']['rent_meeting_info'])) { ?>
<br/>
●受取時の送迎や待ち合わせについて
<?php echo nl2br($params['RentOffices']['rent_meeting_info']); ?><br/>
<?php } ?>
<br/>
<?php if (!empty($params['RentOffices']['rent_office_notification'])) { ?>
●ご利用の店舗からのご案内
<?php echo nl2br($params['RentOffices']['rent_office_notification']); ?><br/>
<?php } ?>
<br/>
＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝<br/>
※このメールは自動配信となっております。<br/>
　レンタカーに関するお問合せは、ご予約確認ページから、<br/>
　もしくはご利用予定の店舗へご連絡をお願いします。<br/>
<br/>
▼ご予約確認はこちら<br/>
https://<?php echo $params['domain']; ?>/mypages/<?php echo $params['Reservation']['reservation_hash']; ?><br/>
＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝－＝<br/>
<br/>
■予約番号<br/>
　<?php echo $params['Reservation']['reservation_key']; ?><br/>
■ご利用者名<br/>
　<?php echo $params['Reservation']['last_name'].'　'.$params['Reservation']['first_name']; ?>様<br/>
■ご利用日時<br/>
　受取：<?php echo $params['Reservation']['rent_date']."(".$params['Reservation']['rent_week'].") ".$params['Reservation']['rent_time']; ?><br/>
　返却：<?php echo $params['Reservation']['return_date']."(".$params['Reservation']['return_week'].") ".$params['Reservation']['return_time']; ?><br/>
■レンタカー会社名<br/>
　<?php echo $params['Client']['name']; ?><br/>
■プラン名<br/>
　<?php echo $params['Commodity']['name']; ?><br/>
■車両タイプ<br/>
　<?php echo $params['CarType']['name']; ?><br/>
　<?php if (isset($params['Commodity']['smoking_flg'])) {
    switch($params['Commodity']['smoking_flg']) {
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
?><br/>
■ご利用人数<br/>
　大人<?php echo $params['Reservation']['adults_count']; ?>名<br/>
<?php if (!empty($params['Reservation']['children_count'])) { ?>
　子供<?php echo $params['Reservation']['children_count']; ?>名<br/>
<?php } ?>
<?php if (!empty($params['Reservation']['infants_count'])) { ?>
　幼児<?php echo $params['Reservation']['infants_count']; ?>名<br/>
<?php } ?>
<br/>
■受取店舗・連絡先／住所<br/>
　店舗名：<?php echo $params['Client']['name']; ?>　<?php echo $params['RentOffices']['name']; ?><br/>
　連絡先：<?php echo $params['RentOffices']['tel']; ?><br/>
　営業時間：<?php echo date('G:i',strtotime($params['RentOffices']['office_hours_from'])).'~'.date('G:i',strtotime($params['RentOffices']['office_hours_to'])); ?><br/>
<?php if (!empty($params['RentOffices']['address'])) { ?>
　住所：<?php echo $params['RentOffices']['address']; ?><br/>
<?php } ?>
<?php if (!empty($params['RentOffices']['access_dynamic'])) { ?>
　アクセス：<?php echo $params['RentOffices']['access_dynamic']; ?><br/>
<?php } ?>
<?php if (!empty($params['RentOffices']['rent_meeting_info'])) { ?>
<br/>
【受取時の送迎や待ち合わせについて】<br />
<?php echo nl2br($params['RentOffices']['rent_meeting_info']); ?><br/>
<?php } ?>
<br/>
■返却店舗・連絡先／住所<br/>
　店舗名：<?php echo $params['Client']['name']; ?>　<?php echo $params['ReturnOffices']['name']; ?><br/>
　連絡先：<?php echo $params['ReturnOffices']['tel']; ?><br/>
　営業時間：<?php echo date('G:i',strtotime($params['ReturnOffices']['office_hours_from'])).'~'.date('G:i',strtotime($params['ReturnOffices']['office_hours_to'])); ?><br/>
<?php if (!empty($params['ReturnOffices']['address'])) { ?>
　住所：<?php echo $params['ReturnOffices']['address']; ?><br/>
<?php } ?>
<?php if (!empty($params['ReturnOffices']['access_dynamic'])) { ?>
　アクセス：<?php echo $params['ReturnOffices']['access_dynamic']; ?><br/>
<?php } ?>
<?php if (!empty($params['ReturnOffices']['return_meeting_info'])) { ?>
<br/>
【返却時の送迎や待ち合わせについて】<br />
<?php echo nl2br($params['ReturnOffices']['return_meeting_info']); ?><br/>
<?php } ?>
<br/>
■予約車両台数<br/>
　<?php echo $params['Reservation']['cars_count']; ?>台<br/>
■標準装備<br/>
　<?php echo $params['equipment_text']; ?><br/>
<?php if (!empty($params['ReservationChildSheet']) || !empty($params['ReservationPrivilege']) || !empty($params['ReservationDetail'])) { ?>
■オプション<br/>
<?php   if (!empty($params['ReservationDetail'])) { ?>
　<?php   foreach ($params['ReservationDetail'] as $key => $value) { ?>
<?php echo $value; ?>
<?php     } ?><br>
<?php   } ?>
<?php   if (!empty($params['ReservationChildSheet'])) { ?>
　<?php   foreach ($params['ReservationChildSheet'] as $childSheet) { ?>
<?php echo $childSheet.'台'; ?>
<?php     } ?><br/>
<?php   } ?>
<?php   if (!empty($params['ReservationPrivilege'])) { ?>
　<?php   foreach ($params['ReservationPrivilege'] as $privilege) { ?>
<?php echo $privilege; ?>
<?php     } ?><br/>
<?php   } ?>
<?php } ?>
<br/>
■予約時合計金額<br/>
　<?php echo number_format($params['Reservation']['amount']); ?>円<br/>
<br/>
■お支払情報<br/>
<?php if ($params['Reservation']['fromStep1']) { ?>
支払い方法：当日店舗でお支払いください<br/>
<?php } else { ?>
支払い方法：WEB事前決済<br/>
ご入金状況：入金済み<br/>
ご入金金額：<?php echo number_format($params['Reservation']['amount']); ?>円<br/>
<?php } ?>
<?php if (!empty($params['Reservation']['arrival_flight_number'])) { ?>
<br/>
■ご到着便<br/>
　<?php echo $params['Reservation']['arrival_flight_number']; ?><br/>
<?php } ?>
<?php if (!empty($params['Reservation']['departure_flight_number'])) { ?>
<br/>
■ご出発便<br/>
　<?php echo $params['Reservation']['departure_flight_number']; ?><br/>
<?php } ?>
<?php if (isset($params['ReservationMail']['contents'])) { ?>
<br/>
■備考<br/>
<?php echo nl2br($params['ReservationMail']['contents']); ?><br/>
<?php } ?>
<br/>
--------------------------------------<br/>
<?php if (!empty($params['Client']['reservation_content'])) { ?>
<br/>
■<?php echo $params['Client']['name']; ?>からのご案内
<?php echo nl2br($params['Client']['reservation_content']); ?><br/>
<?php } ?>
<br/>
■キャンセルポリシー<br/>
<?php if ($params['Reservation']['fromStep1']) { ?>
<?php echo $params['CancelPolicy']; ?><br/>
・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br>
<?php echo nl2br($params['Client']['cancel_policy']); ?><br/>
<?php } else { ?>
〈キャンセル料〉<br/>
<?php echo $params['CancelPolicy']; ?><br/>
・予約時間を１時間以上過ぎてもご連絡のない場合は、キャンセルとして処理させていただきます。<br/>
・無連絡キャンセルの場合、ご返金はいたしかねますのでご了承ください。<br/>
<br/>
■キャンセルポリシーに関するお知らせ<br/>
<?php echo nl2br($params['Client']['cancel_policy']); ?><br/>
<?php } ?>
<br/>
▼ご予約のキャンセル／お問い合わせはこちら<br/>
https://<?php echo $params['domain']; ?>/mypages/<?php echo $params['Reservation']['reservation_hash']; ?><br/>
<br/>
スカイチケットレンタカー<br/>
───────────────────────────────────────────────<br/>
運営会社：<?php echo ADV_COMPANY_NAME_JAPANESE; ?><br/>
（観光庁長官登録旅行業第2035号）<br/>
<br/>
【お問い合せ先】<br/>
<?php echo RENTACAR_CONTACT_URL; ?><br/>
<br/>
―――――――――――――――――――――――――――――<br/>
ーーーーーーーーーーーーーーーーーーーーーーーーーーーー本文ーーーーーーーーーーーーーーーーーーーーーーーーーーーー<br/>
<br/>
<div>
	<?php
	echo $this->Html->link('予約完了メールを再送する','/Reservations/again_mail/'.$this->params->pass[0],array('class'=>'btn btn-success','style'=>'margin-right:35px;'));
	?>

	<button onclick="history.back();" class="btn btn-warning"> 戻る </button>
</div>