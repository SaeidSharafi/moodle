<?php

// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Local language pack from https://lmsclone.smums.ac.ir
 *
 * @package    mlbackend
 * @subpackage php
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['datasetsizelimited'] = 'تنها بخشی از مجموعه داده به دلیل اندازه آن ارزیابی شده است. اگر مطمئن هستید که سیستم شما می‌تواند از عهده مجموعه داده {$a} بربیاید، $CFG->mlbackend_php_no_memory_limit را تنظیم کنید';
$string['errorcantloadmodel'] = 'فایل مدل {$a} وجود ندارد. مدل باید قبل از استفاده از آن برای پیش بینی آموزش داده شود';
$string['errorlowscore'] = 'دقت پیش‌بینی مدل ارزیابی‌شده خیلی بالا نیست، بنابراین برخی از پیش‌بینی‌ها ممکن است دقیق نباشند. امتیاز مدل = {$a->score}، حداقل امتیاز = {$a->minscore}';
$string['errornotenoughdata'] = 'داده های کافی برای ارزیابی این مدل با استفاده از بازه تحلیل ارائه شده وجود ندارد';
$string['errornotenoughdatadev'] = 'نتایج ارزیابی بسیار متفاوت بود. توصیه می‌شود برای اطمینان از معتبر بودن مدل، داده‌های بیشتری جمع آوری شود. نتایج ارزیابی انحراف معیار = {$a->deviation}، حداکثر انحراف استاندارد توصیه شده = {$a->accepteddeviation}';
$string['errorphp7required'] = 'بک‌اند یادگیری ماشین PHP به PHP 7 نیاز دارد';
$string['pluginname'] = 'بک‌اند یادگیری ماشین PHP';
$string['privacy:metadata'] = 'افزونه بک‌اندیادگیری ماشین PHP هیچ داده شخصی را ذخیره نمی‌کند';
