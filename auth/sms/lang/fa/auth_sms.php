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
 * Strings for component 'auth_sms', language 'fa'.
 *
 * @package   auth_sms
 * @copyright 2022 Morteza Ahmadi <m.ahmadi.ma@gmail.com>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['auth_smsdescription'] = 'ثبت نام و ورود کاربران با احراز هویت پیامکی.';
$string['pluginname'] = 'پیامک';
$string['activation_code'] = 'کد فعالسازی';
$string['invalid_activation_code'] = 'کد فعالسازی معتبر نیست';
$string['success_signup_title'] = 'ثبت نام موفق';
$string['success_signup'] = 'ثبت نام شما با موفقیت انجام شد';
$string['phone2exists'] = 'این شماره تلفن همراه قبلا ثبت شده است.';
$string['smsapikey'] = 'کلید api';
$string['smssecretkey'] = 'عبارت رمزی';
$string['method'] = 'روش';
$string['method1_desc'] = 'برای نسخه قدیمی (https://ip.sms.ir)';
$string['method2_desc'] = 'برای نسخه جدید (https://app.sms.ir)';
$string['smstemplateid'] = 'شناسه قالب';
$string['smstemplateid_desc'] = 'از https://app.sms.ir/fast-send/template دریافت کنید.';
$string['wrong_settings'] = 'تنظیمات اشتباه، لطفا با مدیر تماس بگیرید';
$string['smsmagfapassword_desc'] = 'کلمه عبور وب سرویس (از magfa.com)';
$string['smsmagfadomain'] = 'دامنه';
$string['smsmagfadomain_desc'] = 'معمولا کلمه "magfa".';
$string['smsmagfalinenumber'] = 'شماره خط';
$string['smsmagfalinenumber_desc'] = '3000xxxxxxxx';
$string['smstype'] = 'نوع';
$string['smsphoneplace'] = 'مکان ذخیره سازی تلفن همراه';
$string['smsphoneplace_desc'] = 'تلفن همراه می تواند به عنوان تلفن همراه یا در مشخصه های اضافی کاربران ذخیره شود.';
$string['smsmagfatemplatetext'] = 'قالب پیام';
$string['smsmagfatemplatetext_desc'] = 'این متن برای کاربر ارسال می شود. شما می توانید از متغیر "{code}" استفاده نمایید.';
$string['not_exist_error'] = 'وجود ندارد.';
$string['password_mismatch_error'] = 'عدم تطابق رمز عبور';
$string['change_password_success'] = 'رمز عبور شما با موفقیت تغییر کرد.';
$string['smsforgottenpassword'] = 'فراموشی کلمه عبور';
$string['smsforgottenpassword_desc'] = 'پس از تنظیم این ویژگی، لطفا به این <a target="_blank" href="{$a->href}">مسیر</a> رفته و "آدرس صفحهٔ بازیابی رمز ورود (forgottenpasswordurl)" را با مقدار "{$a->url}" تنظیم کنید.';