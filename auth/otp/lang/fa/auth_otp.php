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
 * Strings for component 'auth_otp', language 'en'.
 *
 * @package    auth_otp
 * @copyright  2021 Brain Station 23 ltd
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['pluginname'] = 'OTP';
$string['eventotpgenerated'] = 'رمز عبور تولید شد';
$string['enablesmsservice'] = 'فعال کردن سرویس پیامک';
$string['enablesmsservice_help'] = 'این گزینه برای فعال کردن سرویس پیامک است';
$string['enableaws'] = 'فعال کردن سرویس AWS SNS';
$string['enableaws_help'] = 'فعال کردن سرویس AWS SNS';
$string['awssettings'] = 'تنظیمات AWS SNS';
$string['twilosettions'] = 'تنظیمات Twilio SMS';
$string['sslsmssettings'] = 'تنظیمات SSL SMS';
$string['awsregion'] = 'منطقه AWS SNS';
$string['awsregion_help'] = 'منطقه AWS SNS خود را وارد کنید';
$string['awskey_help'] = 'کلید دسترسی AWS که در سرویس پیامک AWS دریافت کرده‌اید';
$string['awskey'] = 'کلید دسترسی AWS';
$string['awssecrect'] = 'کلید مخفی AWS که در سرویس پیامک AWS دریافت کرده‌اید';
$string['awssecrect_help'] = 'کلید مخفی AWS';
$string['eventotprevoked'] = 'رمز عبور لغو شد';
$string['otpgeneratedsubj'] = 'رمز یک‌بار مصرف';
$string['otpmissmatch'] = 'رمز یک‌بار مصرف شما صحیح نیست لطفاً دوباره بررسی کنید';
$string['otpgeneratedtext'] = 'رمز یک‌بار مصرف برای جلسه جاری: {$a->password}';
$string['otpsentsuccess'] = 'رمز یک‌بار مصرف با موفقیت ارسال شد لطفاً گوشی خود را بررسی کنید';
$string['otpsenterror'] = 'خطایی در هنگام ارسال رمز یک‌بار مصرف رخ داد.';
$string['otpsenterror_number'] = 'خطایی در هنگام ارسال رمز یک‌بار مصرف رخ داد. کد خطا: ';
$string['otpsentinfo'] = 'رمز یک‌بار مصرف برای جلسه جاری قبلاً تولید شده لطفاً بررسی کنید.';
$string['otprevoked'] = 'رمز تولید شده قبلی به دلیل عبور از حد مجاز ورود ناموفق لغو شده است.';
$string['otpperiodwarning'] = 'حداقل زمانی که بعد از آن می‌توان رمز دیگری تولید کرد رعایت نشده. لطفاً بعداً تلاش کنید.';
$string['revokethreshold'] = 'آستانه لغو';
$string['revokethreshold_help'] = 'حد مجاز ورودهای ناموفق که باعث لغو رمز تولید شده می‌شود (0 - نامحدود).';
$string['minrequestperiod'] = 'حداقل دوره';
$string['minrequestperiod_help'] = 'زمانی به ثانیه که بعد از آن می‌توان رمز دیگری تولید کرد (0 - بدون محدودیت). فعال بودن logstore ضروری است.';
$string['logstorerequired'] = '<b>توجه: هیچ logstore فعالی موجود نیست! <a href="{$a}">logstore را فعال کنید</a> یا زمان را به 0 تنظیم کنید.</b>';
$string['fieldsmapping'] = 'نگاشت فیلدهای پروفایل کاربر در هنگام ثبت‌نام';
$string['fieldsmapping_pattern'] = 'الگو';
$string['fieldsmapping_pattern_help'] = 'الگوی گروه‌های Capturing PCRE.';
$string['fieldsmapping_mapping'] = 'نگاشت';
$string['fieldsmapping_mapping_help'] = 'عبارات نگاشت.';
$string['awssenderid'] = 'شناسه فرستنده AWS';
$string['awssenderid_help'] = 'کاربر پیامک را با کدام هویت دریافت می‌کند';
$string['enablemagfa'] = 'فعال کردن سرویس پیامک Magfa';
$string['enablemagfa_help'] = 'فعال کردن سرویس پیامک Magfa و توقف دیگر خدمات';
$string['magfa_username'] = 'نام کاربری Magfa';
$string['magfa_username_help'] = 'نام کاربری Magfa';
$string['magfa_password'] = 'رمز عبور Magfa';
$string['magfa_password_help'] = 'رمز عبور Magfa';
$string['magfa_number'] = 'شماره ثبت شده Magfa';
$string['magfa_number_help'] = 'شماره ثبت شده Magfa';
$string['magfa_domain'] = 'دامنه';
$string['magfa_domain_help'] = 'معمولا کلمه "magfa".';
$string['magfa_templatetext'] = 'قالب پیام';
$string['magfa_templatetext_help'] = 'این متن برای کاربر ارسال می شود. شما می توانید از متغیر "{code}" استفاده نمایید.';
$string['magfa_templatetext_default'] = 'کد فعالسازی شما: {code}';
$string['InvalidParameter'] = 'پارامتر نامعتبر: پیام خالی';
$string['InvalidClientTokenId'] = 'کلید موجود در درخواست نامعتبر است';
$string['SignatureDoesNotMatch'] = 'توکن مخفی موجود در درخواست نامعتبر است';
$string['NotFound'] = 'درخواست نامعتبر';
$string['IncompleteSignature'] = 'اعتبارنامه اشتباه لطفاً اعتبارنامه AWS را بررسی کنید';
$string['cookie'] = 'کوکی‌ها باید در مرورگر شما فعال باشند';
$string['cookie_desc'] = 'این سایت از یک کوکی جلسه استفاده می‌کند که معمولاً MoodleSession نامیده می‌شود.
                          شما باید این کوکی را در مرورگر خود فعال کنید تا تداوم داشته باشد و
                          هنگامی که سایت را مرور می‌کنید، وارد بمانید. وقتی که خارج شوید یا
                          مرورگر را ببندید، این کوکی نابود می‌شود
                          (در مرورگر شما و روی سرور).';
$string['otpbutton'] = 'ورود با OTP';
$string['cookie_help'] = 'کمک با کوکی‌ها باید در مرورگر شما فعال باشند';
$string['forgot'] = 'نام کاربری یا رمز عبور خود را فراموش کرده‌اید؟';
$string['login'] = 'ورود';
$string['otp'] = 'کد تایید';
$string['phone'] = 'شماره همراه';
$string['send'] = 'ارسال';
$string['username'] = 'نام کاربری';
$string['skip'] = 'رد شدن برای ایجاد حساب جدید';
$string['login'] = 'ورود';

$string['error_user_not_found'] = 'کاربری با این شماره ثبت نشده است.';
$string['invalidphonenumber'] = 'شماره تلفن معتبر وارد کنید';
$string['phonenumberempty'] = 'شماره همراه نمیتواند خالی باشد';
