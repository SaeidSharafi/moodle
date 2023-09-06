<?php
/**
 * Adobeconnect web services defintions
 *
 * @package   mod_adobeconnect
 * @copyright 2021 Saeid Sharafi
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = array(
        'adobeconnect_sync_recordings' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'sync_recordings',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'Synchronize recordings from AdobeConnect to Moodle (intended to be used from AJAX)',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => ''
        ),
        'adobeconnect_sync_attendances' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'sync_attendances',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'Synchronize attendances from AdobeConnect to Moodle (intended to be used from AJAX)',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => 'mod/adobeconnect:viewattendees'
        ),
        'adobeconnect_delete_recording' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'delete_recording',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'will delete recordings from AdobeConnect recordings (intended to be used from AJAX)',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => 'mod/adobeconnect:deleterecordings'
        ),
        'adobeconnect_add_to_offline_queue' => array(
            'classname' => 'mod_adobeconnect_external',
            'methodname' => 'add_to_offline_queue',
            'classpath' => 'mod/adobeconnect/externallib.php',
            'description' => 'will queue recording to convert for offline view (intended to be used from AJAX)',
            'type' => 'write',
            'ajax' => true,
            'loginrequired' => true,
            'capabilities' => 'mod/adobeconnect:managerecordings'
        ),
        'adobeconnect_hide_online' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'hide_online',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'Hide online link from recording row (intended to be used from AJAX)',
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => 'mod/adobeconnect:managerecordings'
        ),
        'adobeconnect_hide_offline' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'hide_offline',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'Hide offline link from recording row (intended to be used from AJAX)' ,
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => 'mod/adobeconnect:managerecordings'
        ),
        'adobeconnect_hide_recording' => array(
                'classname' => 'mod_adobeconnect_external',
                'methodname' => 'hide_recording',
                'classpath' => 'mod/adobeconnect/externallib.php',
                'description' => 'Hide offline link from recording row (intended to be used from AJAX)' ,
                'type' => 'write',
                'ajax' => true,
                'loginrequired' => true,
                'capabilities' => 'mod/adobeconnect:managerecordings'
        )
);
