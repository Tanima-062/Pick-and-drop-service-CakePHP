var map;
var geocoder;

function initialize() {
	if (GBrowserIsCompatible()) {
		map = new GMap2(document.getElementById("map_canvas"));
		map.setCenter(new GLatLng());
		geocoder = new GClientGeocoder();
	}
}

function moveAddress(address) {
	geocoder.getLatLng(address, moveTo);
}

function moveTo(latlng) {
	if (latlng) {
		map.setCenter(latlng, 14);
		map.clearOverlays();
		var marker = new GMarker(latlng);
		map.addOverlay(marker);
	}
}

function initializeTarget(map_id, address) {
	if (GBrowserIsCompatible()) {
		var mapObj = new Object();
		var marker = new Object();
		var geocoder = new Object();
		mapObj[map_id] = new GMap2(document.getElementById(map_id));
		mapObj[map_id].setCenter(new GLatLng());
		geocoder[map_id] = new GClientGeocoder();
		geocoder[map_id].getLatLng(address, function moveTo2(latlng) {
			if (latlng) {
				mapObj[map_id].setCenter(latlng, 14);
				mapObj[map_id].clearOverlays();
				marker[map_id] = new GMarker(latlng);
				mapObj[map_id].addOverlay(marker[map_id]);
			}
		});
	}
}

/**
 * 緯度経度でマップを表示する
 * @param latlng
 * @param idSelector
 * @param officeName
 * @param clientId
 */
function mypageMaps(latlng, idSelector, officeName, clientId) {
	// 緯度経度設定
	var mapLatlng = new google.maps.LatLng(latlng[0], latlng[1]);
	// オプション設定
	var opts = {
			zoom: 15,
			center: mapLatlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
	};
	// アイコン設定
	var icon = new google.maps.MarkerImage('/img/hotel/map_icon/map_icon_'+ clientId +'.png',
			new google.maps.Size(55,72),
			new google.maps.Point(0,0)
	);
	var map = new google.maps.Map(document.getElementById(idSelector), opts);
	var marker = new google.maps.Marker({
		position: mapLatlng,    // マーカーの位置
		map: map,    // 表示する地図
		icon: icon,    // アイコン
		title: officeName    // ロールオーバー テキスト
	});
	var infoWindow = new google.maps.InfoWindow();
	google.maps.event.addListener(marker, 'click', function() {
		infoWindow.setContent("<div style='padding:10px'>"+ officeName +"</div>");
		infoWindow.open(map,marker);
	});
}