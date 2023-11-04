<?php echo $last_name.'　'.$first_name; ?>様

スカイチケットレンタカーでございます。
お客様のご予約内容を下記の通り変更いたしました。


■予約番号
<?php echo $reservation_key; ?>

■ご利用者名
<?php echo $last_name.'　'.$first_name; ?>

■メールアドレス
<?php echo $email; ?>

<?php if ($edit_tel) { ?>
■電話番号
<?php echo $tel; ?>

<?php } ?>
<?php if (!(empty($arrival_flight_number) && empty($departure_flight_number))) { ?>

<?php   if (!empty($arrival_flight_number)) { ?>
■到着便
<?php echo $arrival_flight_number; ?>

<?php   } ?>
<?php   if (!empty($departure_flight_number)) { ?>
■出発便
<?php echo $departure_flight_number; ?>

<?php   } ?>
<?php } ?>

■プラン名
<?php echo $commodity_name; ?>


■ご利用人数
　大人<?php echo $adults_count; ?>名
<?php if (!empty($children_count)) { ?>
　子供<?php echo $children_count; ?>名
<?php } ?>
<?php if (!empty($infants_count)) { ?>
　幼児<?php echo $infants_count; ?>名
<?php } ?>
<?php if (!empty($option_list)) { ?>

■オプション
<?php echo html_entity_decode($option_list); ?>

<?php } ?>

■受取店舗
<?php echo $rent_office_name; ?>

■返却店舗
<?php echo $return_office_name; ?>

<?php if (!empty($reservation_email)) { ?>

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