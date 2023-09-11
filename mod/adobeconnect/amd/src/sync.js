// Put this file in path/to/plugin/amd/src
// You can call it anything you like
import Ajax from 'core/ajax';
// import {get_string as getString} from 'core/str';
import {get_strings as getStrings} from 'core/str';
import notification from 'core/notification';
import templates from 'core/templates';


const Selectors = {
    syncRecordings: '[data-action="syncrecordings"]',
    syncAttendance: '[data-action="syncattendance"]',
    deleteRecording: '[data-action="deleterecording"]',
    addToOfflineQueue: '[data-action="addToOfflineQueue"]',
    toggleDisplayOnline: '[data-action="toggledisplayonline"]',
    toggleDisplayOffline: '[data-action="toggledisplayoffline"]',
    toggleDisplayRow: '[data-action="toggledisplayrow"]',

};
const SYNC_RECORDS = "RECORDS";
const SYNC_ATTENDANCE = "ATTENDANCE";
let syncBoth = false;
let syncRecordsDone = false;
let syncAttendanceDone = false;

const registerEventListeners = (localized_strings, locale, contextId, scoid, groupmode, usrprincipal, showoffline) => {

    // window.console.log(localized_strings);
    document.addEventListener('click', e => {

        const syncRecordings = e.target.closest(Selectors.syncRecordings);
        if (syncRecordings) {
            e.preventDefault();
            syncBoth = false;
            synchronizeRecords(localized_strings, locale, contextId, scoid, groupmode, usrprincipal, showoffline, syncRecordings);

        }

        const syncAttendance = e.target.closest(Selectors.syncAttendance);
        if (syncAttendance) {
            e.preventDefault();
            syncBoth = false;
            synchronizeAttendance(localized_strings, locale, contextId, scoid, groupmode, syncAttendance);
        }
        const deleteRecording = e.target.closest(Selectors.deleteRecording);
        if (deleteRecording) {
            e.preventDefault();
            syncBoth = false;
            deleteRecording.classList.add('loading');
            notification.confirm(localized_strings['delete'], localized_strings['confirmdelete'],
                localized_strings['yes'], localized_strings['no'], function () {
                    let query = '#recording-' + deleteRecording.dataset.recordingid;
                    let row = document.querySelector(query);
                    row.classList.add('disabled');
                    var res = Ajax.call([{
                        methodname: "adobeconnect_delete_recording",
                        args: {
                            cmid: contextId,
                            recording_scoid: deleteRecording.dataset.recordingscoid,
                            recording_id: deleteRecording.dataset.recordingid
                        }
                    }]);
                    res[0].done(function (response) {
                        // window.console.log("Response: ");

                        if (response.status == 1) {
                            showNotification(response, "success");
                            row.parentNode.removeChild(row);
                            deleteRecording.classList.remove('loading');
                        } else {
                            if (response.status == -1) {
                                notification.alert("خطا", response.msg);
                            } else {
                                showNotification(response, "success");
                            }

                            row.classList.remove('disabled');
                            deleteRecording.classList.remove('loading');
                            return;
                        }

                    }).fail(function (ex) {
                        // do something with the exception
                        row.classList.remove('disabled');
                        deleteRecording.classList.remove('loading');
                        // window.console.log("Failed");
                        // window.console.log(ex);
                        notification.exception(ex);
                    });
                }, function () {
                    deleteRecording.classList.remove('loading');
                });
        }

        const toggleDisplayOnline = e.target.closest(Selectors.toggleDisplayOnline);
        const toggleDisplayOffline = e.target.closest(Selectors.toggleDisplayOffline);

        if (toggleDisplayOnline) {
            e.preventDefault();
            if (toggleDisplayOnline.classList.contains('disabled')) {
                // window.console.log('disabled');
                return;
            }
            toggleDisplayOnline.classList.add('disabled');
            let ico = toggleDisplayOnline.querySelector("i");
            ico.classList.remove('fa-eye-slash');
            ico.classList.remove('fa-eye');
            ico.classList.add('spinner-grow');
            // window.console.log('toggleDisplayOnline');
            // window.console.log(toggleDisplayOnline);
            // window.console.log(toggleDisplayOnline.dataset.recordingid);
            // window.console.log(toggleDisplayOnline.getAttribute('data-recordingid'));
            var res = Ajax.call([{
                methodname: "adobeconnect_hide_online",
                args: {
                    cmid: contextId,
                    recording_id: toggleDisplayOnline.dataset.recordingid,
                    hide: toggleDisplayOnline.dataset.toggle
                }
            }]);
            res[0].done(function (response) {
                // window.console.log('adobeconnect_hide_online');
                // window.console.log(response);
                if (response.status === 1) {

                    if (toggleDisplayOnline.dataset.toggle == 0) {
                        ico.classList.remove('fa-eye-slash');
                        ico.classList.add('fa-eye');
                        ico.title = localized_strings['hide'];
                        ico.ariaLabel = localized_strings['hide'];
                        toggleDisplayOnline.dataset.toggle = 1;
                    } else {
                        ico.classList.remove('fa-eye');
                        ico.classList.add('fa-eye-slash');
                        ico.title = localized_strings['show'];
                        ico.ariaLabel = localized_strings['show'];
                        toggleDisplayOnline.dataset.toggle = 0;
                    }
                } else {
                    window.console.log(response.msg);
                }
                toggleDisplayOnline.classList.remove('disabled');
                ico.classList.remove('spinner-grow');
            }).fail(function (ex) {
                // do something with the exception
                // window.console.log("Failed");
                // window.console.log(ex);
                notification.exception(ex);
            });
            // window.console.log(toggleDisplayOnline.dataset.recordingid);
        }
        if (toggleDisplayOffline) {
            e.preventDefault();

            if (toggleDisplayOffline.classList.contains('disabled')) {
                // window.console.log('disabled');
                return;
            }
            toggleDisplayOffline.classList.add('disabled');
            let ico = toggleDisplayOffline.querySelector("i");
            ico.classList.remove('fa-eye-slash');
            ico.classList.remove('fa-eye');
            ico.classList.add('spinner-grow');

            var res = Ajax.call([{
                methodname: "adobeconnect_hide_offline",
                args: {
                    cmid: contextId,
                    recording_id: toggleDisplayOffline.dataset.recordingid,
                    hide: toggleDisplayOffline.dataset.toggle
                }
            }]);
            res[0].done(function (response) {
                // window.console.log('adobeconnect_hide_offline');
                // window.console.log(response);
                if (response.status === 1) {
                    if (toggleDisplayOffline.dataset.toggle == 0) {
                        ico.classList.remove('fa-eye-slash');
                        ico.classList.add('fa-eye');
                        ico.title = localized_strings['hide'];
                        ico.ariaLabel = localized_strings['hide'];
                        toggleDisplayOffline.dataset.toggle = 1;
                    } else {
                        ico.classList.remove('fa-eye');
                        ico.classList.add('fa-eye-slash');
                        ico.title = localized_strings['show'];
                        ico.ariaLabel = localized_strings['show'];
                        toggleDisplayOffline.dataset.toggle = 0;
                    }

                    toggleDisplayOffline.classList.remove('disabled');
                    ico.classList.remove('spinner-grow');

                } else {
                    window.console.log(response.msg);
                }

            }).fail(function (ex) {
                // do something with the exception
                // window.console.log("Failed");
                // window.console.log(ex);
                notification.exception(ex);
            });
            // window.console.log(toggleDisplayOffline.dataset.recordingid);
        }
        const addToOfflineRecorder = e.target.closest(Selectors.addToOfflineQueue);
        if (addToOfflineRecorder) {
            e.preventDefault();
            syncBoth = false;
            addToOfflineRecorder.classList.add('loading');
            notification.confirm(localized_strings['offliner'], localized_strings['confirm_offline'],
                localized_strings['yes'], localized_strings['no'], function () {
                    let query = '#recording-' + addToOfflineRecorder.dataset.recordingid;
                    let row = document.querySelector(query);
                    row.classList.add('disabled');
                    var res = Ajax.call([{
                        methodname: "adobeconnect_add_to_offline_queue",
                        args: {
                            cmid: contextId,
                            recording_scoid: addToOfflineRecorder.dataset.recordingscoid,
                            recording_id: addToOfflineRecorder.dataset.recordingid
                        }
                    }]);
                    res[0].done(function (response) {
                        // window.console.log("Response: ");
                        if (response.status == 1) {
                            row.classList.remove('disabled');
                            showNotification(response, "success");
                            synchronizeRecords(localized_strings, locale, contextId, scoid, groupmode,
                                usrprincipal, showoffline, syncRecordings);
                        } else {
                            if (response.status == -1) {
                                notification.alert("خطا", response.msg);
                            } else {
                                showNotification(response, "success");
                            }

                            row.classList.remove('disabled');
                            addToOfflineRecorder.classList.remove('loading');
                            return;
                        }

                    }).fail(function (ex) {
                        // do something with the exception
                        row.classList.remove('disabled');
                        addToOfflineRecorder.classList.remove('loading');
                        // window.console.log("Failed");
                        // window.console.log(ex);
                        notification.exception(ex);
                    });
                }, function () {
                    addToOfflineRecorder.classList.remove('loading');
                });
        }

        const toggleDisplayRow = e.target.closest(Selectors.toggleDisplayRow);
        if (toggleDisplayRow) {
            e.preventDefault();

            if (toggleDisplayRow.classList.contains('disabled')) {
                // window.console.log('disabled');
                return;
            }
            let row = e.target.closest("tr");
            let toggleDisplayOnline = row.querySelector(Selectors.toggleDisplayOnline);
            let toggleDisplayOffline = row.querySelector(Selectors.toggleDisplayOffline);
            toggleDisplayRow.classList.add('disabled');
            toggleDisplayOnline.classList.add('disabled');
            toggleDisplayOffline.classList.add('disabled');

            let ico = toggleDisplayRow.querySelector("i");

            ico.classList.remove('fa-eye-slash');
            ico.classList.remove('fa-eye');
            ico.classList.add('spinner-grow');


            var res = Ajax.call([{
                methodname: "adobeconnect_hide_recording",
                args: {
                    cmid: contextId,
                    recording_id: toggleDisplayRow.dataset.recordingid,
                    hide: toggleDisplayRow.dataset.toggle
                }
            }]);
            res[0].done(function (response) {
                // window.console.log('adobeconnect_hide_recording');
                // window.console.log(response);
                if (response.status === 1) {
                    let icoOnline = toggleDisplayOnline.querySelector("i");
                    let icoOffline = toggleDisplayOffline.querySelector("i");

                    if (toggleDisplayRow.dataset.toggle == 0) {
                        ico.classList.remove('fa-eye-slash');
                        ico.classList.add('fa-eye');
                        ico.title = localized_strings['hide'];
                        ico.ariaLabel = localized_strings['hide'];

                        icoOnline.classList.remove('fa-eye-slash');
                        icoOnline.classList.add('fa-eye');
                        icoOnline.title = localized_strings['hide'];
                        icoOnline.ariaLabel = localized_strings['hide'];

                        icoOffline.classList.remove('fa-eye-slash');
                        icoOffline.classList.add('fa-eye');
                        icoOffline.title = localized_strings['hide'];
                        icoOffline.ariaLabel = localized_strings['hide'];

                        toggleDisplayRow.dataset.toggle = 1;
                        toggleDisplayOnline.dataset.toggle = 1;
                        toggleDisplayOffline.dataset.toggle = 1;
                    } else {
                        ico.classList.remove('fa-eye');
                        ico.classList.add('fa-eye-slash');
                        ico.title = localized_strings['show'];
                        ico.ariaLabel = localized_strings['show'];

                        icoOnline.classList.remove('fa-eye');
                        icoOnline.classList.add('fa-eye-slash');
                        icoOnline.title = localized_strings['show'];
                        icoOnline.ariaLabel = localized_strings['show'];

                        icoOffline.classList.remove('fa-eye');
                        icoOffline.classList.add('fa-eye-slash');
                        icoOffline.title = localized_strings['show'];
                        icoOffline.ariaLabel = localized_strings['show'];


                        toggleDisplayRow.dataset.toggle = 0;
                        toggleDisplayOnline.dataset.toggle = 0;
                        toggleDisplayOffline.dataset.toggle = 0;
                    }

                    toggleDisplayRow.classList.remove('disabled');
                    toggleDisplayOnline.classList.remove('disabled');
                    toggleDisplayOffline.classList.remove('disabled');
                    ico.classList.remove('spinner-grow');

                } else {
                    window.console.log(response.msg);
                }

            }).fail(function (ex) {
                // do something with the exception
                // window.console.log("Failed");
                // window.console.log(ex);
                notification.exception(ex);
            });
            // window.console.log(toggleDisplayRow.dataset.recordingid);
        }

    });
};

const synchronizeRecords = (localized_strings, locale, contextId, scoid, groupmode,
                            usrprincipal, showoffline, syncRecordings = null, isAuto = false) => {
    toggleLoader();
    // window.console.log("adobeconnect_sync_recordings: ");
    if (syncRecordings) {
        if (syncRecordings.classList.contains('loading')) {
            // window.console.log('disabled');
            return;
        }
    }
    if (!syncRecordings) {
        syncRecordings = document.querySelector(Selectors.syncRecordings);
    }
    if (syncRecordings) {
        syncRecordings.classList.add('loading');
    }
    var res = Ajax.call([{
        methodname: "adobeconnect_sync_recordings",
        args: {
            cmid: contextId,
            meetscoids: scoid,
            groupmode: groupmode,
            usrprincipal: usrprincipal,
            isAuto: isAuto,
        }
    }]);
    res[0].done(function (response) {
        if (response.status == -1) {
            notification.alert("خطا", response.msg);
        }
        if (response.status == 0 || response.status == -1) {
            toggleLoader(SYNC_RECORDS, false);
            if (syncRecordings) {
                syncRecordings.classList.remove('loading');
            }
            showNotification(response);

            return;
        }

        let json = null;
        try {
            json = JSON.parse(response.data);
            // window.console.log(json);

        } catch (e) {
            notification.exception(e);
            toggleLoader(SYNC_RECORDS, false);
            if (syncRecordings) {
                syncRecordings.classList.remove('loading');
            }

            return;
        }

        if (response.status == -2) {

            toggleLoader(SYNC_RECORDS, false);
            if (syncRecordings) {
                syncRecordings.classList.remove('loading');
            }
            let ele = document.getElementById("last_sync_record");
            window.console.log(response.msg);
            if (ele) {
                ele.innerHTML = json.last_sync;
            }

            // window.console.log(response.msg);
            return;
        }

        json.records = generateRecordingsFields(json.records, groupmode, contextId);
        // let promise = generateRecordingsFields(json.records, groupmode, contextId);
        json.showoffline = showoffline;
        json.showoffline = showoffline;

        templates.render('mod_adobeconnect/recordings', json)
            .then((html, js) => {
                window.console.log("TEMPLATES");
                let table = document.querySelector("#records");
                templates.replaceNode(table, html, js);
                let ele = document.getElementById("last_sync_record");
                window.console.log(response);
                if (ele) {
                    ele.innerHTML = json.last_sync;
                }

                toggleLoader(SYNC_RECORDS, false);
                if (syncRecordings) {
                    syncRecordings.classList.remove('loading');
                }
                showNotification(response);

            }).fail((e) => {
            if (syncRecordings) {
                syncRecordings.classList.remove('loading');
            }

            toggleLoader(SYNC_RECORDS, false);
            notification.exception(e);
        });


        // secondsToTime(3);

    }).fail(function (ex) {
        // do something with the exception
        if (syncRecordings) {
            syncRecordings.classList.remove('loading');
        }

        toggleLoader(SYNC_RECORDS, false);
        notification.exception(ex);
        // window.console.log("Failed");
        // window.console.log(ex);
    });
};
const synchronizeAttendance = (localized_strings, locale, contextId, scoid, groupmode,
                               syncAttendance = null, isAuto = false) => {
    toggleLoader();
    // window.console.log("adobeconnect_sync_attendances: ");
    if (syncAttendance) {
        if (syncAttendance.classList.contains('loading')) {
            // window.console.log('disabled');
            return;
        }
    }
    if (!syncAttendance) {
        syncAttendance = document.querySelector(Selectors.syncAttendance);
    }

    if (syncAttendance) {
        syncAttendance.classList.add('loading');
    }


    var res = Ajax.call([{
        methodname: "adobeconnect_sync_attendances",
        args: {
            cmid: contextId,
            meetscoids: scoid,
            isAuto: isAuto,
        }
    }]);
    res[0].done(function (response) {
        // window.console.log("Response:sadsa ");
        if (response.status == -1) {
            notification.alert("خطا", response.msg);
        }
        if (response.status == 0 || response.status == -1) {
            toggleLoader(SYNC_ATTENDANCE, false);
            if (syncAttendance) {
                syncAttendance.classList.remove('loading');
            }
            showNotification(response);

            return;
        }
        // window.console.log(response);
        let json = null;
        try {
            window.console.log(response);
            json = JSON.parse(response.data);

        } catch (e) {
            notification.exception(e);
            toggleLoader(SYNC_ATTENDANCE, false);
            if (syncAttendance) {
                syncAttendance.classList.remove('loading');
            }

            notification.addNotification({
                message: "خطا در پاسخ دریافتی" + "<br>",
                type: "error"
            });
            return;
        }
        if (response.status == -2) {
            toggleLoader(SYNC_ATTENDANCE, false);
            if (syncAttendance) {
                syncAttendance.classList.remove('loading');
            }

            showNotification(response);
            let ele = document.getElementById("last_sync_attendance");
            if (ele) {
                ele.innerHTML = json.last_sync;
            }
            // window.console.log(response.msg);
            return;
        }

        templates.render('mod_adobeconnect/attendance', json)
            .then((html, js) => {
                let table = document.querySelector("#attendances");
                templates.replaceNode(table, html, js);
                let ele = document.getElementById("last_sync_attendance");
                if (ele) {
                    ele.innerHTML = json.last_sync;
                }
                toggleLoader(SYNC_ATTENDANCE, false);
                if (syncAttendance) {
                    syncAttendance.classList.remove('loading');
                }
                showNotification(response, "success");


            }).fail((e) => {
            toggleLoader(SYNC_ATTENDANCE, false);
            if (syncAttendance) {
                syncAttendance.classList.remove('loading');
            }

            notification.exception(e);
        });
    }).fail(function (ex) {
        // do something with the exception
        toggleLoader(SYNC_ATTENDANCE, false);
        if (syncAttendance) {
            syncAttendance.classList.remove('loading');
        }

        // window.console.log("Failed");
        // window.console.log(ex);
        notification.exception(ex);
    });
};

const generateRecordingsFields = (items, groupid, contextId) => {


    let arr_items = Array();
    items.forEach((item) => {
        let row = {};
        row.id = item.id;

        let url_online = 'joinrecording.php?mode=online&id=' + contextId + '&recording=' +
            item.recordingscoid + '&groupid=' + groupid + '&sesskey=' + item.sesskey;


        let url_offline = `joinrecording.php?mode=offline&id=${contextId}&recording=${item.recordingscoid}`
            + `&groupid=${groupid}&sesskey=${item.sesskey}`;

        row.name = item.name;
        row.url = url_online;
        window.console.log(item.adobe_offline);
        row.url_offline = item.adobe_offline ? url_offline : item.url_offline;
        row.adobe_offline = item.adobe_offline;
        row.recording_scoid = item.recordingscoid;
        row.recording_id = item.id;
        row.hideoffline = (item.hideoffline == 1) ? 1 : null;
        row.hideonline = (item.hideonline == 1) ? 1 : null;
        row.hiderow = (item.hiderow == 1) ? 1 : null;
        row.sesskey = item.sesskey;
        row.deleted = (item.deleted == 1) ? 1 : null;
        row.in_offline_server = (item.in_offline_server == 1) ? 1 : null;
        row.in_offline_queue = (item.in_offline_queue == 1) ? 1 : null;
        row.formated_create_date = item.formated_create_date;
        row.formated_duration = item.formated_duration;

        arr_items.push(row);

    });
    return arr_items;


};

const showNotification = (response, type = "info") => {
    if (response.is_notification === 1) {
        notification.addNotification({
            message: response.msg.replace(/\n/g, "<br>"),
            type: type
        });
    } else {
        window.console.log("RESPONSE");
        window.console.log(response);
    }
};
// const generateAttendanceFields = (items) => {
//     let arr_items = Array();
//     items.forEach((item) => {
//         let row = {};
//         window.console.log(item.duration);
//         row.email = item.email;
//         row.session_name = item.session_name;
//         row.participant_name = item.participant_name;
//         row.duration = item.formated_duration;
//         row.count_log = item.count_log;
//         row.user_fields = item.user_fields;
//
//         row.start_dates = item.start_dates;
//         row.end_dates = item.end_dates;
//         row.join_dates = item.join_dates;
//         row.join_times = item.join_times;
//         row.exit_times = item.exit_times;
//
//         row.use_hour = item.use_hour;
//
//
//         arr_items.push(row);
//
//     });
//     return arr_items;
//
// };
// const secondsToTime = (raw) => {
//     let seconds = Math.floor(raw % (60));
//     let min = Math.floor((raw % (60 * 60)) / 60);
//     let hours = Math.floor(raw / (60 * 60));
//     if (seconds < 10) {
//         seconds = "0" + seconds;
//     }
//     if (min < 10) {
//         min = "0" + min;
//     }
//     if (hours < 10) {
//         hours = "0" + hours;
//     }
//     return hours + ":" + min + ":" + seconds;
// };
const toggleLoader = (caller = "", show = true) => {
    let loader = document.querySelector("#details-adobe .loader-full");

    if (show) {
        syncRecordsDone = false;
        syncAttendanceDone = false;
        loader.style.display = "flex";
    } else {
        if (caller === SYNC_RECORDS) {

            syncRecordsDone = true;
        }
        if (caller === SYNC_ATTENDANCE) {
            syncAttendanceDone = true;
        }
        if (syncAttendanceDone && syncRecordsDone || !syncBoth) {
            loader.style.display = "none";
        }
    }

};

export const init = ({locale, contextid, scoid, groupmode, usrprincipal, showoffline, sync}) => {
    var localized_strings = Array();
    localized_strings['delete'] = 'حذف';
    localized_strings['view'] = 'مشاهده';
    localized_strings['confirmdelete'] = 'آیا از حذف این کرورد اطمینان دارید؟';
    localized_strings['yes'] = 'بله';
    localized_strings['no'] = 'خیر';
    localized_strings['hide'] = 'پنهان کردن';
    localized_strings['show'] = 'نمایش';
    localized_strings['offliner'] = 'اضافه کردن به صف تبدیل';
    localized_strings['confirm_offline'] = 'آیا از اضافه کردن این جلسه برای تبدیل به فایل قابل دانلود اطمینان دارید؟';
    getStrings([
        {key: 'delete', component: 'core'},
        {key: 'view', component: 'core'},
        {key: 'confirmdelete', component: 'mod_adobeconnect'},
        {key: 'yes'},
        {key: 'no'},
        {key: 'hide'},
        {key: 'show'},
        {key: 'offliner', component: 'mod_adobeconnect'},
        {key: 'confirm_offline', component: 'mod_adobeconnect'},
    ]).done(function (s) {
        localized_strings['delete'] = s[0];
        localized_strings['view'] = s[1];
        localized_strings['confirmdelete'] = s[2];
        localized_strings['yes'] = s[3];
        localized_strings['no'] = s[4];
        localized_strings['hide'] = s[5];
        localized_strings['show'] = s[6];
        localized_strings['offliner'] = s[7];
        localized_strings['confirm_offline'] = s[8];
        if (sync) {
            syncBoth = true;
            synchronizeRecords(localized_strings, locale, contextid, scoid, groupmode, usrprincipal, showoffline, null, true);
            synchronizeAttendance(localized_strings, locale, contextid, scoid, groupmode, null, true);
        }
        registerEventListeners(localized_strings, locale, contextid, scoid, groupmode, usrprincipal, showoffline);

    }).fail(function (e) {
        notification.exception(e);
        if (sync) {
            syncBoth = true;
            synchronizeRecords(localized_strings, locale, contextid, scoid, groupmode, usrprincipal, showoffline, null, true);
            synchronizeAttendance(localized_strings, locale, contextid, scoid, groupmode, null, true);
        }
        registerEventListeners(localized_strings, locale, contextid, scoid, groupmode, usrprincipal, showoffline);
    });


};
