function readMouseOver(id) {
	var position = $("#"+id).position();
	$("#"+id).next().css('left',position.left);
	$("#"+id).next().css('top',position.top+30);
	$("#"+id).next().show();
}
function readMouseOut() {
	$("#"+id).next().hide();
}
function mainDataUpdate() {

	var oReq = new XMLHttpRequest();
	var protocol = location.protocol;
	var host = location.host;
	var className = "client";
	var readStr;

	oReq.open("POST", protocol+"//"+host+"/admin/Bbs/get_bbs_data/0");
	oReq.onreadystatechange = function(){

		if (oReq.readyState === 4 && oReq.status === 200){

			if (oReq.responseText) {

				var result = JSON.parse(oReq.responseText);
				var param = result['param'];
				var clients = result['clients'];

				$("dl.scr").children().remove();

				for (var key in param) {

					var clone = $("#clone").children().clone(true);

					clone.addClass("clearfix");

					clone.addClass(className);
					clone.children("span").addClass(className+"-info");
					clone.children("span").children("label").eq(0).addClass(className+"-bbs-category");
					clone.children("span").children("label").eq(1).addClass(className+"-bbs-date");
					clone.children("span").children("a").eq(0).attr("onclick",'deleteConfirm("/admin/Bbs/delete/53/","投稿を削除しますか？");');
					clone.children("span").children("a").eq().attr("href",'/admin/Bbs/edit/'+param[key]['Bbs']['id']);

					clone.children("div").addClass(className+"_arrow_box");

					clone.children("span").children("."+className+"-bbs-category").html(param[key]['BbsCategory']['name']);
					clone.children("span").children("."+className+"-bbs-date").html(param[key]['Bbs']['created']);

					if (param[key]['AlreadyRead']['client_id'].length > 0) {
						clone.children("span").children(".read").attr("id","read"+param[key]['Bbs']['id']);
						clone.children("span").children(".read").attr("onmouseover","readMouseOver('read"+param[key]['Bbs']['id']+"');");
						clone.children("span").children(".read").html("既読"+param[key]['AlreadyRead']['client_id'].length);

						readStr = '';
						for (var ckey in clients) {
							if (ckey == 0 || ckey == param[key]['Bbs']['from_id']) {
								continue;
							}

							readStr += clients[ckey]+'<span class="detail-read">';
							if (param[key]['AlreadyRead']['client_id'].some(function(v){return v == ckey })) {
								readStr += '<span>既読</span></span><br/>';
							} else {
								readStr += '<span class="red">未読</span></span><br/>';
							}

						}

						clone.children("span").children(".read-list").html(readStr);
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

function chatDataUpdate(id) {

	var oReq = new XMLHttpRequest();
	var protocol = location.protocol;
	var host = location.host;
	var className;

	oReq.open("POST", protocol+"//"+host+"/admin/Bbs/get_bbs_chat_data/"+id);
	oReq.onreadystatechange = function(){

		if (oReq.readyState === 4 && oReq.status === 200){

			if (oReq.responseText) {

				var param = JSON.parse(oReq.responseText);
				$("dl.scr").children().remove();

				for (var key in param) {

					var clone = $("#clone").children().clone(true);

					if (param[key]['Bbs']['from_id'] == id) {
						className = "admin";
						clone.css('background-image', 'url(/client/img/icon/'+param[key]['FromClient']['seo']+'.jpg)');
					} else {
						className = "client";
						clone.addClass("clearfix");
					}

					clone.addClass(className);
					clone.children("span").addClass(className+"-info");
					clone.children("span").children("label").eq(0).addClass(className+"-bbs-category");
					clone.children("span").children("label").eq(1).addClass(className+"-bbs-date");
					clone.children("div").addClass(className+"_arrow_box");

					clone.children("span").children("."+className+"-bbs-category").html(param[key]['BbsCategory']['name']);
					clone.children("span").children("."+className+"-bbs-date").html(param[key]['Bbs']['created']);

					if (param[key]['AlreadyRead']['client_id'].some(function(v){return v == id })) {
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

function deleteConfirm(url,message) {

	// 「OK」時の処理開始 ＋ 確認ダイアログの表示
	if(window.confirm(message)){

		location.href = url;
	} else {
		return false;
	}
}
function inputConvert(id,sort) {

	$("#td-input"+id).empty();
	$("#td-input"+id).append("<input id='input"+id+"' onblur='postQuestion("+id+");' type='text' value='"+sort+"' style='width:30px; margin:0; padding:0;' />");

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

function postQuestion(id) {

	var sort = $("#input"+id).val();

	$("#td-input"+id).empty();
	$("#td-input"+id).html(sort);

	var oReq = new XMLHttpRequest();
	var hrefStr = location.host;
	oReq.open("POST", "http://"+hrefStr+"/admin/questionnaires/sortedit/"+id+"/"+sort);

	oReq.send();
}


function showText(id) {

	if ($("#text-area"+id).is(':hidden') == true) {
		$("#text-area"+id).show();
	} else {
		$("#text-area"+id).hide();
	}
}

function buttonDisp(id) {

	var flg = $("select[name='data[Questionnaire][items]["+id+"][type]']").val();

	if (flg == 2 || flg == 5) {
		$("#btn"+id).show();
	} else {
		$("#btn"+id).hide();
	}
}


function radioBtn(cloneId,key) {

	var id = $("#question-table td input").length;
	var row = $('#name'+key+' input:first').clone();

	row.attr({
			'id':'QuestionnaireItems'+cloneId+'Name'+id,
			'name':'data[Questionnaire][items]['+key+'][name]['+id+']'
		});

	$('#name'+key+' input:last').after(row);

}
function delRadioBtn(cloneId) {

	if ($('#name'+cloneId+' input').length > 1) {
		$('#name'+cloneId+' input:last').remove();
	} else {
		window.alert('これ以上項目を削除できません。最低１項目設定して下さい');
	}

}

function checkAll(dayCount) {

	var i;

	for(i = 3;i <= dayCount+2;i++) {
		if (!document.editForm.elements[2].checked) {
			document.editForm.elements[i].checked = false;
		} else {
			document.editForm.elements[i].checked = true;
		}
	}
}

function htmlEncode(value){
  return $('<div/>').text(value).html();
}

function htmlDecode(value){
  return $('<div/>').html(value).text();
}

// 横スクロール時にサイドメニューを動かす
window.addEventListener('scroll', _handleScroll, false);
function _handleScroll() {
	$('.sidebar-menu').css('left', (-window.scrollX +40) + 'px');
}

//商品管理の 受付締切時間/受付締切日時の切り替え
$(function() {

	if ($(".scr")[0]) {
		var href = location.href;
		var rep;

		rep = href.match(/[0-9]/);

		if (rep === null) {
			setInterval("mainDataUpdate()",600000);
		} else {
			setInterval("chatDataUpdate("+rep+")",10000);
		}

		$(".scr").scrollTop($(".scr").get(0).scrollHeight);
	}

	$(".edit").click(function () {

		var string = $(this).next().html();
		var input = "<input id='content' class='edit-input' type='text' name='data[content]'>";

		$(this).next().html(input);
		$("#content").attr('value',htmlDecode(string));

	});

	$(".read").hover(
		function () {
			var position = $(this).position();
			$(this).next().css('left',position.left);
			$(this).next().css('top',position.top+30);
			$(this).next().show();
		},
		function () {
			$(this).next().hide();
		}
	);

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


	$("#question-button").click(function () {

		var id = $("#question-table tr").length;
		var row = $('#question-table tr:first').clone();

		row.children("td").children("select:first").attr({
				'id':'Questionnaire'+id+'Type',
				'name':'data[Questionnaire][items]['+id+'][type]',
				'onChange':'javascript:buttonDisp('+id+');'
			});
		row.children("td").children("p:first").attr({
				'id':'btn'+id
			});
		row.children("td").children("p").children("a:first").attr({
				'onClick':'radioBtn('+id+')'
			});
		row.children("td").children("p").children("a:last").attr({
				'onClick':'delRadioBtn('+id+')'
			});

		row.children("td").children("p").hide();

		row.children("td:first").next().attr({
			'id':'group'+id
		});
		row.children("td:first").next().children("input").attr({
			'id':'QuestionnaireGroup'+id+'Name',
			'name':'data[QuestionnaireGroup]['+id+'][name]',
		});

		row.children("td:last").prev().attr({
				'id':'name'+id
		});
		row.children("td:last").prev().children("input:first").attr({
			'id':'Questionnaire'+id+'Name0',
			'name':'data[Questionnaire][items]['+id+'][name][0]',
		});
		row.children("td:last").prev().children("input:not(:first)").remove();

		$("#question-table tr:last").after(row);

	});
	$("#question-del-button").click(function () {

		if ($("#question-table tr").length > 1) {
			$('#question-table tr:last').remove();
		} else {
			window.alert('これ以上項目を削除できません。最低１項目設定して下さい');
		}
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
