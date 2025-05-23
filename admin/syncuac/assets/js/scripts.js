// A $( document ).ready() block.

let board = null;
let pager = null;
let ele_index = null;
let ele_level = null;
let loaderHtml = '<div class="loader-btn" ></div>';
let sec = null;
let baseUrl = "/admin/syncuac";
var looper;
var looperItems;
var sq;
var isCanceled = false;
var hasErrors  = false;
let insertHTMLErrors = [];
let insertErrors = [];
var Status = {
    fatal_error: -2,
    error: -1,
    success: 1,
    next: 2,
    end: 3,
    cancel: 4,
    save: 11,
    edit: 12,
    skip: 13
}
var Step = {
    importStudents: 1,
    importLessons: 2,
    importTeachers: 3,
    importEnrollments: 4,
    importTeacherEnrollments: 5,

}
var offset,
    clock,
    interval;
var clockAll = 0;
var offsetAll = 0;
var StudyLevelText = {
    3: "کارشناسي",
    5: "کارشناسي ارشدناپيوسته",
    6: "کارشناسي ناپيوسته",
    7: "دکتري تخصصي",
    8: "دکتري عمومي",
}
var StepsText = {
    1: "دریافت و ثبت اطلاعات دانشپذیران",
    2: "دریافت و ثبت اطلاعات دروس",
    3: "دریافت و ثبت اطلاعات اساتید",
    4: "دریافت اطلاعات و ثبت نام دانشپذیران",
    5: "دریافت اطلاعات و ثبت نام اساتید",
}

let ele_saved;
let ele_edited;
let ele_skipped;
let ele_failed;


let edited = 0;
let skipped = 0;
let saved = 0;
let failed = 0;

var options = {};
options.delay = options.delay || 1000;
var page_text = "صفحه";
var current_step;

function getInput(ele) {
    let parent = $("#" + ele);
    let req = false;
    let num = false;
    let items = [];
    items['faculty'] = parent.find(".faculty").first();
    items['term'] = parent.find(".term").first();
    items['study_level'] = parent.find(".study-level").first();
    items['lesson'] = parent.find(".lesson").first();
    items['group'] = parent.find(".group").first();
    items['professor'] = parent.find(".professor").first();
    items['student'] = parent.find(".student").first();
    let pages = Array.from(Array(10000).keys());
    pages = pages.splice(1);
    for (var key in items) {
        let obj = items[key];
        if (obj.length === 0) {
            continue;
        }
        // console.log(obj);
        let attr = obj.attr('required');
        obj.parent().removeClass("error");
        if (typeof attr !== typeof undefined && attr !== false) {
            if (obj.val().length === 0) {
                obj.parent().addClass("error");
                req = true;
            }

        }
        if (obj.hasClass('numeric') && obj.val().length !== 0) {
            if (!(/^\d+$/g.test(obj.val()))) {
                $.alert({
                    title: 'خطا',
                    content: "تنها مقادیر عددی قابل قبول می باشند",
                    type: 'red',
                })
                req = true;
                // your code here
            }
        } else if (!obj.hasClass('numeric') && obj.val().length !== 0) {
            if (!/^\d(,\d)*$/g.test(obj.val())) {
                $.alert({
                    title: 'خطا',
                    content: "کد مقاطع تحصیلی باید عدد باشد و با , از یکدیگر جدا شوند",
                    type: 'red',
                })
                req = true;
                // your code here
            }
        }
    }

    if (req) {
        return false;
    }
    let params = {
        reqAPI: '',
        step: Step.importStudents,
        faculty: items['faculty'].val(),
        term: items['term'].val(),
        study_levels: items['study_level'].val(),
        lesson: items['lesson'].val(),
        group: items['group'].val(),
        professor: items['professor'].val(),
        student: items['student'].val(),
        items_per_page: 1000,
        pages: pages

    }
    return params;
}

/*function postToIframe(data, url, target) {
    $('body').append('<form action="' + url + '" method="post" target="' + target + '" id="postToIframe"></form>');
    $.each(data, function (n, v) {
        $('#postToIframe').append('<input type="hidden" name="' + n + '" value="' + v + '" />');
    });
    $('#postToIframe').submit().remove();
}*/

var getAPIDataLoop = function (ele, mockup = 0) {
    console.log("getAPIDataLoop");
    page_text = "صفحه";
    insertHTMLErrors = [];
    insertErrors = [];
    hasErrors = false;
    let parentId = $(ele).data("parent");
    let apiObject = $(ele).data("api");
    let params = getInput(parentId);
    // let studyLevel = params.study_levels.split(',');
    params.mockup = mockup;
    // console.log(studyLevel);
    let requstAPI = "";
    let saveAPI = "";
    if (!params) {
        return;
    }
    isCanceled = false;

    offsetAll = Date.now();
    resetAll();
    board.html("");
    pager.text("در حال دریافت اطلاعات");
    toggleLoader();
    params.study_level_index = 0;
    switch (apiObject) {
        case "all":
            console.log("ALL");
            current_step.val(params.step);
            getAPIDataAutomatic(params, mockup);
            break;
        case "studentEnrollments":
            requstAPI = "getStudentEnrollments";
            params.pages = [1];
            params.items_per_page = 10000;
            ajaxCaller(requstAPI, params, true);
            // saveAPI = "saveStudentEnrollments";
            break;
        case "ProfessorEnrollments":
            requstAPI = "getProfessorInfo";
            params.pages = [1];
            params.items_per_page = 10000;
            ajaxCaller(requstAPI, params, true);
            // saveAPI = "saveProfessorEnrollments";
            break;
        case "ImportLessonEnrollments":
            requstAPI = "ImportLessonEnrollments";
            params.pages = [1];
            params.items_per_page = 10000;
            ajaxCaller(requstAPI, params, true);
            // saveAPI = "saveLessonEnrollments";
            break;
        case "ImportStudents":
            requstAPI = "ImportStudents";
            params.items_per_page = 500;
            ajaxCaller(requstAPI, params);
            break;
        case "ImportTeachers":
            requstAPI = "ImportTeachers";
            params.items_per_page = 500;
            ajaxCaller(requstAPI, params, true);
            break;
        case "ImportLessons":
            requstAPI = "ImportLessons";
            params.items_per_page = 50;
            ajaxCaller(requstAPI, params);
            break;
        case "ImportEnrolments":
            requstAPI = "ImportEnrolments";
            params.items_per_page = 10;
            ajaxCaller(requstAPI, params);
            break;
        case "ImportTeacherEnrolments":
            requstAPI = "ImportTeacherEnrolments";
            params.items_per_page = 10;
            ajaxCaller(requstAPI, params);
            break;
    }


    ele_index.val(0);
    ele_level.val(0);


    edited = 0;
    skipped = 0;
    saved = 0;
    failed = 0;

    ele_saved.text(0);
    ele_edited.text(0);
    ele_skipped.text(0);
    ele_failed.text(0);
    board.show();

};

var getAPIDataAutomatic = function (params, mockup = 0) {
    console.log("getAPIDataAutomatic");
    page_text = "صفحه";

    let apiStep = parseInt(current_step.val());
    // let studyLevel = params.study_levels.split(',');
    params.mockup = mockup;
    // console.log(studyLevel);
    let requstAPI = "";
    isCanceled = false;
    offsetAll = Date.now();
    resetAll();
    board.html("");
    params.cr_text = "";


    switch (apiStep) {
        case Step.importStudents:
            requstAPI = "ImportStudents";
            insertErrors.push('خطا های ثبت اطلاعات دانشپذیران');
            insertHTMLErrors.push(addText('خطا های ثبت اطلاعات دانشپذیران'));
            params.cr_text ="دریافت و ثبت اطلاعات دانشپذیران";
            params.items_per_page = 500;
            break;
        case Step.importTeachers:
            requstAPI = "ImportTeachers";
            insertErrors.push('خطا های ثبت اطلاعات اساتید');
            insertHTMLErrors.push(addText('خطا های ثبت اطلاعات اساتید'));
            params.cr_text = "دریافت و ثبت اطلاعات اساتید";
            params.items_per_page = 500;
            break;
        case Step.importLessons:
            requstAPI = "ImportLessons";
            insertErrors.push('خطا های ثبت اطلاعات دروس');
            insertHTMLErrors.push(addText('خطا های ثبت اطلاعات دروس'));
            params.cr_text = "دریافت و ثبت اطلاعات دروس";
            params.items_per_page = 200;
            break;
        case Step.importEnrollments:
            requstAPI = "ImportEnrolments";
            insertErrors.push('خطا های ثبت اطلاعات ثبت نام ها');
            insertHTMLErrors.push(addText('خطا های ثبت اطلاعات ثبت نام ها'));
            params.cr_text = "دریافت اطلاعات و ثبت نام دانشپذیران";
            params.items_per_page = 10;
            break;
        case Step.importTeacherEnrollments:
            requstAPI = "ImportTeacherEnrolments";
            insertErrors.push('خطا های ثبت نام اساتید');
            insertHTMLErrors.push(addText('خطا های ثبت نام اساتید'));
            params.cr_text = "دریافت اطلاعات و ثبت نام اساتید";
            params.items_per_page = 10;
            break;
    }
    pager.text("در حال دریافت اطلاعات" + params.cr_text);

    params.study_level_index = 0;
    console.log(requstAPI);
    console.log(params);
    // console.log(insertErrors);
    callAjaxAutomatic(requstAPI, params, apiStep);
    // console.log(insertErrors);
    ele_index.val(0);
    ele_level.val(0);


    edited = 0;
    skipped = 0;
    saved = 0;
    failed = 0;

    ele_saved.text(0);
    ele_edited.text(0);
    ele_skipped.text(0);
    ele_failed.text(0);
    board.show();

};
function ajaxCaller(requstAPI, params,showDetail =false) {
    looper = $.when();
    if ($.fn.DataTable.isDataTable('#data-table')) {
        $('#data-table').DataTable().clear().destroy();
    }
    $('#data-table').remove();
    //board.hide();
    // pages = Array(52, 53, 54);
    // let staus = 0;
    let con = $.when;

    let study_levels = Array(5, 7);
    if (params.study_levels) {
        study_levels = params.study_levels.split(',');
    }

    //let study_level_index = params.study_level_index;


    console.log("Study level:" + params.study_level);
    console.log(params);
    con.apply($, $.map(params.pages, function (page, i) {
        looper = looper.then(function () {
            console.log("Page:" + page);

            reset();
            start();
            if (isCanceled) {
                return $.Deferred().reject(Status.cancel);
            }
            pager.text("در حال دریافت و ثبت اطلاعات" + " | " + "صفحه " + page);
            if (requstAPI == "ImportStudents" || requstAPI == "ImportLessons") {
                pager.text(pager.text() );
                    // + " | " + "مقطع " + StudyLevelText[study_levels[params.study_level_index]]);
            }
            params.page = page;
            params.study_level = study_levels[params.study_level_index];
            return $.when(getData(params, requstAPI))
                .then((data, textStatus, jqXHR) => {
                    // staus = data.status;
                    switch (data.status) {
                        case Status.success:
                        case Status.edit:
                        case Status.save:

                            if (params.mockup == true) {
                                console.log(data.response);

                                if (data.columns) {
                                    $('.report-container').html('<table id="data-table"/>');
                                    $('#data-table').DataTable({
                                        data: data.response,
                                        columns: data.columns,
                                        scrollY: "250px",
                                        scrollCollapse: true,
                                        responsive: true,
                                        "language": {
                                            "url": "/admin/syncuac/assets/js/persian.json"
                                        }
                                    });
                                    board.hide();
                                    isCanceled = true;
                                    return $.Deferred().reject(Status.end);
                                } else {
                                    board.append(addText(data.msg));
                                    board.scrollTop(board[0].scrollHeight);
                                    return $.Deferred().reject(Status.end);
                                }
                            } else {
                                board.append(addText(data.response));
                                board.scrollTop(board[0].scrollHeight);
                                if (!data.items) {
                                    break;
                                }
                                for (key in data.items) {
                                    switch (data.items[key].status) {
                                        case Status.save:
                                            saved++;
                                            ele_saved.text(saved);
                                            break;
                                        case Status.edit:
                                            edited++;
                                            ele_edited.text(edited);
                                            break;
                                        case Status.skip:
                                            skipped++;
                                            ele_skipped.text(skipped);
                                            break;
                                        case Status.error:
                                            failed++;
                                            ele_failed.text(failed);
                                            hasErrors = true;
                                            if(!showDetail) {
                                                board.append(addText(data.items[key].msg));
                                                board.scrollTop(board[0].scrollHeight);
                                            }
                                            insertErrors.push(stripHTML(data.items[key].msg));
                                            insertHTMLErrors.push(addText(data.items[key].msg));
                                            break;

                                    }

                                    if(showDetail) {
                                        board.append(addText(data.items[key].msg));
                                        board.scrollTop(board[0].scrollHeight);
                                    }
                                }
                            }
                            break;
                        case Status.end:
                            board.append(addText(data.response));
                            console.log("study_level_index: " + params.study_level_index);
                            if ((requstAPI == "ImportStudents" || requstAPI == "ImportLessons")
                                && params.study_level_index < (study_levels.length - 1)) {
                                params.study_level_index++;
                                return $.Deferred().reject(Status.next);
                            } else {
                                return $.Deferred().reject(Status.end);
                            }
                            // console.log("Error");
                            break;
                        case Status.error:
                            if (!isCanceled) {
                                popRetry(data.response, requstAPI, params, page, insertHTMLErrors, ajaxCaller, true);
                            }
                            return $.Deferred().reject(Status.error);
                        case Status.next:
                            return $.Deferred().reject(Status.next);
                            break;
                        case Status.fatal_error:
                            $.alert({
                                title: 'خطا',
                                content: data.response,
                                type: 'red',
                                typeAnimated: true,
                                columnClass: 'col-md-6 col-md-offset-3 col-xs-12',
                                containerFluid: true,
                                buttons: {
                                    ok: {
                                        text: 'تایید',
                                        action: () => {
                                        }
                                    }

                                }
                            });
                            return $.Deferred().reject(Status.fatal_error);
                            break;
                    }
                    stop();
                    // board.append((clock / 1000) + " Second");
                })
                .fail((error) => {
                    console.log(error);
                    status = Status.error;
                    if (!isCanceled && !$.isNumeric(error))
                        popRetry(error, requstAPI, params, page, insertHTMLErrors, ajaxCaller);

                });

        });
        return looper;
    })).then(function () {
        // run this after all ajax calls have completed
        if ((requstAPI == "ImportStudents" || requstAPI == "ImportLessons")
            && params.study_level_index < (study_levels.length - 1)) {
            params.study_level_index++;
            let pages = Array.from(Array(10000).keys());
            params.pages = pages.splice(1);
            ajaxCaller(requstAPI, params);
        }else {
            board.append(addText("پایان ثبت اطلاعات", true));
            board.scrollTop(board[0].scrollHeight);
            toggleLoader(false);
            popErrors(insertHTMLErrors);
        }



    }).fail((status) => {
        reset();
        switch (status) {
            case Status.next:
                let pages = Array.from(Array(10000).keys());
                params.pages = pages.splice(1);
                ajaxCaller(requstAPI, params);
                break;
            case Status.error:
                // stop();
                // toggleLoader(false);
                // board.append(addText("خطا"));
                // popErrors(insertHTMLErrors);
                break;
            case Status.fatal_error:
                stop();
                toggleLoader(false);
                pager.text("-");
                // board.append(addText("خطا"));
                popErrors(insertHTMLErrors);
                break;
            case Status.cancel:
                stop();
                toggleLoader(false);
                board.append(addText("لغو عملیات", true));
                popErrors(insertHTMLErrors);
                break;
            case Status.end:
                stop();
                board.append(addText("پایان ثبت اطلاعات", true));
                board.scrollTop(board[0].scrollHeight);
                toggleLoader(false);
                // console.log('Done!');
                popErrors(insertHTMLErrors);
                break;
            default:
                stop();
                toggleLoader(false);

        }

    });
}
function callAjaxAutomatic(requstAPI, params, step) {
    looper = $.when();
    if ($.fn.DataTable.isDataTable('#data-table')) {
        $('#data-table').DataTable().clear().destroy();
    }
    $('#data-table').remove();

    // let staus = 0;
    let con = $.when;

    let study_levels = Array(5, 7);
    if (params.study_levels) {
        study_levels = params.study_levels.split(',');
    }

    //let study_level_index = params.study_level_index;


    console.log("Study level:" + params.study_level);
    console.log(params);
    //console.log(insertErrors);
    console.log(params);
    con.apply($, $.map(params.pages, function (page, i) {
        looper = looper.then(function () {
            console.log("Page:" + page);
            reset();
            start();
            if (isCanceled) {
                return $.Deferred().reject(Status.cancel);
            }
            pager.text("در حال " + params.cr_text + " | " + "صفحه " + page);
            if (requstAPI == "ImportStudents" || requstAPI == "ImportLessons") {
                pager.text(pager.text());
                // + " | " + "مقطع " + StudyLevelText[study_levels[params.study_level_index]]);
            }
            params.page = page;
            params.study_level = study_levels[params.study_level_index];
            return $.when(getData(params, requstAPI))
                .then((data, textStatus, jqXHR) => {
                    // staus = data.status;
                    switch (data.status) {
                        case Status.success:
                            board.append(addText(data.response));
                            board.scrollTop(board[0].scrollHeight);
                            if (!data.items) {
                                break;
                            }
                            for (key in data.items) {
                                switch (data.items[key].status) {
                                    case Status.save:
                                        saved++;
                                        ele_saved.text(saved);
                                        break;
                                    case Status.edit:
                                        edited++;
                                        ele_edited.text(edited);
                                        break;
                                    case Status.skip:
                                        skipped++;
                                        ele_skipped.text(skipped);
                                        break;
                                    case Status.error:
                                        failed++;
                                        ele_failed.text(failed);
                                        hasErrors = true;
                                        board.append(addText(data.items[key].msg));
                                        board.scrollTop(board[0].scrollHeight);
                                        insertErrors.push(stripHTML(data.items[key].msg));
                                        insertHTMLErrors.push(addText(data.items[key].msg));
                                        break;
                                }

                            }
                            break;
                        case Status.end:
                            board.append(addText(data.response));
                            console.log("study_level_index: " + params.study_level_index);
                            if ((requstAPI == "ImportStudents" || requstAPI == "ImportLessons")
                                && params.study_level_index < (study_levels.length - 1)) {
                                params.study_level_index++;
                                return $.Deferred().reject(Status.next);
                            } else {
                                return $.Deferred().reject(Status.end);
                            }
                            // console.log("Error");
                            break;
                        case Status.error:
                            if (!isCanceled) {
                                popRetry(data.response, requstAPI, params, page, insertHTMLErrors, callAjaxAutomatic, true);
                            }
                            return $.Deferred().reject(Status.error);
                        case Status.next:
                            return $.Deferred().reject(Status.next);
                            break;
                        case Status.fatal_error:
                            // board.append(addText(data.response));
                            $.alert({
                                title: 'خطا',
                                content: data.response,
                                type: 'red',
                                typeAnimated: true,
                                columnClass: 'col-md-6 col-md-offset-3 col-xs-12',
                                containerFluid: true,
                                buttons: {
                                    ok: {
                                        text: 'تایید',
                                        action: () => {
                                        }
                                    }

                                }
                            });
                            return $.Deferred().reject(Status.fatal_error);
                            break;
                    }
                    stop();
                })
                .fail((error) => {
                    // console.log(error);
                    status = Status.error;
                    console.log(params);
                    if (!isCanceled && !$.isNumeric(error))
                        popRetry(error, requstAPI, params, page, insertHTMLErrors, callAjaxAutomatic);

                });

        });
        return looper;
    })).then(function () {
        // run this after all ajax calls have completed

        if ((requstAPI == "ImportStudents" || requstAPI == "ImportLessons")
            && params.study_level_index < (study_levels.length - 1)) {
            params.study_level_index++;
            let pages = Array.from(Array(10000).keys());
            params.pages = pages.splice(1);
            callAjaxAutomatic(requstAPI, params, step);
        }else {
            board.append(addText("پایان ثبت اطلاعات", true));
            board.scrollTop(board[0].scrollHeight);
            if (params.step >= Step.importTeacherEnrollments) {
                toggleLoader(false);
                console.log('Done!');
                popErrors(insertHTMLErrors);
            } else {
                params.step = parseInt(current_step.val()) + 1;
                current_step.val(params.step);
                popNextStep(params);
            }
        }

        // $('.close-btn').show();
        // console.log('Done!');
        // toggleLoader(false);
        // popErrors(insertHTMLErrors);

    }).fail((status) => {
        reset();
        switch (status) {
            case Status.next:
                console.log('next!');
                let pages = Array.from(Array(10000).keys());
                params.pages = pages.splice(1);
                callAjaxAutomatic(requstAPI, params, step);
                break;
            case Status.error:
                console.log('error!');
                // stop();
                // toggleLoader(false);
                // board.append(addText("خطا"));
                // popErrors(insertHTMLErrors);
                break;
            case Status.fatal_error:
                stop();
                toggleLoader(false);
                pager.text("-");
                // board.append(addText("خطا"));
                popErrors(insertHTMLErrors);
                break;
            case Status.cancel:
                console.log('cancel!');
                stop();
                toggleLoader(false);
                board.append(addText("لغو عملیات", true));
                popErrors(insertHTMLErrors);
                break;
            case Status.end:
                console.log('end!');
                stop();
                board.append(addText("پایان ثبت اطلاعات " + StepsText[params.step], true));
                board.scrollTop(board[0].scrollHeight);
                if (params.step >= Step.importTeacherEnrollments) {
                    toggleLoader(false);
                    console.log('Done!');
                    popErrors(insertHTMLErrors);
                } else {
                    params.step = parseInt(current_step.val()) + 1;
                    current_step.val(params.step);
                    popNextStep(params);
                }

                break;
            default:
                console.log('default!');
                stop();
                toggleLoader(false);

        }

    });

}



function saveFile(filename, text) {

    var blob = new Blob([text], {
        type: "text/plain;charset=utf-8"
    });
    saveAs(blob, filename);
}

function errorMsg(error) {
    switch (error.status) {
        case 404:
            error.statusText = "خطا در پردازش اطلاعات"
            break;
        case 504:
            error.statusText = "به علت عدم پاسخگویی سرور سامانه سما، دریافت اطلاعات با مشکل مواجه شده است."
            break;
        default:

    }
    return 'Error : <span class="text-danger">' + error.status + '</span><br>' + error.statusText;
    ;
}

var myVar;
var timeleft = 10;

var downloadTimer;

function popRetry(error, requstAPI, params, page, insertHTMLErrors, callback, isMsg = false) {
    console.log("Error! Retry?");
    let msg;
    if (!isMsg) {
        msg = errorMsg(error);
    } else {
        msg = error;
    }
    console.log(params);
    msg += '<br>' + 'صفحه : <span class="text-danger">' + (page) + '</span>';
    if (params.pages.length > 1) {
        let pages = Array.from(Array(10000).keys());
        params.pages = pages.splice(page);
    }
    if (!isCanceled) {
        $.alert({
            title: 'خطا',
            content: msg,
            type: 'red',
            autoClose: 'retry|3000',
            typeAnimated: true,
            columnClass: 'col-md-6 col-md-offset-3 col-xs-12',
            containerFluid: true,
            buttons: {
                retry: {
                    text: 'تلاش مجدد',
                    action: () => {
                        toggleLoader();
                        callback(requstAPI, params);
                    }
                },
                cancel: {
                    text: 'انصراف',
                    action: () => {
                        stop();
                        toggleLoader(false);
                        popErrors(insertHTMLErrors);
                    }
                }

            }
        });
    }
}

function popNextStep(params) {
    console.log("Next Step");

    let msg = 'ثبت اطلاعات ' + '<span class="text-danger">' + StepsText[params.step - 1] + '</span>' + ' به پایان رسید.';
    msg += '<br>' + 'مرحله بعد: ' + '<span class="text-success">' +  StepsText[params.step] + '</span>';
    msg += '<br>' + 'مرحله بعد آغاز شود؟';
    let pages = Array.from(Array(10000).keys());
    params.pages = pages.splice(1);
    if (!isCanceled) {
        $.alert({
            title: 'توجه!',
            content: msg,
            type: 'green',
            autoClose: 'retry|10000',
            typeAnimated: true,
            columnClass: 'col-md-6 col-md-offset-3 col-xs-12',
            containerFluid: true,
            buttons: {
                retry: {
                    text: 'آغاز مرحله بعد',
                    action: () => {
                        toggleLoader();
                        getAPIDataAutomatic(params);
                    }
                },
                cancel: {
                    text: 'انصراف',
                    action: () => {
                        toggleLoader(false);
                        popErrors(insertHTMLErrors);
                    }
                }

            }
        });
    }
}

function stripHTML(html) {
    var temporalDivElement = document.createElement("div");
    // Set the HTML content with the providen
    temporalDivElement.innerHTML = html;
    // Retrieve the text property of the element (cross-browser support)
    return temporalDivElement.textContent || temporalDivElement.innerText || "";
    // innerHTML will be a xss safe string
}


function popErrors(errors) {
    if (errors.length > 0 &&  hasErrors)
        $.alert({
            title: 'خطا ها',
            content: errors.join(""),
            type: 'red',
            typeAnimated: true,
            columnClass: 'col-md-8 col-md-offset-2 col-xs-12',
            containerFluid: true,
            buttons: {
                download: {
                    text: 'ذخیره',
                    action: () => {
                        saveFile("errors.txt", insertErrors.join("\n"));
                    }
                },
                ok: {
                    text: 'بستن',
                    action: () => {

                    }
                }

            }
        });
}

function toggleLoader(show = true) {
    let loader = $(".loader-container");
    let tabs = $(".tab-content");
    let loaderbtn = $(".btn-cancel");
    $(".btn-op").each(function (index) {
        let self = $(this);
        if (show) {
            self.addClass('disabled');
            loader.show();
            loaderbtn.html(loaderbtn.data("text"));
            tabs.addClass("blurred");

        }
        else {
            self.removeClass('disabled');
            loader.hide();
            tabs.removeClass("blurred")
        }
    })
}

function cancelOperation() {
    isCanceled = true;
    let loaderbtn = $(".btn-cancel");
    loaderbtn.html(loaderHtml);
}

var getData = function (params, action) {

    let clone = JSON.parse(JSON.stringify(params));
    clone.pages=null;
    return $.ajax({
        url: baseUrl + '/request_handler.php',
        dataType: "json",
        type: "POST",
        data: {data: clone, action: action},
        success: function (result, textStatus, jqXHR) {


        },
        error: function (error) {
            // mark the ajax call as failed

        }
    });
};

function start() {
    if (!interval) {
        offset = Date.now();
        interval = setInterval(update, options.delay);
    }
}

function stop() {
    if (interval) {
        clearInterval(interval);
        interval = null;
    }
}

function reset() {
    clock = 0;
}

function resetAll() {
    clockAll = 0;
}

function update() {
    clock += delta();
    clockAll += deltaAll();

    render();
}

function delta() {
    var now = Date.now(),
        d = now - offset;

    offset = now;
    return d;
}

function deltaAll() {
    var now = Date.now(),
        d = now - offsetAll;

    offsetAll = now;
    return d;
}

function render() {
    let seconds = parseInt(clock / 1000);
    let Allseconds = parseInt(clockAll / 1000);
    $("#timer").text(secondsTimeSpanToHMS(seconds));
    $("#timerAll").text(secondsTimeSpanToHMS(Allseconds, true));
}

function secondsTimeSpanToHMS(s, sh = false) {
    var h = Math.floor(s / 3600); //Get whole hours
    s -= h * 3600;
    var m = Math.floor(s / 60); //Get remaining minutes
    s -= m * 60;
    if (sh)
        return h + ":" + (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
    else
        return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
}


$(document).ready(function () {

    board = $("#export");
    pager = $("#pager");
    sec = $("#key");
    ele_index = $("#page-index");
    ele_level = $("#study-level");
    ele_saved = $('.counter.saved .value');
    ele_edited = $('.counter.edited .value');
    ele_skipped = $('.counter.skipped .value');
    ele_failed = $('.counter.failed .value');
    current_step = $('#current-step');
    // $('#data-table').DataTable(
    //     {order: [[1, "asc"]],}
    // );
});

var addText = function (text, isheader = false) {
    let cls = 'info';
    if (isheader)
        cls = 'info-header';
    return "<div class='d-block " + cls + "'>" + text + "</div>";
};


