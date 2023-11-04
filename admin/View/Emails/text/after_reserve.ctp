<?php echo $status; ?>のお知らせです。
<?php echo $notificationDetail; ?>

--------------------------------------
<?php if (isset($changeDetail)) { ?>

<?php   foreach ($changeDetail as $change) { ?>
<?php echo $change; ?>

<?php   } ?>

----------------------------------------------------------------------------
<?php } ?>
<?php if (strcmp($status,'キャンセル') == 0) { ?>

<?php   if (isset($cancel_remark) && !empty($cancel_remark)) { ?>
【キャンセル理由】
<?php echo $cancelReason; ?>

【キャンセル理由詳細】
<?php echo mb_strimwidth($cancel_remark,0,50,'...'); ?>

<?php   } else { ?>
【キャンセル理由】
未記入
<?php   } ?>
<?php } ?>

■ご予約情報
予約番号：<?php echo $reservation_key; ?>

ご利用者名：<?php echo $last_name.'　'.$first_name; ?>

メールアドレス：<?php echo $email; ?>

電話番号：<?php echo $tel; ?>


プラン名：<?php echo $commodity_name; ?>

車両タイプ / クラス：<?php echo $car_type; ?> <?php echo $car_class; ?>

ご利用人数：大人<?php echo $adults_count; ?>名<?php if (!empty($children_count)) { echo ' 子供'.$children_count.'名'; } if (!empty($infants_count)) { echo ' 幼児'.$infants_count.'名'; } ?>

受取店舗：<?php echo $rent_office_name; ?>

受取日時：<?php echo html_entity_decode($rent_date)."(".$rent_week.") ".html_entity_decode($rent_time); ?>

返却店舗：<?php echo $return_office_name; ?>

返却日時：<?php echo html_entity_decode($return_date)."(".$return_week.") ".html_entity_decode($return_time); ?>

<?php if (strcmp($status,'お問い合わせ') != 0) { ?>

<?php   if (!empty($arrival_flight_number)) { ?>
到着便：<?php echo $arrival_flight_number; ?>

<?php   } ?>
<?php   if (!empty($departure_flight_number)) { ?>
出発便：<?php echo $departure_flight_number; ?>

<?php   } ?>
<?php } ?>


<?php if (isset($contents)) { ?>
■問合せ
<?php echo mb_strimwidth($contents,0,500,'...'); ?>



<?php } ?>
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