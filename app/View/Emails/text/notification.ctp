<?php echo $status; ?>のお知らせです。
<?php echo $notification_detail; ?>

--------------------------------------
■お客様情報
ステータス：<?php echo $status; ?>

予約番号：<?php echo $reservation_key; ?>

ご利用者名：<?php echo $last_name.' '.$first_name; ?>

電話番号：<?php echo $tel; ?>

メールアドレス：<?php echo $email; ?>


■ご予約詳細
プラン名：<?php echo $commodity_name; ?>

車両タイプ / クラス：<?php echo $car_type; ?> <?php echo $car_class; ?>

受取店舗：<?php echo $rent_office_name; ?>

受取日時：<?php echo html_entity_decode($rent_date)."(".$rent_week.") ".html_entity_decode($rent_time); ?>

返却店舗：<?php echo $return_office_name; ?>

返却日時：<?php echo html_entity_decode($return_date)."(".$return_week.") ".html_entity_decode($return_time); ?>

ご利用人数：大人<?php echo $adults_count; ?>名<?php if (!empty($children_count)) { ?> 子供<?php echo $children_count; ?>名<?php } ?><?php if (!empty($infants_count)) { ?> 幼児<?php echo $infants_count; ?>名<?php } ?>

予約車両台数：<?php echo $car_count; ?>台

合計料金：　<?php echo number_format($amount_minus_fee); ?>円
 - 基本料金：<?php echo $basic_price; ?>

<?php if (!empty($equipment_text)) { ?>
 - 標準装備：<?php echo html_entity_decode($equipment_text); ?>

<?php } ?>
<?php if (!empty($option_text)) { ?>
 - オプション：<?php echo html_entity_decode($option_text); ?>

<?php } ?>
<?php if (!empty($disclaimer)) { ?>
 - 免責補償料金：<?php echo $disclaimer; ?>

<?php } ?>
<?php if (!empty($night_fee)) { ?>
 - 深夜手数料：<?php echo $night_fee; ?>

<?php } ?>
<?php if (!empty($drop_off)) { ?>
 - 乗捨料金：<?php echo $drop_off; ?>

<?php } ?>

■決済情報
<?php if ($fromStep1) { ?>
支払い方法：現地精算
<?php } else { ?>
支払い方法：WEB事前決済
入金状況：入金済み
入金金額：<?php echo number_format($amount_minus_fee); ?>円
<?php } ?>
<?php if (!(empty($arrival_flight_number) && empty($departure_flight_number))) { ?>

■航空便情報
<?php   if (!empty($arrival_flight_number)) { ?>
到着便：<?php echo $arrival_flight_number; ?>

<?php   } ?>
<?php   if (!empty($departure_flight_number)) { ?>
出発便：<?php echo $departure_flight_number; ?>

<?php   } ?>
<?php } ?>

■問合せ
<?php echo mb_strimwidth($reservation_email,0,500,"..."); ?>



▼ご予約の詳細は管理画面にてご確認いただけます。
https://<?php echo $domain; ?>/rentacar/client/Reservations/edit/<?php echo $reservation_id; ?>

―――――――――――――――――――――――――――――
株式会社アドベンチャー　スカイチケットレンタカー

〒<?php echo COMPANY_ZIP; ?>

<?php echo COMPANY_ADDRESS; ?>

事業者様専用tel  :<?php echo ADV_SETTLEMENT_TEL; ?>

お客様専用tel  :<?php echo DISPLAY_RENTACAR_TEL; ?>

mail :<?php echo EMAIL_ADDRESS_RENTACAR; ?>

URL  :https://<?php echo $domain; ?>/rentacar/
―――――――――――――――――――――――――――――