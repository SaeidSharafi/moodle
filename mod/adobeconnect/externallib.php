<?php
/**
 * Mod adobeconnect external API
 *
 * @package    format_tiles
 * @copyright  2021 Saeid Sharafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
global $CFG;
require_once("$CFG->libdir/externallib.php");
require_once($CFG->dirroot . '/mod/adobeconnect/locallib.php');

/**
 * Mod adobeconnect external functions
 *
 * @package    mod_adobeconnect
 * @category   external
 * @copyright  2021 Saeid Sharafi
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @since      Moodle 3.9
 */
class mod_adobeconnect_external extends external_api {
    /**
     * Testing Ajax
     *
     * @return array
     */
    public static function sync_recordings($meetscoids, $cmid, $groupmode, $usrprincipal, $isAuto) {

        $obj_meetscoids = unserialize($meetscoids);
        $res = syncRecordings($obj_meetscoids, $cmid, $groupmode, $usrprincipal, $isAuto);

        return $res;
    }

    public static function sync_attendances($meetscoids, $cmid, $isAuto) {
        $obj_meetscoids = unserialize($meetscoids);
        $res = syncAttendances($obj_meetscoids, $cmid, $isAuto);

        return $res;
    }

    public static function delete_recording($cmid, $recording_scoid, $recording_id) {

        $res = deleteRecording($cmid, $recording_scoid, $recording_id);
        return $res;
    }

    public static function add_to_offline_queue($cmid, $recording_scoid, $recording_id) {

        $res = addToOfflineQueue($cmid, $recording_scoid, $recording_id);
        return $res;
    }

    public static function hide_online($cmid, $recordingId, $hide) {


        $res = recordingHideShowOnline($cmid, $recordingId, $hide);

        return $res;
    }

    public static function hide_offline($cmid, $recordingId, $hide) {

        $res = recordingHideShowOffline($cmid, $recordingId, $hide);

        return $res;
    }

    public static function hide_recording($cmid, $recordingId, $hide) {

        $res = recordingHideShowRow($cmid, $recordingId, $hide);

        return $res;
    }

    public static function sync_recordings_parameters() {
        return new external_function_parameters(array(
                'meetscoids' => new external_value(PARAM_RAW, 'Adobe meeting sco id'),
                'cmid' => new external_value(PARAM_INT, 'adobeconnect activity instance id'),
                'groupmode' => new external_value(PARAM_INT, 'whether or not groupmode is active'),
                'usrprincipal' => new external_value(PARAM_INT, 'Adobe usrprincipal code'),
                'isAuto' => new external_value(PARAM_BOOL, 'whether its automatic sync or not')
        ));
    }

    public static function sync_attendances_parameters() {
        return new external_function_parameters(array(
                'meetscoids' => new external_value(PARAM_RAW, 'Adobe meeting sco id'),
                'cmid' => new external_value(PARAM_INT, 'adobeconnect activity instance id'),
                'isAuto' => new external_value(PARAM_BOOL, 'whether its automatic sync or not')
        ));
    }

    public static function delete_recording_parameters() {
        return new external_function_parameters(array(
                'cmid' => new external_value(PARAM_RAW, 'Adobe meeting sco id'),
                'recording_scoid' => new external_value(PARAM_INT, 'adobeconnect recording scoid id'),
                'recording_id' => new external_value(PARAM_INT, 'adobeconnect recording id in moodle'),
        ));
    }

    public static function add_to_offline_queue_parameters() {
        return new external_function_parameters(array(
            'cmid' => new external_value(PARAM_RAW, 'Adobe meeting sco id'),
            'recording_scoid' => new external_value(PARAM_INT, 'adobeconnect recording scoid id'),
            'recording_id' => new external_value(PARAM_INT, 'adobeconnect recording id in moodle'),
        ));
    }

    public static function hide_online_parameters() {
        return new external_function_parameters(array(
                'cmid' => new external_value(PARAM_RAW, 'adobeconnect activity instance id'),
                'recording_id' => new external_value(PARAM_INT, 'adobeconnect recording id in moodle'),
                'hide' => new external_value(PARAM_INT, 'whether hide the link or not')
        ));
    }

    public static function hide_offline_parameters() {
        return new external_function_parameters(array(
                'cmid' => new external_value(PARAM_RAW, 'adobeconnect activity instance id'),
                'recording_id' => new external_value(PARAM_INT, 'adobeconnect recording id in moodle'),
                'hide' => new external_value(PARAM_INT, 'whether hide the link or not')
        ));
    }

    public static function hide_recording_parameters() {
        return new external_function_parameters(array(
                'cmid' => new external_value(PARAM_RAW, 'adobeconnect activity instance id'),
                'recording_id' => new external_value(PARAM_INT, 'adobeconnect recording id in moodle'),
                'hide' => new external_value(PARAM_INT, 'whether hide the row or not')
        ));
    }

    public static function sync_recordings_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'data to generate tables'),
        ));
    }

    public static function delete_recording_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'data to confirm delete'),
        ));
    }

    public static function add_to_offline_queue_returns() {
        return new external_single_structure(array(
            'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
            'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
            'msg' => new external_value(PARAM_TEXT, 'message'),
            'data' => new external_value(PARAM_RAW, 'data to confirm queue'),
        ));
    }

    public static function sync_attendances_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'data to generate tables'),
        ));
    }

    public static function hide_online_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'not using'),
        ));
    }

    public static function hide_offline_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'not using'),
        ));
    }

    public static function hide_recording_returns() {
        return new external_single_structure(array(
                'status' => new external_value(PARAM_INT, 'Whether or not operation was successful'),
                'is_notification' => new external_value(PARAM_INT, 'Show message as notification'),
                'msg' => new external_value(PARAM_TEXT, 'message'),
                'data' => new external_value(PARAM_RAW, 'not using'),
        ));
    }
}
