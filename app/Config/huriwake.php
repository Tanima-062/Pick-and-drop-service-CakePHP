<?php

function getSwitch() {


	if (isset($_COOKIE['CakeCookie']['Switch'])) {
		return $_COOKIE['CakeCookie']['Switch'];
	} else {
		return false;
	}

}

function uaCheck()
{
    $ua = $_SERVER['HTTP_USER_AGENT'];

    # ｽﾏｰﾄﾌｫﾝ
    if(
           (ereg("iPhone", $ua))
        || (ereg("Android", $ua))
        || (ereg("Windows Phone", $ua))
        || (ereg("BlackBerry", $ua))
    )
    {
		//AndroidのタブレットはPCページにする
		if (ereg("Android", $ua) && strpos($ua,'Mobile') === FALSE){
            $flg = 'pc';
		} else {
            $flg = 'smartphone';
		}
    }
    # ﾓﾊﾞｲﾙ
    elseif(
           (ereg("DoCoMo", $ua))
        || (ereg("SoftBank", $ua))
        || (ereg("Vodafone", $ua))
        || (ereg("J-PHONE", $ua))
        || (ereg("UP.Browser", $ua))
        || (ereg("KDDI", $ua))
        || (ereg("WILLCOM", $ua))
        || (ereg("DDIPOCKET", $ua))
        || (ereg("PDXGW", $ua))
        || (ereg("Googlebot-Mobile", $ua))
        || (ereg("Y!J", $ua))
        || (ereg("LD_mobile_bot", $ua))
        || (ereg("moba-crawler", $ua))
        || (ereg("RFCrawler-Mobile", $ua))
        || (ereg("froute.jp", $ua))
        || (ereg("ichiro", $ua))
    )
    {
        $flg = 'mobile';
    }
    # PC
    else
    {
        $flg = 'pc';
    }

    return $flg;
}


?>
