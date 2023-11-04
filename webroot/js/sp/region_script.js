// var dtp_opt = {
//     lang: "ja",
//     minDate: 0,
//     step: 30,
//     yearStart: new Date().getFullYear(),
//     yearEnd: new Date().getFullYear() + 1,
//     onSelectTime: function (ct, $i) {
//         $("#datetimepicker_start, #datetimepicker_end").datetimepicker("hide");
//     },
// };

// $("#datetimepicker_start").datetimepicker(dtp_opt);
// $("#datetimepicker_end").datetimepicker(dtp_opt);

// function rentSearch() {
//     var form = document.search_form;
//     var date_text;

//     var airport_id = $("#airport_id").val();
//     var area_id = $("#airport_id option:selected").attr("area");
//     var prefecture_id = $("#airport_id option:selected").attr("prefecture");
//     if (area_id && prefecture_id) {
//         form.place.value = "1";
//         form.area_id.value = area_id;
//         form.prefecture.value = prefecture_id;
//     } else {
//         form.place.value = "3";
//         form.area_id.value = "";
//         form.prefecture.value = "";
//     }

//     var return_airport_id = $("#return_airport_id").val();
//     var return_area_id = $("#return_airport_id option:selected").attr("area");
//     var return_prefecture_id = $("#return_airport_id option:selected").attr(
//         "prefecture"
//     );
//     if (return_area_id && return_prefecture_id) {
//         form.return_place.value = "1";
//         form.return_area_id.value = return_area_id;
//         form.return_prefecture.value = return_prefecture_id;
//     } else {
//         form.return_place.value = "3";
//         form.return_area_id.value = "";
//         form.return_prefecture.value = "";
//     }

//     if (!!airport_id && !!form.date.value && !!form.return_date.value) {
//         if (!!form.date.value) {
//             date_part = form.date.value.split(" ");
//             ymd = date_part[0].split("/");
//             time = date_part[1].split(":").join("-");

//             form.year.value = ymd[0];
//             form.month.value = ymd[1];
//             form.day.value = ymd[2];
//             form.time.value = time;
//         }

//         if (!!form.return_date.value) {
//             date_part = form.return_date.value.split(" ");
//             ymd = date_part[0].split("/");
//             time = date_part[1].split(":").join("-");

//             form.return_year.value = ymd[0];
//             form.return_month.value = ymd[1];
//             form.return_day.value = ymd[2];
//             form.return_time.value = time;
//         }

//         //出発店舗へ返却
//         if (!return_airport_id) {
//             form.return_way.value = "0";
//         } else {
//             form.return_way.value = form.return_place.value;
//         }

//         //url形式に変換
//         var search_url = form.action;
//         search_url = search_url.split("?")[0];
//         search_url = search_url + "?" + $(form).serialize();
//         search_url = search_url.replace(/return_date=.*?&/g, "");
//         search_url = search_url.replace(/date=.*?&/g, "");
//         location.href = search_url;
//         return false;
//     }

//     // 使ってなさそう？
//     // $('.error').html('');
//     // if(!form.date.value){
//     // 	$('#error_date').html('出発日時を選択してください。');
//     // }
//     // if(!form.airport_id.value){
//     // 	$('#error_airport_id').html('出発場所を選択してください。');
//     // }
//     // if(!form.return_date.value){
//     // 	$('#error_return_date').html('返却日時を選択してください。');
//     // }

//     return false;
// }

// $(function () {
//     $("#datetimepicker_start").on("change", function () {
//         var start_part = $("#datetimepicker_start").val().split(" ");
//         var start_date = start_part[0];

//         var end_part = $("#datetimepicker_end").val().split(" ");
//         var end_time = end_part[1];

//         $("#datetimepicker_end").val(start_date + " " + end_time);
//     });
//     $("#airport_id").on("change", function () {
//         var val_airport = $("#airport_id").val();
//         $("#return_airport_id").val(val_airport);
//     });
// });
