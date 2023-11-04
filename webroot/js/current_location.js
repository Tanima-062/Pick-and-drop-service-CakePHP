// 現在地から検索ボックスの値を設定する
$(function () {
	if (!navigator.geolocation) {
		return;
	}

	// 現在地を取得
	navigator.geolocation.getCurrentPosition(
		function (position) {
			var url  = '/rentacar/api/ajax/v1/current_location/'
					+ position.coords.latitude + '/' + position.coords.longitude + '/';
			
			$.ajax({
				url: url,
				method: 'GET',
				dataType: 'json'
			}).done(function(data){
				var prefecture_id = data.response.prefecture_id;
				$('.select_place_tab[data-place=departure_prefecture]').trigger('click');
				$('#prefecture > option[value=' + data.response.prefecture_id + ']').prop('selected', 'selected');
				$('#prefecture').trigger('change');
			});
		}
	);
});
