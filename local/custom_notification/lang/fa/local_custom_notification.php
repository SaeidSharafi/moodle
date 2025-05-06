<?php
$string['custom_notification'] = 'اعلانات';
$string['pluginname'] = 'اعلانات سفارشی';
$string['messageprovider:activity_created'] = 'فعالیت جدید';
$string['activity_created_subject'] = 'فعالیت جدید ایجاد شد: {$a}';
$string['activity_created_message'] = 'یک فعالیت جدید با عنوان "{$a->activityname}" ایجاد شده است. می‌توانید آن را از اینجا مشاهده کنید: {$a->activityurl}';
$string['activity_created_message_html'] = '
<p>یک فعالیت جدید با عنوان "<strong>{$a->activityname}</strong>" ایجاد شده است.</p>
<p>
<span>تاریخ ایجاد: </span>
<span>{$a->created_at}</span>
</p>
<p>
<p>
{$a->fields}
</p>
</p>
<p><a href="{$a->activityurl}">مشاهده فعالیت</a></p>';
