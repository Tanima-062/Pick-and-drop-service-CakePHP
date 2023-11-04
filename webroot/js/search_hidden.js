// PC
// default.ctp(PC)のみで使用中みたい

/*
 * 国内/海外タブのデフォルトを国内表示にする
 */
window.onpageshow = function () {
    $("input[name=rentacar-tab]").val(["rentacar-domestic"]);
    $("input[name=select-popularity]").prop("checked", false);
};
$(function () {
    /*
     * 国内/海外タブ切替
     */

    $('input[name="rentacar-tab"]').change(function () {
        var val = $(this).val();
        var domestic = $("#SearchIndexForm");
        var oversea = $("#rentacar-oversea-form");
        if (val == "rentacar-domestic") {
            domestic.show();
            oversea.hide();
            $("#search-container").removeClass("oversea");
            $("#top-contents-domestic").show();
        } else {
            oversea.show();
            domestic.hide();
            $("#search-container").addClass("oversea");
            $("#top-contents-domestic").hide();
            // ajax
            getOverseaCountry().done(function (res) {
                var options = [];
                for (var country_id in res) {
                    if (res[country_id].id === 273) continue;

                    var option = $("<option>")
                        .val(res[country_id].id)
                        .text(res[country_id].name);
                    options.push(option);
                }
                $("#pickUpCountry").append(options);
            });
        }
    });
    /*
     * 海外検索フォーム
     */
    // 乗り捨てチェック
    $("#return_way_check_oversea").on("click", function () {
        // 同じ場所に返却
        if ($(this).prop("checked")) {
            $("#return-place").hide();
            $("#dropOffCountry").find("option:not(:eq(0))").remove();

            const location_id = $("#pickUpLocation").val();
            if (Number(location_id) === 0) {
                // 検索ボタンを無効化
                $("#submit_oversea_search").prop("disabled", true);
            } else {
                // 検索ボタンを有効化
                $("#submit_oversea_search").prop("disabled", false);
            }
            // 乗り捨て
        } else {
            // 乗り捨ての場合、返却国を取得
            setDropOffCountry();
            $("#return-place").show();

            // 検索ボタンを無効化
            $("#submit_oversea_search").prop("disabled", true);
        }
    });

    $("#pickUpCountry").change(function () {
        var pickUpCountryId = $(this).val();
        $("#pickUpCity").val(0).change();
        $("#pickUpCity").find("option:not(:eq(0))").remove();
        // $('#pickUpLocation').find('option:not(:eq(0))').remove();
        if (Number(pickUpCountryId) === 0) {
            $("#pickUpCity")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            $("#pickUpLocation")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            return false;
        }

        getOverseaCity(pickUpCountryId).done(function (res) {
            var options = [];
            for (var city_id in res) {
                var option = $("<option>")
                    .val(res[city_id].id)
                    .text(res[city_id].name);
                options.push(option);
            }
            $("#pickUpCity").append(options);
            $("#pickUpCity").prop("disabled", false);
            $("#pickUpCity").parent().removeClass("disabled");
        });
    });
    let popularity_city = 0;
    $("#pickUpCity").change(function () {
        var pickUpCityId = $(this).val();

        // 人気のエリアをリセット
        if (Number(pickUpCityId) !== popularity_city) {
            $('input[name="select-popularity"]').prop("checked", false);
        }

        $("#pickUpLocation").val(0).change();
        $("#pickUpLocation").find("option:not(:eq(0))").remove();
        if (Number(pickUpCityId) === 0) {
            $("#pickUpLocation")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            return false;
        }

        getOverseaArea(pickUpCityId).done(function (res) {
            var options = [];
            for (var area_id in res) {
                var option = $("<option>")
                    .val(res[area_id].id)
                    .text(res[area_id].name);
                options.push(option);
            }
            $("#pickUpLocation").append(options);
            $("#pickUpLocation").prop("disabled", false);
            $("#pickUpLocation").parent().removeClass("disabled");
        });
    });
    $("#pickUpLocation").change(function () {
        var pickUpLocationId = $(this).val();
        $("#dropOffCountry").val(0).change();
        $("#dropOffCountry").find("option:not(:eq(0))").remove();
        if (Number(pickUpLocationId) === 0) {
            $("#dropOffCountry")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            $("#dropOffCity")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            $("#dropOffLocation")
                .prop("disabled", true)
                .parent()
                .addClass("disabled");
            // 検索ボタンを無効化
            $("#submit_oversea_search").prop("disabled", true);
            return false;
        }

        // 片道チェック
        if ($("#return_way_check_oversea").prop("checked")) {
            // 乗り捨てでない場合、検索ボタンを有効化
            $("#submit_oversea_search").prop("disabled", false);
        } else {
            // 乗り捨ての場合、返却国を取得
            setDropOffCountry();
        }
    });
    $("#dropOffCountry").change(function () {
        var dropOffCountryId = $(this).val();
        var pickUpLocationId = $("#pickUpLocation").val();
        $("#dropOffCity").val(0).change();
        $("#dropOffCity").find("option:not(:eq(0))").remove();
        if (Number(dropOffCountryId) === 0) {
            $("#dropOffCity")
                .prop("disabled", true)
                .parent("label")
                .addClass("disabled");
            $("#dropOffLocation")
                .prop("disabled", true)
                .parent("label")
                .addClass("disabled");
            return false;
        }

        getOverseaCity(dropOffCountryId, pickUpLocationId)
            .done(function (res, status, jqXHR) {
                let options = [];
                if (jqXHR.status !== 200) {
                    // 空が返ってきたときはpickUp側で選んだ都市をリストに追加する
                    const id = $("#pickUpCity").val();
                    const name = $("#pickUpCity option:selected").text();
                    const option = $("<option>").val(id).text(name);
                    options.push(option);
                } else {
                    for (var city_id in res) {
                        var option = $("<option>")
                            .val(res[city_id].id)
                            .text(res[city_id].name);
                        options.push(option);
                    }
                }
                $("#dropOffCity").append(options);
                $("#dropOffCity")
                    .prop("disabled", false)
                    .parent()
                    .removeClass("disabled");
            })
            .fail(function () {
                // 失敗したときはpickUp側で選んだ都市をリストに追加する
                const id = $("#pickUpCity").val();
                const name = $("#pickUpCity option:selected").text();
                const option = $("<option>").val(id).text(name);
                const options = [option];
                $("#dropOffCity").append(options);
                $("#dropOffCity")
                    .prop("disabled", false)
                    .parent()
                    .removeClass("disabled");
            });
    });
    $("#dropOffCity").change(function () {
        var dropOffCityId = $(this).val();
        $("#dropOffLocation").val(0).change();
        $("#dropOffLocation").find("option:not(:eq(0))").remove();
        if (Number(dropOffCityId) === 0) {
            $("#dropOffLocation").prop("disabled", false);
            $("#dropOffLocation").parent().addClass("disabled");
            return false;
        }

        getOverseaArea(dropOffCityId)
            .done(function (res, status, jqXHR) {
                let options = [];
                if (jqXHR.status !== 200) {
                    // 空が返ってきたときはpickUp側で選んだエリアをリストに追加する
                    const id = $("#pickUpLocation").val();
                    const name = $("#pickUpLocation option:selected").text();
                    const option = $("<option>").val(id).text(name);
                    options.push(option);
                } else {
                    for (var area_id in res) {
                        var option = $("<option>")
                            .val(res[area_id].id)
                            .text(res[area_id].name);
                        options.push(option);
                    }
                }
                $("#dropOffLocation").append(options);
                $("#dropOffLocation")
                    .prop("disabled", false)
                    .parent()
                    .removeClass("disabled");
            })
            .fail(function () {
                // 失敗したときはpickUp側で選んだエリアをリストに追加する
                const id = $("#pickUpLocation").val();
                const name = $("#pickUpLocation option:selected").text();
                const option = $("<option>").val(id).text(name);
                const options = [option];
                $("#dropOffLocation").append(options);
                $("#dropOffLocation")
                    .prop("disabled", false)
                    .parent()
                    .removeClass("disabled");
            });
    });
    $("#dropOffLocation").change(function () {
        var dropOffLocationId = $(this).val();
        if (Number(dropOffLocationId) === 0) {
            // 検索ボタンを無効化
            $("#submit_oversea_search").prop("disabled", true);
        } else {
            // 検索ボタンを有効化
            $("#submit_oversea_search").prop("disabled", false);
        }
    });
    // 海外検索実行
    $("#submit_oversea_search").click(function () {
        const isOneWay = !$("#return_way_check_oversea").prop("checked");
        const country_id = $("#pickUpCountry").val();
        const city_id = $("#pickUpCity").val();
        const location_id = $("#pickUpLocation").val();

        const return_country_id = isOneWay
            ? $("#dropOffCountry").val()
            : country_id;
        const return_city_id = isOneWay ? $("#dropOffCity").val() : city_id;
        const return_location_id = isOneWay
            ? $("#dropOffLocation").val()
            : location_id;

        const date = $("#pickUpDate").val();
        const time = $("#pickUpTime").val();
        const return_date = $("#dropOffDate").val();
        const return_time = $("#dropOffTime").val();

        const driver_age = $("#driver-age").prop("checked");

        const link =
            "/car-rental/result?country_id=" +
            country_id +
            "&city_id=" +
            city_id +
            "&location_id=" +
            location_id +
            "&date=" +
            date.replace(/\//g, "-") +
            "&time=" +
            time.replace(/:/g, "-") +
            "&return_country_id=" +
            return_country_id +
            "&return_city_id=" +
            return_city_id +
            "&return_location_id=" +
            return_location_id +
            "&return_date=" +
            return_date.replace(/\//g, "-") +
            "&return_time=" +
            return_time.replace(/:/g, "-") +
            "&age=" +
            Number(driver_age);

        location.href = link;
    });

    // 人気のエリア
    $('input[name="select-popularity"]').change(function () {
        var area = $(this).val();
        var country = 0;
        var city = 0;
        switch (area) {
            case "honolulu":
                country = 362;
                city = popularity_city = 19814;
                break;
            case "kona":
                country = 362;
                city = popularity_city = 19710;
                break;
            case "waikiki":
                country = 362;
                city = popularity_city = 19692;
                break;
            case "bangkok":
                country = 341;
                city = popularity_city = 15479;
                break;
            case "las-vegas":
                country = 353;
                city = popularity_city = 15891;
                break;
        }
        $("#pickUpCountry").val(country);
        getOverseaCity(country).done(function (res) {
            var options = [];
            for (var city_id in res) {
                var option = $("<option>")
                    .val(res[city_id].id)
                    .text(res[city_id].name);
                options.push(option);
            }
            $("#pickUpCity").append(options);
            $("#pickUpCity").val(city).change().prop("disabled", false);
            $("#pickUpCity").parent().removeClass("disabled");
        });
    });

    /*
     * 地図エリアのタブ切替
     */
    $('#js-tab a[href^="#js-"]').on("click", function () {
        var index = $(this).index($(this));
        $("#js-mapBody .js-mapLabel").hide();
        $(this.hash).show();
        $(this).parent("li").siblings("li").removeClass("is-active"); // 自分以外の兄弟（隣接）セレクタ
        $(this).parent("li").addClass("is-active");
        return false;
    });

    // Datetime
    // $(".js-input_time").on("change", function () {
    //     setTimeValue(this);
    // });
    $(".calendar").on({
        change: function () {
            var str_id = "SearchReturn";
            if ($(this).attr("id") == "SearchDate") {
                str_id = "Search";
                // 出発日が変更されたとき、到着日も同じ日付に変更する
                changeReturnDate();
            }
            setDateValue(this, str_id);
        },
        click: function () {
            $(this).next(".ui-datepicker-trigger").trigger("click");
        },
    });

    // departure
    $(".tab_departure").on("click", function () {
        $(".tab_departure").removeClass("is_selected");
        $(this).addClass("is_selected");

        var place_name = $(this).data("place");
        $(".select_place_ol").hide();
        $("#" + place_name).show();

        var radio_val = $(this).data("radio");
        $("input[name=place]").val(radio_val);
        switch (radio_val) {
            case 1:
                $("#js-prefectureOptions")
                    .detach()
                    .prependTo("#departure_prefecture");
                if ($("#prefecture").val() == 0) {
                    $("#area_placeholder").show();
                } else {
                    setArea($("#prefecture").val(), 1);
                }
                if ($("#js-prefectureOptions").find("i").length == 0) {
                    $("#js-prefectureOptions")
                        .children("label")
                        .append('<i class="fa fa-unsorted"></i>');
                }
                break;
            case 4:
                $("#js-prefectureOptions")
                    .detach()
                    .prependTo("#departure_station");
                if ($("#prefecture").val() == 0) {
                    $("#station_placeholder").show();
                } else {
                    setStation($("#prefecture").val(), 1);
                }
                if ($("#js-prefectureOptions").find("i").length == 0) {
                    $("#js-prefectureOptions")
                        .children("label")
                        .append('<i class="fa fa-unsorted"></i>');
                }
                break;
            default:
                break;
        }
    });
    $('select[name="prefecture"]').on("change", function () {
        var prefecture = $(this).val();
        if (prefecture == 0) {
            $("#station_placeholder").show();
            $("#area_placeholder").show();
        } else {
            $("#station_placeholder").hide();
            $("#area_placeholder").hide();
        }
        setArea(prefecture, 1);
        setStation(prefecture, 1);
    });
    if ($(".tab_departure.is_selected").length > 0) {
        $(".tab_departure.is_selected").trigger("click");
    } else {
        $(".tab_departure[data-place=departure_station]").trigger("click");
    }

    // returnWay
    $("#return_way_check").on("change", function () {
        if ($(this).prop("checked")) {
            $(".search_select_return").hide();
            $("#SearchReturnWay0").prop("checked", true);
            $("input[name=return_place]").prop("disabled", true);
        } else {
            $(".search_select_return").show();
            $("#SearchReturnWay1").prop("checked", true);
            $("[data-place='return_way_station']").trigger("click");
        }
    });
    $(".tab_return").on("click", function () {
        $(".tab_return").removeClass("is_selected");
        $(this).addClass("is_selected");

        var place_name = $(this).data("place");
        $(".select_return_ol").hide();
        $("#" + place_name).show();

        var radio_val = $(this).data("radio");
        $("input[name=return_place]").prop("disabled", false).val(radio_val);
        switch (radio_val) {
            case 1:
                $("#js-returnPrefectureOptions")
                    .detach()
                    .prependTo("#return_way_prefecture");
                if ($("#return_prefecture").val() == 0) {
                    $("#return_area_placeholder").show();
                } else {
                    setArea($("#return_prefecture").val(), 0);
                }
                if ($("#js-returnPrefectureOptions").find("i").length == 0) {
                    $("#js-returnPrefectureOptions")
                        .children("label")
                        .append('<i class="fa fa-unsorted"></i>');
                }
                break;
            case 4:
                $("#js-returnPrefectureOptions")
                    .detach()
                    .prependTo("#return_way_station");
                if ($("#return_prefecture").val() == 0) {
                    $("#return_station_placeholder").show();
                } else {
                    setStation($("#return_prefecture").val(), 0);
                }
                if ($("#js-returnPrefectureOptions").find("i").length == 0) {
                    $("#js-returnPrefectureOptions")
                        .children("label")
                        .append('<i class="fa fa-unsorted"></i>');
                }
                break;
            default:
                break;
        }
    });
    $('select[name="return_prefecture"]').on("change", function () {
        var return_prefecture = $(this).val();
        if (return_prefecture == 0) {
            $("#return_station_placeholder").show();
            $("#return_area_placeholder").show();
        } else {
            $("#return_station_placeholder").hide();
            $("#return_area_placeholder").hide();
        }
        setArea(return_prefecture, 0);
        setStation(return_prefecture, 0);
    });

    // 会社指定
    $("input[name=area_type]").click(function () {
        var val = $(this).val();
        switch (val) {
            case "0":
                $('input[name="client_id[]"]').prop("checked", false);
                break;
            case "1":
                $('input[data-area_type="1"]').prop("checked", true);
                $('input[data-area_type="2"]').prop("checked", false);
                break;
            case "2":
                $('input[data-area_type="1"]').prop("checked", false);
                $('input[data-area_type="2"]').prop("checked", true);
                break;
        }
    });

    // 絞り込みタブ
    $(".btn_option_tab").on("click", function () {
        $(".btn_option_tab")
            .removeClass("is_selected")
            .attr("aria-selected", "false");
        $(this).addClass("is_selected").attr("aria-selected", "true");

        var section_name = $(this).data("section");
        $(".js-options_tab_panel").hide().attr("aria-hidden", "true");
        $("#search_select_" + section_name)
            .show()
            .attr("aria-hidden", "false");
    });

    // 禁煙・喫煙
    $(".btn_radio_smoking").on("click", function () {
        $(".btn_radio_smoking")
            .removeClass("is_selected")
            .attr("aria-selected", "false");
        $(this).addClass("is_selected").attr("aria-selected", "true");
        var valSelectSmoking = $(this).data("smoking");
        $("input[name=smoking_flg]").val(valSelectSmoking);
    });

    // 車両タイプ
    $("input[name=car_type_select]").on("change", function () {
        // すべて選択する
        if ($(this).val() == 1) {
            $("input[name='car_type[]']").prop("checked", true);
        }
        // すべて解除する
        else if ($(this).val() == 2) {
            $("input[name='car_type[]']").prop("checked", false);
        }
    });

    // submit
    $(".js-btn_search_submit").on("click", function (event) {
        event.preventDefault();
        var gaEventCategory = $(this).data("ga_category");
        var gaEventLabel = $(this).data("ga_label");

        checkCarType();
        if (!checkPlace() || !checkDate() || !checkRange()) {
            return false;
        }

        if (gaEventCategory === void 0 || gaEventLabel === void 0) {
            execSubmit();
        } else {
            // gaイベント
            ga("send", "event", gaEventCategory, "click", gaEventLabel, {
                hitCallback: createFunctionWithTimeout(function () {
                    execSubmit();
                }),
            });
        }
    });

    // 初期表示　
    // $("#over_departure_date").text($("#SearchDate").val());
    // $("#over_return_date").text($("#SearchReturnDate").val());
	
    // $(".js-input_time").trigger("change");
	
    var return_way_val = $("input[name=return_way]:checked").val();
    if (return_way_val > 0) {
        $("#return_way_check").prop("checked", false);
        $(".search_select_return").show();
        var return_place_val = $("input[name=return_place]:checked").val();
        $(".tab_return[data-radio=" + return_place_val + "]").trigger("click");
    }
    if (document.getElementById("search_select_smoking") != null) {
        var valSmoking = $("input[name=smoking_flg]:checked").val();
        $(".btn_radio_smoking[data-smoking=" + valSmoking + "]").trigger(
            "click"
        );
    }
    if (document.getElementById("search_select_car_type") != null) {
        $("#search_select_car_type").show();
    }
});
function execSubmit() {
    $.when(
        $("[name=date]").prop("disabled", true),
        $("[name=return_date]").prop("disabled", true)
    ).done(function () {
        $("#SearchIndexForm").submit();
    });
}
function changeReturnDate() {
    var from_date = $("#SearchDate").val();

    //日付を変更する
    $("#SearchReturnDate").val(from_date).trigger("change");
}
function setDateValue(obj, str_id) {
    // 選択した日付を表示する
    var date_id = $(obj).data("date_id");
    var date_value = $(obj).val();
    $("#" + date_id).text(date_value);

    // 分割してフォームに値を入れる
    var from_date = getSelectDate(str_id);
    var year = from_date.getFullYear();
    var month = from_date.getMonth() + 1;
    var day = from_date.getDate();

    if (str_id == "Search") {
        $("select[name=year]").val(year);
        $("select[name=month]").val(month);
        $("select[name=day]").val(day);
    } else if (str_id == "SearchReturn") {
        $("select[name=return_year]").val(year);
        $("select[name=return_month]").val(month);
        $("select[name=return_day]").val(day);
    }
}
// 選択した時間を表示する
// function setTimeValue(obj) {
//     var time_id = $(obj).data("date_id");
//     var time_value = $("option:selected", obj).text();
//     $("#" + time_id).text(time_value);
// }

// 都道府県に応じたエリアを取得
function setArea(prefecture, rent_flg) {
    if (!prefecture) {
        return;
    }

    var obj = rent_flg ? $("#SearchAreaId") : $("#SearchReturnAreaId");
    var selected = obj.val();
    obj.find("option").remove();
    var areas = area_arr[prefecture] ? area_arr[prefecture] : [];

    for (var i = 0; i < areas.length; i++) {
        var area_id = areas[i]["id"];
        var option = $("<option>").val(areas[i]["id"]).text(areas[i]["name"]);
        if (selected == area_id) {
            option.attr("selected", "selected");
        }
        obj.append(option);
    }

    if (obj.children().length > 0) {
        obj.prop("disabled", false);
    } else {
        obj.prop("disabled", true);
    }
}
// 都道府県に応じた駅を取得
function setStation(prefecture, rent_flg) {
    if (!prefecture) {
        return;
    }

    var obj = rent_flg ? $("#SearchStationId") : $("#SearchReturnStationId");
    var selected = obj.val();
    obj.children().remove();
    var stations = station_arr[prefecture] ? station_arr[prefecture] : [];
    var areas = area_arr[prefecture] ? area_arr[prefecture] : [];

    // 主要駅はoptions[0]に格納しておく
    var options = [];
    // オプション項目を生成する
    for (var area_id in stations) {
        for (var i = 0; i < stations[area_id].length; i++) {
            var station = stations[area_id][i];

            var option = $("<option>").val(station["id"]).text(station["name"]);
            if (selected == station["id"]) {
                option.attr("selected", "selected");
            }

            // 主要駅の場合
            if (station["major"]) {
                if (!options[0]) {
                    options[0] = [$("<optgroup>", { label: "主要駅" })];
                }
                options[0].push(option);

                // その他駅の場合
            } else {
                if (!options[area_id]) {
                    var label_name = "その他";
                    for (k in areas) {
                        if (areas[k]["id"] == area_id) {
                            label_name = areas[k]["name"];
                            break;
                        }
                    }
                    options[area_id] = [$("<optgroup>", { label: label_name })];
                }
                options[area_id].push(option);
            }
        }
    }
    for (var option in options) {
        // セレクトにオプションを追加
        obj.append(options[option]);
    }

    if (obj.children().length > 0) {
        obj.prop("disabled", false);
    } else {
        obj.prop("disabled", true);
    }
}
function getSelectDate(id) {
    //日付を取得する
    var date = $("#" + id + "Date").val();
    var time = $("#" + id + "Time")
        .val()
        .replace(/-/g, ":");
    date = new Date(date + " " + time + ":00");
    return date;
}
// 出発日時と返却日時のチェック
function checkDate() {
    var from = getSelectDate("Search");
    var to = getSelectDate("SearchReturn");
    var now = new Date();
    // now.setTime(now.getTime() + 60 * 60 * 1000);

    if (from.getTime() <= now.getTime()) {
        $("#js-searchform_error").html(
            "出発日時は現在の日時以後に設定してください"
        );
        $("#js-searchform_error").show();
        return false;
    } else {
        if (from.getTime() >= to.getTime()) {
            $("#js-searchform_error").html(
                "返却日時は出発日時より後に設定してください"
            );
            $("#js-searchform_error").show();
            return false;
        }
    }

    return true;
}
function checkCarType() {
    if (document.getElementById("search_select_car_type") == null) return true;

    $("input[name=car_type_select]").prop("disabled", true);

    // 車種がすべて選択された状態の場合、car_type[]要素を送信しない
    if ($("input[name='car_type[]']:not(:checked)").length == 0) {
        $("input[name='car_type[]']").prop("disabled", true);
    }

    return true;
}

function checkPlace() {
    var val = null;

    switch ($('input[name="place"]:checked').val()) {
        case "1":
            val = $("#SearchAreaId").val();
            break;
        //		case '2':
        //			val = $('#SearchBulletTrainId').val();
        //			break;
        case "3":
            val = $("#SearchAirportId").val();
            break;
        case "4":
            val = $("#SearchStationId").val();
            break;
    }

    if (!val || val <= 0) {
        $("#js-searchform_error").html("出発場所と返却場所を選択してください");
        $("#js-searchform_error").show();
        return false;
    }

    val = null;
    if ($('input[name="return_way"]:checked').val() == 0) return true;
    switch ($('input[name="return_place"]:checked').val()) {
        case "1":
            val = $("#SearchReturnAreaId").val();
            break;
        //		case '2':
        //			val = $('#SearchReturnBulletTrainId').val();
        //			break;
        case "3":
            val = $("#SearchReturnAirportId").val();
            break;
        case "4":
            val = $("#SearchReturnStationId").val();
            break;
    }

    if (!val || val <= 0) {
        $("#js-searchform_error").html("出発場所と返却場所を選択してください");
        $("#js-searchform_error").show();
        return false;
    }
    return true;
}

//発着地に北海道・沖縄が含まれる場合、他県に乗り捨てできないようにする
function checkRange() {
    $("#js-searchform_error").show();
    const returnFlg = $("#return_way_check").prop("checked");
    let val = null;
    $("#js-searchform_error").html("");
    $("#js-searchform_error").hide();
    let from_place = "";
    let to_place = "";
    if (!returnFlg) {
        placeCheck();
        if (
            from_place == "hokkaido" ||
            to_place == "hokkaido" ||
            from_place == "okinawa" ||
            to_place == "okinawa"
        ) {
            if (from_place != to_place) {
                val = 1;
            }
        }
        function placeCheck() {
            let depatureAirportGroup = "";
            $("#SearchAirportId")
                .find("option:selected")
                .each(function () {
                    depatureAirportGroup = $(this).parent().attr("label");
                });
            let returnAirportGroup = "";
            $("#SearchReturnAirportId")
                .find("option:selected")
                .each(function () {
                    returnAirportGroup = $(this).parent().attr("label");
                });
            if (
                $(".tab_departure.is_selected").data("place") ==
                "departure_airport"
            ) {
                if (
                    depatureAirportGroup == "北海道" ||
                    (depatureAirportGroup == "主要空港" &&
                        $("#SearchAirportId").val() == 330)
                ) {
                    from_place = "hokkaido";
                } else if (
                    depatureAirportGroup == "沖縄県" ||
                    (depatureAirportGroup == "主要空港" &&
                        $("#SearchAirportId").val() == 326)
                ) {
                    from_place = "okinawa";
                }
            } else {
                if ($("#prefecture").val() == 1) {
                    from_place = "hokkaido";
                } else if ($("#prefecture").val() == 47) {
                    from_place = "okinawa";
                }
            }
            if (
                $(".tab_return.is_selected").data("place") ==
                "return_way_airport"
            ) {
                if (
                    returnAirportGroup == "北海道" ||
                    (returnAirportGroup == "主要空港" &&
                        $("#SearchReturnAirportId").val() == 330)
                ) {
                    to_place = "hokkaido";
                } else if (
                    returnAirportGroup == "沖縄県" ||
                    (returnAirportGroup == "主要空港" &&
                        $("#SearchReturnAirportId").val() == 326)
                ) {
                    to_place = "okinawa";
                }
            } else {
                if ($("#return_prefecture").val() == 1) {
                    to_place = "hokkaido";
                } else if ($("#return_prefecture").val() == 47) {
                    to_place = "okinawa";
                }
            }
        }
    }
    if (val != null) {
        $("#js-searchform_error").html(
            "北海道および沖縄は他の都道府県に乗り捨てできません"
        );
        $("#js-searchform_error").show();
        return false;
    }
    return true;
}

// 海外レンタカー
const baseUrl = "https://dev-car-rental-api.skyticket.com/v1/jp/searchPlace";
function getOverseaCountry(pickup_area_id = null) {
    var url = baseUrl + "/countries";
    if (pickup_area_id) {
        url += "?locationId=" + pickup_area_id;
    }
    return $.ajax({
        type: "GET",
        dataType: "json",
        timeout: 10000,
        url: url,
    });
}

function getOverseaCity(country_id, pickup_area_id = null) {
    var url = baseUrl + "/countries/" + country_id + "/cities";
    if (pickup_area_id) {
        url += "?locationId=" + pickup_area_id;
    }
    return $.ajax({
        type: "GET",
        dataType: "json",
        timeout: 10000,
        url: url,
    });
}

function getOverseaArea(city_id) {
    var url = baseUrl + "/cities/" + city_id + "/areas";
    return $.ajax({
        type: "GET",
        dataType: "json",
        timeout: 10000,
        url: url,
    });
}

function setDropOffCountry() {
    var pickUpLocationId = $("#pickUpLocation").val();
    if (Number(pickUpLocationId) !== 0) {
        $("#dropOffCountry").hide();
        $("#dropOffLoading").show();
        getOverseaCountry(pickUpLocationId)
            .done(function (res, status, jqXHR) {
                let options = [];
                if (jqXHR.status !== 200) {
                    // 空が返ってきたときはpickUp側で選んだ国をリストに追加する
                    const id = $("#pickUpCountry").val();
                    const name = $("#pickUpCountry option:selected").text();
                    const option = $("<option>").val(id).text(name);
                    options.push(option);
                } else {
                    for (var country_id in res) {
                        if (res[country_id].id === 273) continue;

                        var option = $("<option>")
                            .val(res[country_id].id)
                            .text(res[country_id].name);
                        options.push(option);
                    }
                }
                $("#dropOffCountry").append(options);
                $("#dropOffLoading").hide();
                $("#dropOffCountry").show().prop("disabled", false);
                $("#dropOffCountry").parent().removeClass("disabled");
            })
            .fail(function () {
                // 失敗したときはpickUp側で選んだ国をリストに追加する
                const id = $("#pickUpCountry").val();
                const name = $("#pickUpCountry option:selected").text();
                const option = $("<option>").val(id).text(name);
                const options = [option];
                $("#dropOffCountry").append(options);

                $("#dropOffLoading").hide();
                $("#dropOffCountry").show().prop("disabled", false);
                $("#dropOffCountry").parent().removeClass("disabled");
            });
    }
}

/*
$('input[id=SearchReturnWay0]').change( function(){
	$('.search_hidden_box').toggle(false);
});
$('input[id=SearchReturnWay1]').change( function(){
	$('.search_hidden_box').toggle(true);
});
*/
