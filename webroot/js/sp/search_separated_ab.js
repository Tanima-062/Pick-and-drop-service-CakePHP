// SP ABテストのパターンの時に使う「条件変更」「絞り込み」分離されているモーダルで使う

$(function () {
    var $form = $('#SearchIndexFormAB');

    var obj_search_form = checkFormAB();

    if (!obj_search_form) {
        return false;
    }

    // departure 出発店舗が未定の場合のみ表示
    if ($form.find(".search_select_departure").length > 0) {
        $form.on("click", ".tab_departure", function () {
            $form.find(".tab_departure").removeClass("is_selected");
            $(this).addClass("is_selected");

            var place_name = $(this).data("place");
            $form.find(".select_place_ol").hide();
            $form.find("#" + place_name).show();

            var radio_val = $(this).data("radio");
            $form.find("input[name=place]").val(radio_val);
            switch (radio_val) {
                case 1:
                    $form.find("#js-prefectureOptions")
                        .detach()
                        .prependTo($form.find("#departure_prefecture"));
                    if ($form.find("#prefecture").val() == 0) {
                      $form.find("#area_placeholder").show();
                    } else {
                        setAreaAB($form.find("#prefecture").val(), 1);
                    }
                    break;
                case 4:
                    $form.find("#js-prefectureOptions")
                        .detach()
                        .prependTo($form.find("#departure_station"));
                    if ($form.find("#prefecture").val() == 0) {
                        $form.find("#station_placeholder").show();
                    } else {
                        setStationAB($form.find("#prefecture").val(), 1);
                    }
                    break;
                default:
                    break;
            }
        });
        $form.on("change", 'select[name="prefecture"]', function () {
            var prefecture = $(this).val();
            if (prefecture == 0) {
                $form.find("#station_placeholder, #area_placeholder").show();
            } else {
                $form.find("#station_placeholder, #area_placeholder").hide();
            }
            setAreaAB(prefecture, 1);
            setStationAB(prefecture, 1);
        });
        if ($form.find(".tab_departure.is_selected").length > 0) {
            $form.find(".tab_departure.is_selected").trigger("click");
        } else {
            $form.find(".tab_departure[data-place=departure_airport]").trigger("click");
        }
    }

    $form.on("change", ".calendar", function () {
        if ($(this).attr("id") == "SearchDate") {
            changeReturnDateAB();
        }
        setDateValueAB(this);
    });

    // returnWay
    $form.on("change", "#return_way_check_ab", function () {
        if ($(this).prop("checked")) {
            $form.find(".search_select_return").hide();
            $form.find("#SearchReturnWay0").prop("checked", true);
        } else {
            $form.find(".search_select_return").show();
            $form.find("[data-place='return_way_airport']").trigger("click");
        }
    });
    $form.on("click", ".tab_return", function () {
        $form.find(".tab_return").removeClass("is_selected");
        $(this).addClass("is_selected");

        var place_name = $(this).data("place");
        $form.find(".select_return_ol").hide();
        $form.find("#" + place_name).show();

        var radio_val = $(this).data("radio");
        $form.find("#SearchReturnWay" + radio_val).prop("checked", true);
        switch (radio_val) {
            case 1:
                $form.find("#js-returnPrefectureOptions")
                    .detach()
                    .prependTo($form.find("#return_way_prefecture"));
                if ($form.find("#return_prefecture").val() == 0) {
                    $form.find("#return_area_placeholder").show();
                } else {
                    setAreaAB($form.find("#return_prefecture").val(), 0);
                }
              break;
            case 4:
                $form.find("#js-returnPrefectureOptions")
                    .detach()
                    .prependTo($form.find("#return_way_station"));
                if ($form.find("#return_prefecture").val() == 0) {
                    $form.find("#return_station_placeholder").show();
                } else {
                    setStationAB($form.find("#return_prefecture").val(), 0);
                }
                break;
            default:
                break;
        }
    });
    $form.on("change", 'select[name="return_prefecture"]', function () {
        var return_prefecture = $(this).val();
        if (return_prefecture == 0) {
            $form.find("#return_station_placeholder, #return_area_placeholder").show();
        } else {
            $form.find("#return_station_placeholder, #return_area_placeholder").hide();
        }
        setAreaAB(return_prefecture, 0);
        setStationAB(return_prefecture, 0);
  });

  $form.on("click", ".js-btn_search_submit", function (event) {
      event.preventDefault();
      var gaEventCategory = $(this).data("ga_category");
      var gaEventLabel = $(this).data("ga_label");

      if (!checkPlaceAB() || !checkDateAB() || !checkRangeAB()) {
          return false;
      }

      if (gaEventCategory === void 0 || gaEventLabel === void 0) {
          obj_search_form.submit();
      } else {
          ga("send", "event", gaEventCategory, "click", gaEventLabel, {
              hitCallback: createFunctionWithTimeout(function () {
                  obj_search_form.submit();
              }),
          });
        }
    });

    setDateValueAB($form.find("#SearchDate"));
    setDateValueAB($form.find("#SearchReturnDate"));

    var return_way_val = $form.find("input[name=return_way]:checked").val();
    if (return_way_val > 0) {
        $form.find("#return_way_check_ab").prop("checked", false);
        $form.find(".search_select_return").show();
        $form.find(".tab_return[data-radio=" + return_way_val + "]").trigger("click");
    } else {
        $form.find("#return_way_check_ab").trigger("change");
    }

    $form.find(".js-search_reset").on("click", function (event) {
        event.preventDefault();
        $form.find('.select_car_type .form-checkbox').prop('checked', false);
        $form.find('.select_option .form-checkbox').prop('checked', false);
        $form.find('input[name="smoking_flg"][value="2"]').prop('checked', true);
        $form.find('#select_client_id').val('0');
    })
});

// 選択した日付のうち、月と日のみ表示する
function setDateValueAB(obj) {
    var $form = $('#SearchIndexFormAB');
    var date_id = $(obj).data("date_id");
    var slice_date = $(obj).val().slice(5);
    $form.find("#" + date_id).text(slice_date);
}

// 都道府県に応じたエリアを取得
function setAreaAB(prefecture, rent_flg) {
    if (!prefecture) {
        return;
    }

    var $form = $('#SearchIndexFormAB');
    var obj = rent_flg ? $form.find("#SearchAreaId") : $form.find("#SearchReturnAreaId");
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
function setStationAB(prefecture, rent_flg) {
    if (!prefecture) {
        return;
    }

    var $form = $('#SearchIndexFormAB');
    var obj = rent_flg ? $form.find("#SearchStationId") : $form.find("#SearchReturnStationId");
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

function changeReturnDateAB() {
    var $form = $('#SearchIndexFormAB');
    var from_date = getSelectDateAB("Search");

    var year = from_date.getFullYear();
    var month = from_date.getMonth() + 1;
    var day = from_date.getDate();
    var return_date =
        year + "/" + ("00" + month).slice(-2) + "/" + ("00" + day).slice(-2);

    //日付を変更する
    $("#SearchReturnDate").val(return_date).trigger("change");
    $form.find("#SearchReturnDate").val(return_date).trigger("change");
}
function getSelectDateAB(id) {
    //日付を取得する
    var $form = $('#SearchIndexFormAB');
    var date = $form.find("#" + id + "Date").val();
    var time = $form.find("#" + id + "Time")
        .val()
        .replace(/-/g, ":");
    date = new Date(date + " " + time + ":00");
    return date;
}
function checkFormAB() {
    // フォームの判定
    var is_search =
        document.getElementById("SearchIndexFormAB") != null ? true : false;

    if (is_search) {
        return $("#SearchIndexFormAB");
    } else {
        return false;
    }
}
// 出発日時と返却日時のチェック
function checkDateAB() {
    var $form = $('#SearchIndexFormAB');
    var from = getSelectDateAB("Search");
    var to = getSelectDateAB("SearchReturn");
    var now = new Date();
    // now.setTime(now.getTime() + 60 * 60 * 1000);

    if (from.getTime() <= now.getTime()) {
        $form.find("#js-searchform_error").html(
            "出発日時は現在の日時以降に設定してください"
        );
        $form.find("#js-searchform_error").show();
        return false;
    } else {
        if (from.getTime() >= to.getTime()) {
            $form.find("#js-searchform_error").html(
                "返却日時は出発日時より後に設定してください"
            );
            $form.find("#js-searchform_error").show();
            return false;
        }
    }

    return true;
}
// 出発場所と返却場所のチェック
function checkPlaceAB() {
    var $form = $('#SearchIndexFormAB');
    var val = null;

    switch ($form.find('input[name="place"]:checked').val()) {
        case "1":
            val = $form.find("#SearchAreaId").val();
            break;
        case "2":
            val = $form.find("#SearchBulletTrainId").val();
            break;
        case "3":
            val = $form.find("#SearchAirportId").val();
            break;
        case "4":
            val = $form.find("#SearchStationId").val();
            break;
    }

    if (!val || val <= 0) {
        $form.find("#js-searchform_error").html("出発場所と返却場所を選択してください");
        $form.find("#js-searchform_error").show();
        return false;
    }

    val = null;
    switch ($form.find('input[name="return_way"]:checked').val()) {
        case "0":
            return true;
            break;
        case "1":
            val = $form.find("#SearchReturnAreaId").val();
            break;
        case "2":
            val = $form.find("#SearchReturnBulletTrainId").val();
            break;
        case "3":
            val = $form.find("#SearchReturnAirportId").val();
            break;
        case "4":
            val = $form.find("#SearchReturnStationId").val();
            break;
    }

    if (!val || val <= 0) {
        $form.find("#js-searchform_error").html("出発場所と返却場所を選択してください");
        $form.find("#js-searchform_error").show();
        return false;
    }

    return true;
}

//発着地に北海道・沖縄が含まれる場合、他県に乗り捨てできないようにする
function checkRangeAB() {
    var $form = $('#SearchIndexFormAB');
    $form.find("#js-searchform_error").show();
    const returnFlg = $form.find("#return_way_check_ab").prop("checked");
    let val = null;
    $form.find("#js-searchform_error").html("");
    $form.find("#js-searchform_error").hide();
    let from_place = "";
    let to_place = "";
    if (!returnFlg) {
        placeCheckAB();
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
        function placeCheckAB() {
            let depatureAirportGroup = "";
            $form.find("#SearchAirportId")
                .find("option:selected")
                .each(function () {
                    depatureAirportGroup = $(this).parent().attr("label");
                });
            let returnAirportGroup = "";
            $form.find("#SearchReturnAirportId")
                .find("option:selected")
                .each(function () {
                    returnAirportGroup = $(this).parent().attr("label");
                });
            if (
                $form.find(".tab_departure.is_selected").data("place") ==
                "departure_airport"
            ) {
                if (
                    depatureAirportGroup == "北海道" ||
                    (depatureAirportGroup == "主要空港" &&
                        $form.find("#SearchAirportId").val() == 330)
                ) {
                    from_place = "hokkaido";
                } else if (
                    depatureAirportGroup == "沖縄県" ||
                    (depatureAirportGroup == "主要空港" &&
                        $form.find("#SearchAirportId").val() == 326)
                ) {
                    from_place = "okinawa";
                }
            } else {
                if ($form.find("#prefecture").val() == 1) {
                    from_place = "hokkaido";
                } else if ($form.find("#prefecture").val() == 47) {
                    from_place = "okinawa";
                }
            }
            if (
                $form.find(".tab_return.is_selected").data("place") ==
                "return_way_airport"
            ) {
                if (
                    returnAirportGroup == "北海道" ||
                    (returnAirportGroup == "主要空港" &&
                        $form.find("#SearchReturnAirportId").val() == 330)
                ) {
                    to_place = "hokkaido";
                } else if (
                    returnAirportGroup == "沖縄県" ||
                    (returnAirportGroup == "主要空港" &&
                        $form.find("#SearchReturnAirportId").val() == 326)
                ) {
                    to_place = "okinawa";
                }
            } else {
                if ($form.find("#return_prefecture").val() == 1) {
                    to_place = "hokkaido";
                } else if ($form.find("#return_prefecture").val() == 47) {
                    to_place = "okinawa";
                }
            }
        }
    }
    if (val != null) {
        $form.find("#js-searchform_error").html(
            "北海道および沖縄は他の都道府県に乗り捨てできません"
        );
        $form.find("#js-searchform_error").show();
        return false;
    }
    return true;
}
