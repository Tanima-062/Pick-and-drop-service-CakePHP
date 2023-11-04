
function deleteCampaignBox(eq) {

	$("#campaign-block").children(".add"+eq).remove();

	var height = $("#campaign-block").height();

	$("#campaign-block").height((height-50));
	$("#copy-box").height((height-50));

}

function loadStationListByArea(element,area_id,retrieve,refresh){

	$.ajax({
		type: "GET",
		url: "/rentacar/client/ajax/get_stations_by_area/" + area_id,
		success: function(result) {

			var stations = null;
			stations = new Bloodhound({
			  datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
			  queryTokenizer: Bloodhound.tokenizers.whitespace,
			  local: JSON.parse(result)
			});
			stations.initialize(true);

			if(refresh){
				$(element).tagsinput('removeAll');
				$(element).tagsinput('destroy');
			}

			$(element).tagsinput({
				  itemValue: 'id',
				  itemText: 'name',
				  freeInput: false,
				  typeaheadjs: {
				    name: 'stations',
				    displayKey: 'name',
				    source: stations.ttAdapter()
				  }
			});
			if(refresh){
				$(element).tagsinput('refresh');
			}
		},
		complete: function(xhr,status){
			if(retrieve){
				var data = $(element).attr("data");
				var arr_data = JSON.parse(data);
				if (arr_data != null){
	   				for (var i = 0; i < arr_data.length; i++) {
					    $(element).tagsinput('add', {"id": arr_data[i].id ,"name": arr_data[i].name});
					}
				}
			}
		}
	});
}

$(document).ready(function(){

	if($('#OfficeAreaId').length){

		var registred_area_id = $("#OfficeAreaId").val();
		loadStationListByArea("#OfficeStationIds",registred_area_id,true,false);

		$("#OfficeAreaId").change(function(){
       		var area_id = this.value;
       		loadStationListByArea("#OfficeStationIds",area_id,false,true);
   		});
	}

	/**
	 * timepickerを使用
	 * ホテル編集画面
	 */
//	$('.from_time_tp').timepicker({
//		disableFocus: true,
//		showMeridian: false,
//		defaultTime: '08:00',
//		minuteStep: '10',
//	});
//	$('.to_time_tp').timepicker({
//		disableFocus: true,
//		showMeridian: false,
//		defaultTime: '20:00',
//		minuteStep: '10',
//	});

	$("#add-campaign").click(function(){

		var campaign = $(".campaign :last").clone();
		var count = parseInt(campaign.attr('key')) + parseInt(1);

		campaign.attr('key',count);
		campaign.attr('class','');
		campaign.attr('class','campaign add'+count);
		campaign.children("select").attr('id','Campaign'+count+'PriceRankId');
		campaign.children("select").attr('name','data[Campaign]['+count+'][price_rank_id]');
		campaign.children("input:eq(0)").attr('id','Campaign'+count+'CommodityId');
		campaign.children("input:eq(0)").attr('name','data[Campaign]['+count+'][commodity_id]');
		campaign.children("input:eq(1)").attr('id','Campaign'+count+'ClientId');
		campaign.children("input:eq(1)").attr('name','data[Campaign]['+count+'][client_id]');
		campaign.children("input:eq(2)").attr('id','Campaign'+count+'StaffId');
		campaign.children("input:eq(2)").attr('name','data[Campaign]['+count+'][staff_id]');
		campaign.children("input:eq(3)").attr('name','data[Campaign]['+count+'][rank_date_from]');
		campaign.children("input:eq(3)").attr('id','Campaign'+count+'RankDateFrom');
		campaign.children("input:eq(4)").attr('name','data[Campaign]['+count+'][rank_date_to]');
		campaign.children("input:eq(4)").attr('id','Campaign'+count+'RankDateTo');
		campaign.children("a").remove();
		campaign.children(".del-label").remove();
		campaign.children("input:eq(5)").remove();
		campaign.children("input:eq(5)").remove();
		campaign.children("input:eq(5)").remove();
		campaign.append("<a class='btn btn btn-warning' style='width:50px; font-size:10px;' " +
				"onclick='deleteCampaignBox("+count+");'>キャンセル</a>");

		$("#campaign-block p:last").after(campaign);

		 var dates = $( '#Campaign'+count+'RankDateFrom, #Campaign'+count+'RankDateTo' ).removeClass('hasDatepicker').datepicker( {
		    	dateFormat: 'yy-mm-dd',
		        showAnim: 'clip',
		        monthNames: ['1月','2月','3月','4月','5月','6月',
		                     '7月','8月','9月','10月','11月','12月'],
		        changeMonth: false,
		        numberOfMonths: 3,
		        showCurrentAtPos: 0,
		    } );


		var height = $("#campaign-block").height();

		if (height > 200) {
			$("#copy-box").height((height-50));
		}

		return false;
	});


	$(".copy-campaign").click(function(){

		var rankId = $(this).attr('rank-id');
		var fromDate = $(this).attr('from');
		var toDate = $(this).attr('to');

		var campaign = $(".campaign :last").clone();
		var count = parseInt(campaign.attr('key')) + parseInt(1);

		// 空のものがある状態でコピーをした場合は空のフォームに当てはめる
		if (!$(".campaign :first").children("input:eq(3)").val()) {

			$(".campaign :first").children("select[name='select']").val(rankId);
			$(".campaign :first").children("input:eq(3)").val(fromDate);
			$(".campaign :first").children("input:eq(4)").val(toDate);

		} else {

			campaign.attr('key',count);
			campaign.attr('class','');
			campaign.attr('class','campaign add'+count);
			campaign.children("select").attr('id','Campaign'+count+'PriceRankId');
			campaign.children("select").attr('name','data[Campaign]['+count+'][price_rank_id]');
			campaign.children("select[name='select']").val(rankId);
			campaign.children("input:eq(0)").attr('id','Campaign'+count+'CommodityId');
			campaign.children("input:eq(0)").attr('name','data[Campaign]['+count+'][commodity_id]');
			campaign.children("input:eq(1)").attr('id','Campaign'+count+'ClientId');
			campaign.children("input:eq(1)").attr('name','data[Campaign]['+count+'][client_id]');
			campaign.children("input:eq(2)").attr('id','Campaign'+count+'StaffId');
			campaign.children("input:eq(2)").attr('name','data[Campaign]['+count+'][staff_id]');
			campaign.children("input:eq(3)").attr('name','data[Campaign]['+count+'][rank_date_from]');
			campaign.children("input:eq(3)").attr('id','Campaign'+count+'RankDateFrom');
			campaign.children("input:eq(3)").val(fromDate);
			campaign.children("input:eq(4)").attr('name','data[Campaign]['+count+'][rank_date_to]');
			campaign.children("input:eq(4)").attr('id','Campaign'+count+'RankDateTo');
			campaign.children("input:eq(4)").val(toDate);
			campaign.children("a").remove();
			campaign.children(".del-label").remove();
			campaign.children("input:eq(5)").remove();
			campaign.children("input:eq(5)").remove();
			campaign.children("input:eq(5)").remove();
			campaign.append("<a class='btn btn btn-warning' style='width:50px; font-size:10px;' " +
					"onclick='deleteCampaignBox("+count+");'>キャンセル</a>");

			$("#campaign-block p:last").after(campaign);

			 var dates = $( '#Campaign'+count+'RankDateFrom, #Campaign'+count+'RankDateTo' ).removeClass('hasDatepicker').datepicker( {
			    	dateFormat: 'yy-mm-dd',
			        showAnim: 'clip',
			        monthNames: ['1月','2月','3月','4月','5月','6月',
			                     '7月','8月','9月','10月','11月','12月'],
			        changeMonth: false,
			        numberOfMonths: 3,
			        showCurrentAtPos: 0,
			        onSelect: function( selectedDate ) {

			            var option = this . className  == 'jquery-ui-datepicker-from hasDatepicker' ? 'minDate' : 'maxDate',
			                instance = jQuery( this ) . data( 'datepicker' ),
			                date = jQuery . datepicker . parseDate(
			                    instance . settings . dateFormat ||
			                    jQuery . datepicker . _defaults . dateFormat,
			                    selectedDate, instance . settings );
			            dates . not( this ) . datepicker( 'option', option, date );
			        }
			    } );


			var height = $("#campaign-block").height();

			if (height > 200) {
				$("#copy-box").height((height-50));
			}
		}

		return false;
	});
});

function chatDataUpdate() {

	var oReq = new XMLHttpRequest();
	var protocol = location.protocol;
	var host = location.host;
	var className;

	oReq.open("POST", protocol+"//"+host+"/client/Bbs/get_bbs_chat_data/");
	oReq.onreadystatechange = function(){

		if (oReq.readyState === 4 && oReq.status === 200){

			if (oReq.responseText) {

				var result = JSON.parse(oReq.responseText);
				var param = result['param'];
				var id = result['clientId'];

				$("dl.scr").children().remove();

				for (var key in param) {

					var clone = $("#clone").children().clone(true);

					if (param[key]['Bbs']['from_id'] != id) {
						className = "admin";
					} else {
						className = "client";
						clone.addClass("clearfix");
						clone.css('background-image', 'url(/client/img/icon/'+param[key]['FromClient']['seo']+'.jpg)');
					}

					clone.addClass(className);
					clone.children("span").addClass(className+"-info");
					clone.children("span").children("label").eq(0).addClass(className+"-bbs-category");
					clone.children("span").children("label").eq(1).addClass(className+"-bbs-date");
					clone.children("div").addClass(className+"_arrow_box");

					clone.children("span").children("."+className+"-bbs-category").html(param[key]['BbsCategory']['name']);
					clone.children("span").children("."+className+"-bbs-date").html(param[key]['Bbs']['created']);
					clone.children("span").children(".chat-read").html(param[key]['AlreadyRead']);
					if (param[key]['AlreadyRead'] == 1) {
						clone.children("span").children(".chat-read").html("既読");
					}

					clone.children("."+className+"_arrow_box").html(param[key]['Bbs']['content']);

					$("dl.scr").append(clone);
				}
				$(".scr").scrollTop($(".scr").get(0).scrollHeight);

			} else {
				// レスポンスデータ無し
			}
		} else {
			//window.alert('内容の保存が正常に終了いたしませんでした。\nお手数ですがもう一度お試し下さい。');
		}
	};
	oReq.send();
}

function mainDataUpdate() {

	var oReq = new XMLHttpRequest();
	var protocol = location.protocol;
	var host = location.host;
	var className;

	oReq.open("POST", protocol+"//"+host+"/client/Bbs/get_bbs_data/");
	oReq.onreadystatechange = function(){

		if (oReq.readyState === 4 && oReq.status === 200){

			if (oReq.responseText) {

				var param = JSON.parse(oReq.responseText);

				$("dl.scr").children().remove();

				for (var key in param) {

					var clone = $("#clone").children().clone(true);

					clone.children(".admin-bbs-category").html(param[key]['BbsCategory']['name']);
					clone.children(".bbs-date").html(param[key]['Bbs']['created']);

					clone.children(".admin_arrow_box").html(param[key]['Bbs']['content']);

					$("dl.scr").append(clone);
				}
				$(".scr").scrollTop($(".scr").get(0).scrollHeight);

			} else {
				// レスポンスデータ無し
			}
		} else {
			//window.alert('内容の保存が正常に終了いたしませんでした。\nお手数ですがもう一度お試し下さい。');
		}
	};
	oReq.send();
}

function inputConvert(id,sort) {

	$("#td-input"+id).empty();
	$("#td-input"+id).append("<input id='input"+id+"' onblur='postQ("+id+");' type='text' value='"+sort+"' style='width:30px; margin:0; padding:0;' />");

}

function postQ(id) {

	var sort = $("#input"+id).val();

	$("#td-input"+id).empty();
	$("#td-input"+id).html(sort);

	var oReq = new XMLHttpRequest();
	var hrefStr = location.host;
	oReq.open("POST", "http://"+hrefStr+"/client/client_templates/sortedit/"+id+"/"+sort);
	oReq.onreadystatechange = function(){
		// 本番用
		if (oReq.readyState === 4 && oReq.status === 200){
			location.reload();
		}
	};
	oReq.send();
}

function sortCancel() {

	$("#sort-edit").html('ソート番号編集');
	$("#sort-edit").attr('class','btn btn-warning');


	$("#sort-edit").show();
	$(".sort-now").show();
	$(".sort-form").hide();
	$("#sort-save").hide();
	$("#sort-cancel").remove();
}

// 横スクロール時にサイドメニューを動かす
window.addEventListener('scroll', _handleScroll, false);
function _handleScroll() {
	$('.sidebar-menu').css('left', (-window.scrollX +40) + 'px');
}

//商品管理の 受付締切時間/受付締切日時の切り替え
$(function() {

	$('#CarClassStockAll').on('click', function() {
		  $('input.stock-check:checkbox').prop('checked', $(this).is(':checked'));
		});

	if ($(".scr")[0]) {

		var href = location.href;
		if (href.lastIndexOf("main") != -1) {
			setInterval("mainDataUpdate()",600000);
		} else if (href.lastIndexOf("chat") != -1) {
			setInterval("chatDataUpdate()",10000);
		}

		$(".scr").scrollTop($(".scr").get(0).scrollHeight);
	}

	$("#sort-edit").click(function () {

		$("#sort-edit").hide();
		$(".sort-now").hide();
		$("#sort-save").show();
		$(".sort-form").show();
		$(this).after('<a id="sort-cancel" class="btn btn-danger" href="javascript:void(0);" onclick="sortCancel();">キャンセル</a>');
	});

	if($("#CommodityTermIsDeadlineHours").val() == 0) {
		$("#deadline-hours").hide();
		$("#deadline-days").show();
	} else {
		$("#deadline-days").hide();
		$("#deadline-hours").show();
	}

	$("#CommodityTermIsDeadlineHours").change(function () {

		if($(this).val() == 1) {
			$("#deadline-days").hide();
			$("#deadline-hours").show();
		} else {
			$("#deadline-hours").hide();
			$("#deadline-days").show();
		}

	});







$(function() {
	// 車両クラス登録/編集 在庫管理地域一括チェック/非チェック
	$('#CarClassEditAllStockEdit').on('change', function() {
		if ($(this).is(':checked')) {
			$('input[id^="CarClassStockGroupStockGroupId"]').prop('checked', true);

		} else {
			$('input[id^="CarClassStockGroupStockGroupId"]').prop('checked', false);
		}
	});




	// 商品情報登録/編集 営業所
	$('#CommodityRentOfficePrefecture, #CommodityReturnOfficePrefecture').on('change', function() {
		var pref_id = $(this).val();

		$(this).parent('td').children('div[class="checkbox"]').hide();

		if (pref_id) {
			$(this).parent('td').children('div').children('input[class="pref-class_' + pref_id + '"]').parent('div[class="checkbox"]').show();
		} else {
			$(this).parent('td').children('div').children('input[class^="pref-class_"]').parent('div[class="checkbox"]').show();
		}
	});

	// 全選択
	$('#CommodityRentOfficeAllCommodityEdit, #CommodityReturnOfficeAllCommodityEdit').on('change', function() {
		var pref_id = $(this).parents('div').prev('select').val();

		if (pref_id) {
			if ($(this).is(':checked')) {
				$(this).parents('td').children('div.checkbox').children('input[class="pref-class_' + pref_id +'"]').prop('checked', true);

			} else {
				$(this).parents('td').children('div.checkbox').children('input[class="pref-class_' + pref_id +'"]').prop('checked', false);
			}

		} else {
			if ($(this).is(':checked')) {
				$(this).parents('td').children('div.checkbox').children('input[class^="pref-class_"]').prop('checked', true);

			} else {
				$(this).parents('td').children('div.checkbox').children('input[class^="pref-class_"]').prop('checked', false);
			}
		}
	});
});






});

// リセットボタン処理
$(function() {
	$(document).on('click', '.btn-reset', function() {
		let $form = $(this).closest('form');
		$form.find('input[type=text]:enabled').val('');
		$form.find('textarea:enabled').val('');
		$form.find('select:enabled').val('');
		let $checkbox = $form.find('input[type=checkbox]:enabled');
		if($checkbox.length > 0) {
			$checkbox.prop('checked', false);
		}
	})
});
