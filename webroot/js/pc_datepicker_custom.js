// datepicker
// jquery-ui-datepickerのカスタム
// 判明次第ここに集約
// 祝日取得API
$.get('/rentacar/getPublicHoliday/', function(holidaysData) {
    $(function () {
		// 祝日かどうかを判定
        function isPublicHoliday(date) {
            var holidays = Object.keys(holidaysData);
            for (var i = 0; i < holidays.length; i++) {
                var holiday = new Date(Date.parse(holidays[i]));
                if (holiday.getYear() == date.getYear() &&
                    holiday.getMonth() == date.getMonth() &&
                    holiday.getDate() == date.getDate()) {
                    return true;
                }
            }
            return false;
        }

        var commonDatePickerSettings = {
            showOn: "button",
            prevText: '<i class="icm-right-arrow"></i>',
            nextText: '<i class="icm-right-arrow"></i>',
            beforeShowDay: function(date) {
				// 祝日ならクラスを付与
                if (isPublicHoliday(date)) {
                    return [true, "ui-datepicker-public-holiday", null];
                }
                return [true, "", null];
            }
        };

        $("#SearchDate").datepicker($.extend({}, commonDatePickerSettings, {
            minDate: new Date(),
            onClose: function (dateText) {
                var getDate = new Date(dateText);
                $("#SearchReturnDate").datepicker("option", "minDate", getDate);
            }
        }));

        $("#SearchReturnDate").datepicker($.extend({}, commonDatePickerSettings, {
            minDate: new Date($("#SearchDate").val())
        }));

        $("#pickUpDate").datepicker($.extend({}, commonDatePickerSettings, {
            minDate: new Date(),
            onClose: function (dateText) {
                var getDate = new Date(dateText);
                $("#dropOffDate").datepicker("option", "minDate", getDate);
            }
        }));

        $("#dropOffDate").datepicker($.extend({}, commonDatePickerSettings, {
            minDate: new Date($("#SearchDate-oversea").val())
        }));
    });
});