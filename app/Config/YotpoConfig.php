<?php

$config = array(
	'Yotpo' => array(
		'app_key' => 'nmzINpXtOIn4Lz4pyOs3cubcjEvVBGATSO1qsINs',
		// 'app_key' => (IS_PRODUCTION) ? 'nmzINpXtOIn4Lz4pyOs3cubcjEvVBGATSO1qsINs' : 'EbU0YEAxp9WpQXP8FSdqmnqqG4buWj8heb3GCzYQ', //testストア使用時用
		'app_secret' => 'HjsvxT4E4BhW33pK5oNiHkEdTfGy663IRQnMO1VD',
		// 'app_secret' => (IS_PRODUCTION) ? 'HjsvxT4E4BhW33pK5oNiHkEdTfGy663IRQnMO1VD' : 'sbtoZNYd50KCTL6YS46dT6UflMX3pTr3NpNOMeoE', //testストア使用時用
		'oauth_url' => 'https://api.yotpo.com/oauth/token',
		'api_url' => 'https://api.yotpo.com/apps/',
		'domain' => (IS_PRODUCTION) ? 'skyticket.jp' : 'jp.skyticket.jp',
		'is_active' => true,
		'is_ssl' => true,
		'api_limit' => 100,
	)
);
