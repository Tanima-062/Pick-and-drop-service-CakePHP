<?php
$tcpdf->setPrintHeader(false);
$tcpdf->AddPage();
$tcpdf->SetTextColor(0, 0, 0);
$tcpdf->SetFont('kozgopromedium', '', 10); // 日本語に対応したフォントに設定
$tcpdf->SetAutoPageBreak(true, '10');
$tcpdf->Image('/var/www/skyticket.com/rentacar/webroot/client/img/ADVENTURE_logo.jpeg', '153', '14', '35', '7');
$tcpdf->Image('/var/www/skyticket.com/rentacar/webroot/client/img/ADVENTURE_companyseal.png', '165', '25', '22', '20');

// 支払額/請求額の合算
$paymentAmountSum = 0;
$billingAmountSum = 0;

$amount = number_format($settlementTopData['SettlementSummary']['amount']);
$limitDate = date('Y/m/d', strtotime($settlementTopData['SettlementSummary']['payment_limit_datetime']));
$notificationDate = date('Y/m/d', strtotime($settlementTopData['SettlementSummary']['notification_datetime']));
preg_match('/^(\d{4})(\d{2})$/', $settlementTopData['SettlementSummary']['settlement_month'], $settlementMonth);
$year = $settlementMonth[1];
$month = $settlementMonth[2];

$html = <<< EOF
<style>
.border {
	border-bottom-style:solid;
	border-bottom-color:black;
}
.head_column {
	text-align:center;
}
.head_color {
	text-align:center;
	background-color:#d3d3d3;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
.count_column {
	width:20%;
}
.amount_column {
	width:30%;
}
.text_right {
	text-align:right;
}
.td_right {
	text-align:right;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
.item {
	width:50%;
}
.item_column {
	width:50%;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
.detail_count {
	width:10%;
}
.detail_amount {
	width:15%;
}
.item_name {
	width:45%;
	text-align:left;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
.item_code {
	width:5%;
	text-align:center;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
.total_column {
	text-align:center;
	border-style:solid;
	border-width:1px 1px 1px 1px;
	border-color:black;
}
</style>
<table>
	<tr>
		<td rowspan="3" class="item">{$toName}御中</td>
		<td colspan="2" class="text_right" style="width:45%">
			発行日：{$notificationDate}<br>
			<br>
		</td>
	</tr>
	<tr>
		<td style="width:50%" colspan="2">
			〒{$COMPANY_ZIP}<br>
			{$COMPANY_ADDRESS}<br>
			{$COMPANY_ADDRESS_OTHER}<br>
			{$ADV_COMPANY_NAME_JAPANESE}<br>
		</td>
	</tr>
	<tr>
		<td colspan="2">TEL:{$ADV_SETTLEMENT_TEL} / FAX:{$ADV_DISPLAY_FAX}</td>
	</tr>
</table>
<h1 class="head_column">{$documentName}書</h1>
<div>下記の通り、{$documentString}申し上げます</div>
<table class="item">
	<thead>
		<tr>
			<td class="head_color">{$amountName}金額(消費税込)</td>
			<td class="head_color">¥{$amount}</td>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td class="head_column border">{$limitName}</td>
			<td class="head_column border">{$limitDate}</td>
		</tr>
		<tr>
			<td class="head_column border">{$notificationName}日</td>
			<td class="head_column border">{$notificationDate}</td>
		</tr>
	</tbody>
</table>
<br>
<div>● 販売実績</div>
<table>
	<thead>
		<tr>
			<td colspan="2" class="item_column head_color">品目</td>
			<td class="head_color count_column">件数</td>
			<td class="head_color amount_column">金額</td>
		</tr>
	</thead>
	<tbody>
EOF;
foreach($settlementSalesPerformanceData as $sk => $sv){
	$html.= '<tr>';
	$html.= '<td class="item_code">'. $sv['SettlementSummarySalesPerformance']['item_code'] .'</td>';
	$html.= '<td class="item_name">'. $sv['SettlementSummarySalesPerformance']['item_name'] .'</td>';
	$html.= '<td class="td_right count_column">'. $sv['SettlementSummarySalesPerformance']['count'] .'</td>';
	$html.= '<td class="td_right amount_column">'. number_format($sv['SettlementSummarySalesPerformance']['amount']) .'</td>';
	$html.= '</tr>';
}
$html .= <<< EOF
	</tbody>
</table>
<br>
<div>● 精算内容詳細</div>
<table>
	<thead>
		<tr>
			<td colspan="2" class="item_column head_color">品目</td>
			<td class="detail_count head_color">件数</td>
			<td class="detail_count head_color">料率</td>
			<td class="detail_amount head_color">支払額</td>
			<td class="detail_amount head_color">請求額</td>
		</tr>
	</thead>
	<tbody>
EOF;
foreach($settlementDetailData as $sk => $sv){
	if($sv['SettlementSummaryDetail']['item_code'] != ''){
		$commissionRate = !empty($sv['SettlementSummaryDetail']['commission_rate']) ? number_format($sv['SettlementSummaryDetail']['commission_rate'], 1).'%' : '-';
	}else{
		$commissionRate = '';
	}
	$paymentAmount = ($sv['SettlementSummaryDetail']['payment_amount'] != '') ? number_format($sv['SettlementSummaryDetail']['payment_amount']) : '';
	$billingAmount = ($sv['SettlementSummaryDetail']['billing_amount'] != '') ? number_format($sv['SettlementSummaryDetail']['billing_amount']) : '';
	if ($sv['SettlementSummaryDetail']['item_name'] == $taxName && $isInternalTax == '1') {
		if ($paymentAmount > 0) {
			$paymentAmount = '(' . $paymentAmount . ')';
		}
		if ($billingAmount > 0) {
			$billingAmount = '(' . $billingAmount . ')';
		}
	}
$html .= <<< EOF
		<tr>
			<td class="item_code">{$sv['SettlementSummaryDetail']['item_code']}</td>
			<td class="item_name">{$sv['SettlementSummaryDetail']['item_name']}</td>
			<td class="detail_count td_right">{$sv['SettlementSummaryDetail']['count']}</td>
			<td class="detail_count td_right">{$commissionRate}</td>
			<td class="detail_amount td_right">{$paymentAmount}</td>
			<td class="detail_amount td_right">{$billingAmount}</td>
		</tr>
EOF;
	if (!($sv['SettlementSummaryDetail']['item_name'] == $taxName && $isInternalTax == '1')) {
		// 内税の場合は消費税を合算しない
		$paymentAmountSum += $sv['SettlementSummaryDetail']['payment_amount'];
		$billingAmountSum += $sv['SettlementSummaryDetail']['billing_amount'];
	}
}
$paymentAmountSum = number_format($paymentAmountSum);
$billingAmountSum = number_format($billingAmountSum);
$html .= <<< EOF
		<tr>
			<td colspan="4" class="total_column">合計</td>
			<td class="detail_amount td_right">{$paymentAmountSum}</td>
			<td class="detail_amount td_right">{$billingAmountSum}</td>
		</tr>
		<tr>
			<td colspan="4" class="total_column">ご精算金額</td>
			<td colspan="2" class="td_right">{$amount}</td>
		</tr>
	</tbody>
</table>
<br>
<br>
<table style="padding:3px">
	<tr><td colspan="2">● お振込先情報</td></tr>
	<tr>
		<td class="count_column">銀行・支店名：</td>
		<td>{$bankName}</td>
	</tr>
	<tr>
		<td class="count_column">口座番号：</td>
		<td>{$bankNumber}</td>
	</tr>
	<tr>
		<td class="count_column">口座名義：</td>
		<td>{$bankHolder}</td>
	</tr>
</table>
{$transferMessage}
EOF;

// サイズを5％ずつ小さくしていく調整用
// 1ページに収めたい出力内容が複数ページ出力される場合少しずつ縮小していって1ページに収める
function writeHTMLShrink($tcpdf, $html, $size='100'){
	$startNo = $tcpdf->getNumPages();
	$style = "<style>*{font-size:".$size."%;}</style>";
	$tcpdf->writeHTML($html.$style, false, false, false, false, 'L');
	$endNo = $tcpdf->getNumPages();
	if($endNo != $startNo){
		// 1ページに収まらなかったためページを削除し作り直す
		for($i = $endNo;$i >= $startNo;$i--){
			$tcpdf->deletePage($i);
		}
		$tcpdf->AddPage();
		$tcpdf->Image('/var/www/skyticket.com/rentacar/webroot/client/img/ADVENTURE_logo.jpeg', '153', '14', '35', '7');
		$tcpdf->Image('/var/www/skyticket.com/rentacar/webroot/client/img/ADVENTURE_companyseal.png', '165', '25', '22', '20');
		writeHTMLShrink($tcpdf, $html, ($size - 10));
	}
}
writeHTMLShrink($tcpdf, $html);

// キャンセル一覧
if(count($settlementCancelData) > 0){
	$tcpdf->AddPage();
$html = <<< EOF
<style>
* {
	font-size:85%;
}
.text_center {
	text-align:center;
}
.reservation_key {
	text-align:center;
	border-bottom-style:solid;
	border-width:1px 1px 1px 1px;
	border-bottom-color:black;
	width:13%;
}
.client_name {
	text-align:center;
	border-bottom-style:solid;
	border-width:1px 1px 1px 1px;
	border-bottom-color:black;
	width:25%;
}
.return_office_name {
	text-align:center;
	border-bottom-style:solid;
	border-width:1px 1px 1px 1px;
	border-bottom-color:black;
	width:28%;
}
.name {
	text-align:center;
	border-bottom-style:solid;
	border-width:1px 1px 1px 1px;
	border-bottom-color:black;
	width:22%;
}
.amount {
	text-align:right;
	border-bottom-style:solid;
	border-width:1px 1px 1px 1px;
	border-bottom-color:black;
	width:8%;
}
.header_color {
	background-color:#d3d3d3;
	text-align:center;
}
</style>
<div class="text_center">スカイチケット&nbsp;&nbsp;{$year}年{$month}月&nbsp;&nbsp;キャンセル&nbsp;&nbsp;明細</div>
<br>
<table style="padding:2px">
	<thead>
		<tr>
			<td class="header_color reservation_key">予約番号</td>
			<td class="header_color client_name">クライアント名</td>
			<td class="header_color return_office_name">返却店舗名</td>
			<td class="header_color name">氏名</td>
			<td class="header_color amount">合計金額</td>
		</tr>
	</thead>
	<tbody>
EOF;
	foreach($settlementCancelData as $sk => $sv){
		$amount = number_format($sv['SettlementSummaryCancelData']['amount']);
$html .= <<< EOF
		<tr>
			<td class="reservation_key">{$sv['SettlementSummaryCancelData']['reservation_key']}</td>
			<td class="client_name">{$sv['SettlementSummaryCancelData']['client_name']}</td>
			<td class="return_office_name">{$sv['SettlementSummaryCancelData']['return_office_name']}</td>
			<td class="name">{$sv['SettlementSummaryCancelData']['name']}</td>
			<td class="amount">{$amount}</td>
		</tr>
EOF;
	}
$html .= <<< EOF
	</tbody>
</table>
EOF;
	// 作った HTML を書きだします。
	$tcpdf->writeHTML($html, false, false, false, false, 'L');
}

ob_end_clean();

$fileName = $settlementTopData['SettlementSummary']['settlement_company_accounting_code'].$toName.'様ご精算書.pdf';
$fileName = rawurlencode($fileName);
// ダウンロードファイル名を日本語にするために無理やりヘッダーを作る(Dオプションは日本語NG)
if($previewFlg){
	$pdfData = $tcpdf->Output($fileName, 'I'); //D or I(prevew)
}else{
	$pdfData = $tcpdf->Output($fileName, 'S'); //D or I(prevew)
}
header("Pragma: public");
header("Expires: 0 ");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Content-Transfer-Encoding: binary");
header("Content-Type: application/octet-streams");
header("Content-Disposition: attachment; filename*=utf-8''".$fileName);
print $pdfData;

?> 
