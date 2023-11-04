<!-- <div class="actions"> <div class="span3">-->
<h3 id="list" style="background-color: #3A87AD; color:#FFF; text-align:center;">MENU</h3>
<div class="actions">

	<ul class="nav nav-list">
		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('ダッシュボード'), '/'); ?></li>
		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('予約一覧'), array('controller' => 'Reservations', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('キャンセル一覧'), array('controller' => 'Reservations', 'action' => 'cancel')); ?></li>
		<li><?php echo $this->Html->link(__('入金一覧'), array('controller' => 'Payments', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('入金一覧(新)'), array('controller' => 'Payments', 'action' => 'new_index')); ?></li>
		<li><?php echo $this->Html->link(__('お知らせ一覧'), array('controller' => 'News', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('売上集計'), array('controller' => 'Statistics', 'action' => 'sales_summary')); ?></li>
		<li><?php echo $this->Html->link(__('キャンセル数集計'), array('controller' => 'Statistics', 'action' => 'cancel_summary')); ?></li>
		<li><?php echo $this->Html->link(__('予約獲得数集計'), array('controller' => 'Statistics', 'action' => 'reservation_summary')); ?></li>
		<li><?php echo $this->Html->link(__('精算額集計'), array('controller' => 'Statistics', 'action' => 'settlement_summary')); ?></li>
		<li><?php echo $this->Html->link(__('精算後調整データ'), array('controller' => 'Statistics', 'action' => 'settlement_adjust')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('募集型料金一覧'), array('controller' => 'TourPrices', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('募集型予約一覧'), array('controller' => 'TourReservations', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('全体の更新履歴'), array('controller' => 'UpdatedTables', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link('クライアント一覧', array('controller' => 'Clients', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link('精算管理会社一覧', array('controller' => 'SettlementCompanies', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link('営業所精算管理マスタ', array('controller' => 'OfficeSettlements', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('スタッフ一覧'), array('controller' => 'Users', 'action' => 'index')); ?></li>

        <li class="divider"></li>
		<li><?php echo $this->Html->link(__('精算書一覧'), array('controller' => 'SettlementSummary', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('郵便番号マスタ'), array('controller' => 'Zipcodes', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('都道府県マスタ'), array('controller' => 'Prefectures', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('市区町村マスタ'), array('controller' => 'Cities', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('ランドマークカテゴリマスタ'), array('controller' => 'LandmarkCategories', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('ランドマークマスタ'), array('controller' => 'Landmarks', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('エリア一覧マスタ'), array('controller' => 'Areas', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('駅マスタ'), array('controller' => 'Stations', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('車両タイプマスタ'), array('controller' => 'CarTypes', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('車種マスタ'), array('controller' => 'CarModels', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('自動車メーカーマスタ'), array('controller' => 'Automakers', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('装備マスタ'), array('controller' => 'Equipment', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('キャンセル料マスタ'), array('controller' => 'CancelFees', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('販売手数料マスタ'), array('controller' => 'CommissionRates', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('レコメンド'), array('controller' => 'Recommends', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('メールテンプレート作成'), array('controller' => 'MailTemplates', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('メール送信対象抽出'), array('controller' => 'SelectMailTargets', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('メール送信履歴'), array('controller' => 'MailSendHistories', 'action' => 'index')); ?></li>

		<li class="divider"></li>
        <li><?php echo $this->Html->link(__('トップコンテンツマスタ'), array('controller' => 'TopContents', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('お知らせマスタ'), array('controller' => 'Messages', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('祝日マスタ'), array('controller' => 'PublicHolidays', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('ページカテゴリマスタ'), array('controller' => 'PageCategories', 'action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('ページマスタ'), array('controller' => 'Pages', 'action' => 'index')); ?></li>

		<li class="divider"></li>
		<li><?php echo $this->Html->link(__('パスワード変更'), array('controller' => 'Users', 'action' => 'edit/' . $cdata['id'])); ?></li>
		<li class="divider"></li>
	</ul>
</div>
