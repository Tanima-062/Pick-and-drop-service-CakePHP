<?php
class UserInfoControl {

	public function getDevice($ua){

		# ｽﾏｰﾄﾌｫﾝ
		if(
		(preg_match("/iPhone/", $ua))
		|| (preg_match("/Android/", $ua))
		|| (preg_match("/Windows Phone/", $ua))
		|| (preg_match("/BlackBerry/", $ua))
		)
		{
			$flg = 'スマートフォン';
		}
		# ﾓﾊﾞｲﾙ
		elseif(
		(preg_match("/DoCoMo/", $ua))
		|| (preg_match("/SoftBank/", $ua))
		|| (preg_match("/Vodafone/", $ua))
		|| (preg_match("/J-PHONE/", $ua))
		|| (preg_match("/UP.Browser/", $ua))
		|| (preg_match("/KDDI/", $ua))
		|| (preg_match("/WILLCOM/", $ua))
		|| (preg_match("/DDIPOCKET/", $ua))
		|| (preg_match("/PDXGW/", $ua))
		|| (preg_match("/Googlebot-Mobile/", $ua))
		|| (preg_match("/Y!J/", $ua))
		|| (preg_match("/LD_mobile_bot/", $ua))
		|| (preg_match("/moba-crawler/", $ua))
		|| (preg_match("/RFCrawler-Mobile/", $ua))
		|| (preg_match("/froute.jp/", $ua))
		|| (preg_match("/ichiro/", $ua))
		)
		{
			$flg = 'モバイル';
		}
		# PC
		else
		{
			$flg = 'PC';
		}
		return $flg;

	}


}

?>