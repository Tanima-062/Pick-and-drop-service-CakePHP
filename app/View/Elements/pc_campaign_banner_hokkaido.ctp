<?php
	$ymd = date('ymd');
	$url = $_SERVER['REQUEST_URI'];
	$is_hokkaido = strstr($url, '/rentacar/hokkaido/');
	if (($ymd >= 210917 && $ymd <= 211231) && ($is_hokkaido)) {
?>

<img src="/rentacar/img/campaign/campaign_hokkaidrive2021_pc.png" alt="HOKKAIDrive Campaign 2021" class="campaign_banner">

<?php
	}
?>
