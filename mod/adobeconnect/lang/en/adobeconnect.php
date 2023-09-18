<?php
$string['configtitle'] = 'Adobe Connect';
$string['general'] = 'General';
$string['offline'] = 'Offline';
$string['extra'] = 'Extra';
$string['actinbtngrp'] = '';
$string['addparticipant'] = 'Add';
$string['addpresenter']  = 'Add';
$string['admin_httpauth'] = 'HTTP Authentication Header';
$string['admin_httpauth_desc'] = 'The HTTP_AUTH_HEADER value used in your custom.ini';
$string['admin_login'] = 'Admin Login';
$string['admin_login_desc'] = 'Login for main admin account';
$string['admin_offline_group'] = 'Offline Group Name';
$string['admin_offline_group_desc'] = 'Group name with permission to open recording for flv creation';
$string['admin_password'] = 'Admin Password';
$string['admin_password_desc'] = 'Password for main admin account';
$string['adobeconnect'] = 'Adobe Connect';
$string['adobeconnectfieldset'] = 'Adobe Connect Settings';
$string['adobeconnecthost'] = 'Adobe Connect Host';
$string['adobeconnecthostdescription'] = 'The host can give other users privileges, start and stop a meeting in addition to what a persenter can do';
$string['adobeconnectintro'] = 'Intro';
$string['adobeconnectname'] = 'Meeting title';
$string['adobeconnectparticipant'] = 'Adobe Connect Participant';
$string['adobeconnectparticipantdescription'] = 'Can view, but cannot modify any of the meeting settings';
$string['adobeconnectpresenter'] = 'Adobe Connect Presenter';
$string['adobeconnectpresenterdescription'] = 'The presenter of a meeting and can present content, share a screen, send text messages, moderate questions, create text notes, broadcast audio and video, and push content from web links';
$string['allusers'] = 'all users';
$string['assignadoberole'] = 'Assigning Adobe Roles';
$string['assignadoberoles'] = 'Assigning $a->role role for $a->meetname ($a->groupname)';
$string['assignroles'] = 'Assign roles';
$string['availablelist'] = 'Available';
$string['backtomeeting'] = 'Back to $a meeting';
$string['cancelchanges'] = 'Cancel';
$string['duplicatemeetingname'] = 'A duplicate meeting name was found on the server';
$string['duplicateurl'] = 'A duplicate meeting URL was found on the server';
$string['editingfor'] = 'Editing for: $a';
$string['email_login'] = 'Email address login';
$string['email_login_desc'] = 'Check this option only if your Connect Pro server login is set to use email address. Note that toggling this option on/off during regular usage of this activity module can potentially create duplicaed users on the Connect Pro server';
$string['endtime'] = 'End time';
$string['existingusers'] = '$a existing users';
$string['groupswitch'] = 'Filter by group';
$string['host'] = 'Host';
$string['host_desc'] = 'Where REST calls get sent to';
$string['joinmeeting'] = 'Join Meeting';
$string['meethost_desc'] = 'Domain where the Adobe server is installed';
$string['meetinghost'] = 'Meeting domain';
$string['meetingend'] = 'Meeting end time';
$string['meetinginfo'] = 'Meeting Info';
$string['meetingintro'] = 'Meeting Summary';
$string['meetinggroup'] = 'Meeting group';
$string['meetingname'] = 'Meeting Name';
$string['meetingstart'] = 'Meeting start time';
$string['meetingtype'] = 'Meeting type';
$string['modulename'] = 'Adobe Connect';
$string['modulenameplural'] = 'Adobe Connect';
$string['meettemplates'] = 'Meeting Templates';
$string['meeturl'] = 'Meeting URL';
$string['participantbtngrp'] = 'Participant Actions';
$string['participantsgrp'] = 'Meeting Users';
$string['particpantslabel'] = 'Participants';
$string['potentialusers'] = '$a potential users';
$string['port'] = 'Port';
$string['port_desc'] = 'Port used to connect to Adobe Connect';
$string['presenterbtngrp'] = 'Presenter Actions';
$string['presenterlabel'] = 'Presenter';
$string['recordinghdr'] = 'Meeting Recordings';
$string['record_force'] = 'Force Meeting Recordings';
$string['record_force_desc'] = 'Force all Adobe Connect meetings to be recorded.  This is a site wide effect and the Adobe Connect server must be restarted';
$string['removeparticipant'] = 'Remove';
$string['removepresenter'] = 'Remove';
$string['roletoassign'] = 'Role to assign';
$string['samemeettime'] = 'Invalid Meeting time';
$string['savechanges'] = 'Save';
$string['selectparticipants'] = 'Assign roles';
$string['starttime'] = 'Start time';
$string['usergrouprequired'] = 'This Meeting requires users to be in a group in order to join';
$string['testconnection'] = 'Test Connection';
$string['connectiontesttitle'] = 'Connection test window';
$string['conntestintro'] = '<p>A series of tests have been run in order to determine whether the Adobe Connect Pro server has been properly setup for this integration to work  and to also determine whether the user credentials provided in the activity global settings has the correct permissions to perform the neccessary tasks required by the  activity module.  If any of the tests below have failed, this activity module will not function properly.</p><p> For further assistance and documentation in how to set up your  Adobe Connect Pro server please consult the MoodleDocs help page for this activity module <a href="{$a->url}">Help page</a></p>';
$string['greaterstarttime'] = 'The start time cannot be greater than the end time';
$string['invalidadobemeeturl'] = 'Invalid entry for this field.  Click the help bubble for valid entries';

$string['adobeconnect:meetingpresenter'] = 'Meeting Presenter';
$string['adobeconnect:meetingparticipant'] = 'Meeting Particpant';
$string['adobeconnect:meetinghost'] = 'Meeting Host';
$string['public'] = 'Public';
$string['private'] = 'Private';
$string['protected'] = 'Protected';

// Error codes
$string['emptyxml'] = 'Unable to connect to the Adobe Connect Pro server at this time.  Please inform your Moodle administrator.';
$string['adminemptyxml'] = 'Unable to connect to the Adobe Connect Pro server at this time.  Click continue to proceed to the activity settings page and test the connection';
$string['notsetupproperty'] = 'The activity module is not properly setup.  Please contact your Moodle administrator';
$string['adminnotsetupproperty'] = 'The activity module is not properly setup.  Click continue to proceed to the activity settings page and test the connection';
$string['notparticipant'] = 'You are not a participant for this meeting';
$string['unableretrdetails'] = 'Unable to retrieve meeting details';
$string['usernotenrolled'] = 'Only users enrolled and have a role in this course can join this meeting';
$string['nopresenterrole'] = 'error: error finding adobeconnectpresenter role';
$string['nomeeting'] = 'No meeting exists on the server';
$string['noinstances'] = 'There are no instances of adobeconnect';
$string['error1'] = 'You must be a site administrator to access this page';
$string['error2'] = 'The property \'{$a}\' is empty, please input a value and save the settings';
$string['errormeeting'] = 'Error retrieving recording';
$string['settingblurb'] = '<div class="d-none hidden">Y3JlYXRlZCBieSBzYWVpZCBzaGFyYWZp</div>';
$string['meetingtype_help'] = '<p>A public meeting type is one where anyone who has the URL for the meeting can enter the room.</p>
<p>A private meeting type is one where only registered users and participants can enter. The login page does not allow
guests to log in.  With private meetings the meeting does not actually start until the meeting Presenter or Host joins the meeting.</p>

<p>
If you are creating a private meeting it is always good practice to assign at
least 1 host or presenter who will be present in the meeting; because users with
the participant role will be unable to join the meeting unless a user with the
host or presenter roles has already joined th meeting.
</p>

<p>
If the meeting has support for separate groups at least 1 user in each group, who is
to be present in the meeting, should have either the host or presenter role.
</p>';
$string['meettemplates_help'] = '<p>A meeting room template creates meeting with a custom layout for the meeting room.</p>';
$string['pluginadministration'] = 'Adobe Connect Administration';
$string['pluginname'] = 'Adobe Connect';
$string['modulename'] = 'Adobe Connect';
$string['recordinghdr'] = 'Recordings';
$string['https'] = 'HTTPS Connection';
$string['https_desc'] = 'Connect to the Connect server via HTTPS';
$string['invalidurl'] = 'The URL needs to start with a letter (a-z)';
$string['longurl'] = 'That meeting URL is too long. Try shortening it';
$string['errorrecording'] = 'Unable to find recording session';
$string['meetinfo'] = 'More Meeting Detail';
$string['meetinfotxt'] = 'See server meeting details';
$string['missingexpectedgroups'] = 'There are no groups available.';
$string['event_view'] = 'Viewed activity';
$string['event_view_all'] = 'Viewed all instances of the activity';
$string['event_join_meeting'] = 'User joined a meeting';
$string['event_view_recording'] = 'User viewed a recording';
$string['event_assign_role'] = 'User assigned a Connect Pro role';
$string['event_unassign_role'] = 'User unassigned a Connect Pro role';
$string['event_delete_recording'] = 'Deleted recording';


$string['link'] = 'Link';
$string['record_date'] = 'Date';
$string['record_start_hour'] = 'Start at';
$string['record_end_hour'] = 'end at';
$string['record_startdate'] = 'Start Date';
$string['record_enddate'] = 'End Date';
$string['record_createdate'] = 'Date Created';
$string['record_duration'] = 'Duration';
$string['recordings'] = 'Recordings';
$string['title'] = 'Title';
$string['view'] = 'View';
$string['download'] = 'Download';
$string['offline_link'] = 'View offline';

$string['attendance'] = 'Attendance';
$string['view_own_attendance'] = 'View Own Attendance';
$string['view_own_attendance_desc'] = 'Allows users to view their own attendance records.';

$string['sessain_name'] = 'Name';
$string['participant_name'] = 'Adobe Name';
$string['start_date'] = 'Joined At';
$string['end_date'] = 'Left At';
$string['duration'] = 'Duration';
$string['count_log'] = 'In/Out Count';
$string['showoffline'] = 'Show offline link';
$string['operations'] = 'Operations';
$string['showoffline_desc'] = 'if unchecked, class show offline link configuration will be ignored';
$string['customfields'] = 'User custom fields';
$string['customfields_desc'] = 'these fields will be shown in Attendance table (if exist). write each field short name in a line';

$string['adobeconnect_generalsettings'] = 'General';
$string['sync_attendance'] = 'Synchronize Attendances';
$string['delete_recording'] = 'Delete recordings';
$string['sync_recordings'] = 'Synchronize Recordings';
$string['confirmdelete'] = 'Ar you sure you want to remove this recording? <br><span class="text-danger">removed recordings cannot be recovered</span>';
$string['confirm_offline'] = 'Ar you sure you want to add this recording to offliner queueu?';
$string['offliner']= 'Make Offline';
$string['insession'] = 'ongoing session';
$string['last_sync'] = 'Last sync';

$string['permissiondo'] = 'You don\'t have permission for this action: {$a}';
$string['permissionsync'] = 'You don\'t have permission for this action: {$a}';
$string['deleted'] = 'deleted from adobe';

$string['view_own_attendance'] = 'View own attendance';
$string['cachedef_recordings'] = 'Cached urls of offline recordings';
$string['offline_host'] = 'Offline server url';
$string['offline_host_desc'] = 'address of server that generate offline videos, without trailing slash';
$string['offline_host_secret'] = 'Secret key';
$string['offline_host_secret_desc'] = 'secret key that used for authentication';

$string['sync_recording_success']= "recording synchronization was successful";

$string['removed_recording']= "recording has been removed";
$string['offline_server']= "Offline server:";
$string['use_offline']= "Use Offline server";
$string['use_offline_desc']= "Use Offline server";
$string['offline_data_received']= "successfully updated data from the server";
$string['removed_error_adobe']= "recording has been removed from database, but could not be found on Adobe Connect server";
$string['removed_error_db']= "recording removed from Adobe connect but removing form database failed";
$string['offline_server_err_reach']= "Offline server cannot be reached";
$string['offline_server_err_auth']= "Offline server secret code is not correct";
$string['offline_server_err_sco']= "no SCO id provided";
$string['add_to_offline_queue'] = 'Offline';
$string['in_offline_queue'] = 'In Offline Queue';


$string['sync_attendance_not_found']= "There is no attendance";
$string['sync_attendance_success']= "attendance synchronization was successful";
$string['sync_attendance_fail']= "attendance synchronization failed, no SCO id provided";
