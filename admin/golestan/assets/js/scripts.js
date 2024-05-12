// A $( document ).ready() block.
let board = null;

let sec = null;
let baseUrl = "/admin/golestan";
var looper = $.Deferred().resolve();
var looperItems = new $.Deferred().resolve();
$(document).ready(function () {
    console.log("ready!");
    board = $("#export");
    sec = $("#key");
    $(".RegisterData").on("click", function () {
        looper = $.Deferred().resolve();
        looperItems = new $.Deferred().resolve();
        $('.close-btn').hide();
        $('#ex-modal').modal({
            backdrop: 'static',
            keyboard: false
        });

        board.text("");
        let security = sec.val();
        let target = $(this);

        let items = $('#centers').val().split(",");
        let term = $('#term').val();
        let crs_degree = $('#crs_degree').val();
        let edu_group = $('#edu_group').val();
        let crs_group = $('#crs_group').val();
        let course = $('#course').val();
        // let category = $('#category').val();

        let params = {
            term: term,
            crs_degree: crs_degree,
            edu_group: edu_group,
            crs_group: crs_group,
            course: course,
            // category: category
        }
        console.log(params);
        if (!items) {
            console.log('error');
            board.append(addText("Centers has wrong value"));
            return;
        }
        let url = target.attr('data-url');
        let action = target.attr('data-action');
        if (!action || !url) {
            console.log('error');
            return;
        }
        if (action == "students") {
            items = [1];
        }
        if (action == "courses" || action == "enroll") {
            seuqunce(security, items, url, action, params, true);

        } else {
            seuqunce(security, items, url, action, params);
        }
    })
});

var addText = function (text, isheader = false) {
    let cls = 'info';
    if (isheader)
        cls = 'info-header';
    return "<div class='d-block " + cls + "'>" + text + "</div>";
};
var seuqunce = function (key, items, url, action, params, unenroll = false) {

    // go through each item and call the ajax function
    $.when.apply($, $.map(items, function (item, i) {

        looper = looper.then(function () {
            // trigger ajax call with item data
            console.log(item);
            //deferred.resolve(item);
            return ajax_request(key, item, url, action, params, unenroll);
        });
        return looper;
    })).then(function () {
        // run this after all ajax calls have completed
        board.append(addText("پایان ثبت اطلاعات", true));
        board.scrollTop(board[0].scrollHeight);
        $('.close-btn').show();
        console.log('Done!');
    });
};
var ajax_request = function (key, item, url, action, params, unenroll = false) {
    var deferred = $.Deferred();
    console.log(item);
    board.append(addText('در حال دریافت اطلاعات مرکز شماره ' + item, true));
    console.log(board);
    board.scrollTop(board[0].scrollHeight);
    console.log("Item: " + item);
    $.ajax({
        url: baseUrl + '/' + url,
        dataType: "json",
        type: "POST",
        data: {key: key, action: action, center: item, params: params},
        success: function (data) {
            // do something here
            if (!data.success) {
                deferred.reject("error");
                if (data.msg) {
                    board.append(addText(data.msg));
                } else {
                    board.append(addText("Empty result"));
                }

                return;
            }
            console.log(data);
            board.append(addText(data.msg, true));
            board.scrollTop(board[0].scrollHeight);
            console.log(data.items);
            if (data.items == "[]") {
                deferred.reject("error");
                board.append(addText("هیچ داده ای یافت نشد"));
                return;
            }
            if (data.items.length == 0) {
                deferred.resolve(data);
                board.append(addText("هیچ داده ای یافت نشد"));
                board.scrollTop(board[0].scrollHeight);
            }
            $.when.apply($, $.map(data.items, function (item, i) {
                looperItems = looperItems.then(function () {
                    // trigger ajax call with item data
                    console.log("Calling for item");
                    console.log(item);
                    return ajax_request_items(key, item, action, params);


                });
                return looperItems;
            })).then(function () {
                // run this after all ajax calls have completed
                console.log('Done!');
                if (unenroll) {
                    $.ajax({
                        async: false,
                        url: baseUrl + '/register.php',
                        dataType: "json",
                        type: "POST",
                        data: {key: key, action: "unenroll",params: params, data: {action: action, center: item}},
                        success: function (dataIn) {
                            if (!dataIn.success) {
                                board.append(addText("ERROR"));
                                board.append(addText(dataIn.msg));
                                return;
                            }
                            board.append(addText(dataIn.msg, true));

                        }

                    });
                    deferred.resolve(data);
                } else {
                    deferred.resolve(data);
                }


            });
            // mark the ajax call as completed

        },
        error: function (error) {
            // mark the ajax call as failed
            board.append(addText("خطا در اتصال به سرور گلستان"));
            board.append(addText(error.status));
            board.append(addText(error.statusText));
            board.scrollTop(board[0].scrollHeight);
            console.log('Processing Items failed!');
            console.log(error);
            $('.close-btn').show();
            deferred.reject(error);
        }
    });

    return deferred.promise();
};
var ajax_request_items = function (key, item, action, params) {
    // console.log('Processing Items!');
    var deferred2 = $.Deferred();
    $.ajax({
        url: baseUrl + '/register.php',
        dataType: "json",
        type: "POST",
        data: {key: key, action: action, data: item, params: params},
        success: function (data) {
            if (!data.success) {
                deferred2.reject("error");
                console.log("Empty result");
                console.log(data);
                board.append(addText("ERROR"));
                board.append(addText(data.msg));
                return;
            }
            // do something here
            console.log(data);
            board.append(addText(data.msg));
            board.scrollTop(board[0].scrollHeight);
            // mark the ajax call as completed
            deferred2.resolve(data);
        },
        error: function (error) {
            // mark the ajax call as failed
            board.append(addText("خطا در اتصال به سرور گلستان"));
            board.append(addText(error.status));
            board.append(addText(error.statusText));
            console.log('Processing Items failed!');
            $('.close-btn').show();
            deferred2.reject(error);
        }
    });

    return deferred2.promise();
};
