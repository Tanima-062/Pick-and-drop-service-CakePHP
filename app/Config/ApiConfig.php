<?php

$config['ApiConfig'] = array(
	'all'					 => true, // 全てのAPIのレスポンス

	// 個別のレスポンス(コントローラ毎)
	'ApiCors'				 => true, // ローカル環境CORS対応

	// メタサーチ系
	'Travelko'				 => true,
	'SkyscannerAirports'	 => false,
	'SkyscannerCities'		 => false,
	'SkyscannerPlans'		 => false,
	'SkyscannerShops'		 => false,
	'SkyscannerStations'	 => false,

	// クライアント系
	'BudgetCarClasses'		 => true,
	'BudgetStockGroups'		 => true,
	'BudgetStocks'			 => true,
	'BudgetCarModels'		 => true,
	'BudgetPlans'			 => true,
	'BudgetReservations'	 => true,
	'RennaviReservations'	 => true,
	'RennaviReserveSky'		 => true, // スカイレンタカー個別
	'RennaviReserveJnet'	 => true, // Jnet個別

	// 汎用API
	'SearchesApi'			 => true,
	'SearchItemsApi'		 => true,
	'PlansApi'				 => true,
	'ReservationsApi'		 => true,
	'ReservationsRetrieveApi' => true,
	'ApplicationsRetrieveApi' => true,

	// ツアー向けAPI
	'ItemApi'                => true,
	'PlanApi'                => true,
	'ReservationApi'         => true,
	'ReservationsDetailRetrieveApi' => true,

	// Ajax系
	'AjaxCurrentLocation'	 => true,
	'AjaxPlanInfo'			 => true,

	// yotpoクライアント登録処理
	'YotpoReceiveApi'        => true,

);
