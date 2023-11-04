/**
 * カレンダー
 */
(function () {
    $(function () {
        // init
        var stationName = bestPriceCal.stationName;
        var calendar = bestPriceCal.calendar;

        // テンプレート初期化
        var leadTemplate = template($("#best_price_cal_template_lead").text());
        var tabTemplate = template($("#best_price_cal_template_tab").text());
        var arrowTemplate = template(
            $("#best_price_cal_template_arrow").text()
        );
        var navTemplate = template($("#best_price_cal_template_nav").text());
        var calHeader = $("#best_price_cal_template_cal_header").text();
        var calBodyTemplate = template(
            $("#best_price_cal_template_cal_body").text()
        );
        var calDayTemplate = template(
            $("#best_price_cal_template_cal_day").text()
        );
        var calFooter = $("#best_price_cal_template_cal_footer").text();

        // 要素初期化
        var $lead = $("#best_price_cal_lead");
        var $tab = $("#best_price_cal_tab");
        var $nav = $("#best_price_cal_nav");
        var $cal = $("#best_price_cal_calendar");

        // イベント
        $("#best_price_cal").on("click", "[data-nav]", function () {
            var year = $(this).attr("data-year");
            var month = $(this).attr("data-month");
            var type = $(this).attr("data-type");
            renderCalendar(year, month, type, false);
        });
        $("#best_price_cal").on("click", "[data-tab]", function () {
            var parent = $(this).parent();
            var year = parent.attr("data-year");
            var month = parent.attr("data-month");
            var type = $(this).attr("data-tab");
            renderCalendar(year, month, type, false);
        });

        // カレンダーの最初の月を表示
        var firstYear = Object.keys(calendar)[0];
        var firstMonth = Object.keys(calendar[firstYear])[0];
        renderCalendar(firstYear, firstMonth, 1, true);

        /**
         * カレンダーをレンダリング
         */
        function renderCalendar(year, month, currentType, renderLead) {
            year = parseInt(year);
            month = parseInt(month);

            // 該当するカレンダーがなければ何もしない
            if (
                typeof calendar[year] === "undefined" ||
                typeof calendar[year][month] === "undefined"
            ) {
                return;
            }

            // 前月取得
            var prevMonthNum = month - 1;
            var prevYearNum = year;
            if (prevMonthNum <= 0) {
                prevMonthNum = 12;
                prevYearNum = prevYearNum - 1;
            }
            var arrow = '<i class="icm-right-arrow"></i>';
            var arrowSp = '<i class="icm-right-arrow"></i>';
            var prevMonth =
                typeof calendar[prevYearNum] === "undefined" ||
                typeof calendar[prevYearNum][prevMonthNum] === "undefined"
                    ? null
                    : {
                          year: prevYearNum,
                          month: prevMonthNum,
                          arrow: arrow,
                          arrowSp: arrowSp,
                          label: "Prev",
                          nav: "prev",
                          currentType: currentType,
                      };
            // 次月取得
            var nextMonthNum = month + 1;
            var nextYearNum = year;
            if (nextMonthNum >= 13) {
                nextMonthNum = 1;
                nextYearNum = nextYearNum + 1;
            }
            var nextMonth =
                typeof calendar[nextYearNum] === "undefined" ||
                typeof calendar[nextYearNum][nextMonthNum] === "undefined"
                    ? null
                    : {
                          year: nextYearNum,
                          month: nextMonthNum,
                          arrow: arrow,
                          arrowSp: arrowSp,
                          label: "Next",
                          nav: "next",
                          currentType: currentType,
                      };
            // 月間最安値を取得
            var monthCalendar = calendar[year][month];
            var priceKeys = [1, 2, 3, 5, 9];
            var days = Object.keys(monthCalendar);
            var monthlyBestPrice = "";
            var monthlyBestPriceList = {};
            days.forEach(function (day) {
                priceKeys.forEach(function (key) {
                    var price = monthCalendar[day][key];

                    // 価格が空の場合は何もしない
                    if (price === "") {
                        return false;
                    }

                    // 全タイプ中の最安値
                    if (monthlyBestPrice === "" || monthlyBestPrice > price) {
                        monthlyBestPrice = price;
                    }

                    // タイプ別最安値
                    if (
                        typeof monthlyBestPriceList[key] === "undefined" ||
                        monthlyBestPriceList[key] > price
                    ) {
                        monthlyBestPriceList[key] = price;
                    }
                });
            });

            // リードをレンダリング
            if (typeof renderLead !== "undefined" && renderLead) {
                $lead.html(
                    leadTemplate({
                        monthString: year + "年" + month + "月",
                        monthlyBestPrice: numberformat(
                            monthlyBestPrice,
                            "&yen",
                            "〜"
                        ),
                        stationName: stationName,
                    })
                );
            }

            // タブをレンダリング
            $tab.html(
                tabTemplate({
                    year: year,
                    month: month,
                    selectedType1: currentType == 1,
                    selectedType2: currentType == 2,
                    selectedType3: currentType == 3,
                    selectedType95: currentType == 95,
                    bestPriceType1: numberformat(
                        monthlyBestPriceList["1"],
                        "&yen;",
                        "〜"
                    ),
                    bestPriceType2: numberformat(
                        monthlyBestPriceList["2"],
                        "&yen;",
                        "〜"
                    ),
                    bestPriceType3: numberformat(
                        monthlyBestPriceList["3"],
                        "&yen;",
                        "〜"
                    ),
                    bestPriceType95: numberformat(
                        Math.min(
                            monthlyBestPriceList["9"],
                            monthlyBestPriceList["5"]
                        ),
                        "&yen;",
                        "〜"
                    ),
                })
            );

            // ナビをレンダリング
            $nav.html(
                navTemplate({
                    currentYearMonth:
                        year + "/" + (month < 10 ? "0" : "") + month,
                    next: arrowTemplate(nextMonth),
                    prev: arrowTemplate(prevMonth),
                    currentType: currentType,
                })
            );

            // カレンダー本体をレンダリング
            var bestPrice =
                currentType === "95"
                    ? Math.min(monthlyBestPriceList[5], monthlyBestPriceList[9])
                    : monthlyBestPriceList[currentType];
            renderTypeCalender(year, month, currentType, bestPrice);
        }

        /**
         * 指定されたタイプのカレンダーを描画
         */
        function renderTypeCalender(year, month, currentType, bestPrice) {
            year = parseInt(year);
            month = parseInt(month);
            bestPrice = parseInt(bestPrice);

            // searchLink
            // /rentacar/searches?
            // place=4&station_id=70&prefecture=1&year=2019&month=03&day=27
            // &time=11-00&return_way=0&return_year=2019&return_month=03
            // &return_day=27&return_time=17-00&car_type[0]=2
            var searchLink = [
                "place=4",
                "station_id=" + bestPriceCal.stationId,
                "prefecture=" + bestPriceCal.prefectureId,
                "year=" + year,
                "month=" + (month < 10 ? "0" : "") + month,
                "time=11-00",
                "return_way=0",
                "return_year=" + year,
                "return_month=" + (month < 10 ? "0" : "") + month,
                "return_time=17-00",
            ];

            // searchLink SP版
            //rentacar/searches?
            // date=2019%2F04%2F30&time=11-00
            // &return_date=2019%2F04%2F30&return_time=17-00
            // &airport_id=0&area_id=1&prefecture=1&station_id=70
            // &return_airport_id=0&return_prefecture=0&place=4&return_way=0
            // &car_type[]=2
            var searchLinkSp = [
                "time=11-00",
                "return_time=17-00",
                "airport_id=0",
                "prefecture=" + bestPriceCal.prefectureId,
                "station_id=" + bestPriceCal.stationId,
                "return_airport_id=0",
                "return_prefecture=0",
                "place=4",
                "return_way=0",
            ];
            var monthString = [year, (month < 10 ? "0" : "") + month].join("/");

            if (currentType === "95") {
                searchLink.push("car_type[0]=5");
                searchLink.push("car_type[1]=9");
                searchLinkSp.push("car_type[]=5");
                searchLinkSp.push("car_type[]=9");
            } else {
                searchLink.push("car_type[0]=" + currentType);
                searchLinkSp.push("car_type[]=" + currentType);
            }
            searchLink = searchLink.join("&");
            searchLinkSp = searchLinkSp.join("&");

            // 該当するカレンダーがなければ何もしない
            if (
                typeof calendar[year] === "undefined" ||
                typeof calendar[year][month] === "undefined"
            ) {
                return;
            }

            // 週データを用意
            var currentCal = calendar[year][month];
            var length = Object.keys(currentCal).length;
            var days = [];
            var weeks = [];
            for (var i = 1; i < length + 1; i++) {
                var current = currentCal[i];

                // 月頭パディング
                if (i === 1) {
                    for (var j = 0; j < current.day; j++) {
                        days.push(null);
                    }
                }

                // 価格を設定
                var price =
                    currentType == 95
                        ? Math.min(current[5], current[9])
                        : current[currentType];
                if (!price) {
                    price = "";
                }
                current.price = price;
                current.date = i;
                days.push(current);

                // 月末パディング
                if (i === length) {
                    for (var j = 0; j < 6 - current.day; j++) {
                        days.push(null);
                    }
                }
            }
            for (var i = 0; i < days.length / 7; i++) {
                weeks[i] = [];
                for (var j = 0; j < 7; j++) {
                    weeks[i].push(days[i * 7 + j]);
                }
            }

            // レンダリング
            var weekdayNames = [
                "sun",
                "mon",
                "tue",
                "wed",
                "thu",
                "fri",
                "sat",
            ];
            var html = [];
            html.push(calHeader);
            weeks.forEach(function (week) {
                var weekdays = {};
                weekdayNames.forEach(function (name, index) {
                    var day = week[index];
                    if (!day) {
                        weekdays[name] = "";
                    } else {
                        var price = day.price;
                        var link = encodeURI(
                            searchLink +
                                "&day=" +
                                day.date +
                                "&return_day=" +
                                day.date
                        );
                        var dateString =
                            monthString +
                            "/" +
                            (day.date < 10 ? "0" : "") +
                            day.date;
                        var linkSp = encodeURI(
                            searchLinkSp +
                                "&date=" +
                                dateString +
                                "&return_date=" +
                                dateString
                        ).replace("/", "%2F");

                        weekdays[name] = calDayTemplate({
                            date: day.date,
                            price: numberformat(price, "&yen;"),
                            priceSp: numberformat(price),
                            isPublicHoliday: day.is_holiday,
                            isBestPrice: price === bestPrice,
                            href: price
                                ? 'href="/rentacar/searches?' + link + '"'
                                : "",
                            hrefSp: price
                                ? 'href="/rentacar/searches?' + linkSp + '"'
                                : "",
                        });
                    }
                });
                html.push(calBodyTemplate(weekdays));
            });
            html.push(calFooter);
            $cal.html(html.join(""));
        }

        /**
         * 数値をカンマ区切りにフォーマットする
         */
        function numberformat(num, before, after) {
            // 数値でなければ空の文字列を返す
            if (!num) {
                return "";
            }
            var number = String(num).replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");

            if (typeof before !== "undefined") {
                number = before + number;
            }

            if (typeof after !== "undefined") {
                number += after;
            }
            return number;
        }

        /**
         * テンプレート
         */
        function template(template) {
            return function (options) {
                return _template(template, options);
            };
        }
        function _template(template, options) {
            if (!options) {
                return "";
            }

            Object.keys(options).forEach(function (key) {
                var value = options[key];
                if (value === null) {
                    value = "";
                }
                var re = new RegExp("<%= " + key + " *%>");
                template = template.replace(re, value);
            });
            return template;
        }
    });
})();

/**
 * その他
 */
$(function () {
    // nearby
    $(".station2_nearby_title")
        .click(function () {
            if ($(this).attr("data-selected") === "false") {
                $(this).attr("data-selected", true);
                $(this)
                    .siblings(".station2_nearby_list")
                    .slideDown(300)
                    .attr("aria-expanded", true);
            } else {
                $(this).attr("data-selected", false);
                $(this)
                    .siblings(".station2_nearby_list")
                    .slideUp(300)
                    .attr("aria-expanded", false);
            }
        })
        .attr("data-selected", false);
    $(".station2_nearby_list").hide().attr("aria-expanded", false);
});
