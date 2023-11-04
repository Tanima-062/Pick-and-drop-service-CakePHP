<!-- 各プラン内 equipmentの説明モーダル（検索結果） -->

<section class="sp_modal_equipment_desc">
	<div id="js_modal_equip" class="modal_wrap equipment_desc">
		<div class="modal_equip_contents">
			<span id="js_notice_text"></span>
		</div>
	</div>

	<div id="js_modal_overlay" class="modal_overlay"></div>
</section>

<script>
$(function(){
	$(".js-modal_equip_open").on("click", function(){
		var equip_text = $(this).text().trim();
		var notice_text = "";
		switch(equip_text){
			case "免責補償":
				notice_text = "免責補償料金込みプラン";
				break;
			case "AT車":
				notice_text = "オートマチックトランスミッションの車です";
				break;
			case "MT車":
				notice_text = "マニュアルトランスミッションの車です";
				break;
			case "カーナビ":
				notice_text = "カーナビ装備";
				break;
			case "ETC車載器":
				notice_text = "ETC車載器装備（ETCカードは持参）";
				break;
			case "運転サポート":
				notice_text = "安全運転サポート機能車（衝突緩和ブレーキ、はみ出し検知など）";
				break;
			case "バックモニター":
				notice_text = "後ろ向き駐車や車庫入れも安心";
				break;
			case "NOC補償":
				notice_text = "事故時のノンオペレーションチャージの支払いが免除されます";
				break;
			case "AUX":
				notice_text = "イヤホンプラグ差込でお手持ちのスマホから音楽再生を楽しめます";
				break;
			case "Bluetooth":
				notice_text = "Bluetooth接続でお手持ちのスマホから音楽再生を楽しめます";
				break;
			case "スタッドレス":
				notice_text = "雪道も安心のスタッドレスタイヤ装備";
				break;
			case "チェーン":
				notice_text = "タイヤチェーン搭載";
				break;
			case "4WD":
				notice_text = "4WD（四輪駆動車）指定";
				break;
			case "ETCカード":
				notice_text = "ETCカード貸出"
				break;
			case "ドライブレコーダー":
				notice_text = "ドライブレコーダー装備";
			default:
				break;
		}
		$("#js_notice_text").text(notice_text);

		$("#js_modal_overlay").fadeIn("fast");
		$("#js_modal_equip").fadeIn("fast");

		$("#js_modal_overlay, #js_modal_equip").on('click', function(e) {
			$("#js_modal_overlay, #js_modal_equip").off("click");

			$("#js_modal_equip").fadeOut("fast");
			$("#js_modal_overlay").fadeOut("fast");
		});
	});
});
</script>
