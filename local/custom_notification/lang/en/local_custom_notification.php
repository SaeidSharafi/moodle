<?php
$string['pluginname'] = 'Custom Notifications';
$string['custom_notification'] = 'Custom Notifications';
$string['messageprovider:activity_created'] = 'New Activity created';
$string['activity_created_subject'] = 'New activity created: {$a}';
$string['activity_created_message'] = 'A new activity titled "{$a->activityname}" has been created. You can view it here: {$a->activityurl}';
$string['activity_created_message_html'] = '
<p>A new activity titled "<strong>{$a->activityname}</strong>" has been created.</p>
<p>
 Created at: {$a->created_at}
</p>
<p>
{$a->fields}
</p>
<p><a href="{$a->activityurl}">View Activity</a></p>';
