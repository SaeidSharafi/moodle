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
 * Strings for component 'enrol_acecr', language 'en'.
 *
 * @package    enrol_acecr
 * @copyright  1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['assignrole'] = 'Assign role';
$string['businessemail'] = 'AcecrPayment business email';
$string['businessemail_desc'] = 'The email address of your business AcecrPayment account';
$string['cost'] = 'Enrol cost';
$string['costerror'] = 'The enrolment cost is not numeric';
$string['costorkey'] = 'Please choose one of the following methods of enrolment.';
$string['currency'] = 'Currency';
$string['defaultrole'] = 'Default role assignment';
$string['defaultrole_desc'] = 'Select role which should be assigned to users during AcecrPayment enrolments';
$string['enrolenddate'] = 'End date';
$string['enrolenddate_help'] = 'If enabled, users can be enrolled until this date only.';
$string['enrolenddaterror'] = 'Enrolment end date cannot be earlier than start date';
$string['enrolperiod'] = 'Enrolment duration';
$string['enrolperiod_desc'] = 'Default length of time that the enrolment is valid. If set to zero, the enrolment duration will be unlimited by default.';
$string['enrolperiod_help'] = 'Length of time that the enrolment is valid, starting with the moment the user is enrolled. If disabled, the enrolment duration will be unlimited.';
$string['enrolstartdate'] = 'Start date';
$string['enrolstartdate_help'] = 'If enabled, users can be enrolled from this date onward only.';
$string['errdisabled'] = 'The AcecrPayment enrolment plugin is disabled and does not handle payment notifications.';
$string['erripninvalid'] = 'Instant payment notification has not been verified by AcecrPayment.';
$string['erracecrconnect'] = 'Could not connect to {$a->url} to verify the instant payment notification: {$a->result}';
$string['expiredaction'] = 'Enrolment expiry action';
$string['expiredaction_help'] = 'Select action to carry out when user enrolment expires. Please note that some user data and settings are purged from course during course unenrolment.';
$string['mailadmins'] = 'Notify admin';
$string['mailstudents'] = 'Notify students';
$string['mailteachers'] = 'Notify teachers';
$string['messageprovider:acecr_enrolment'] = 'AcecrPayment enrolment messages';
$string['nocost'] = 'There is no cost associated with enrolling in this course!';
$string['acecr:config'] = 'Configure AcecrPayment enrol instances';
$string['acecr:manage'] = 'Manage enrolled users';
$string['acecr:unenrol'] = 'Unenrol users from course';
$string['acecr:unenrolself'] = 'Unenrol self from the course';
$string['acecraccepted'] = 'AcecrPayment payments accepted';
$string['pluginname'] = 'AcecrPayment';
$string['pluginname_desc'] = 'The AcecrPayment module allows you to set up paid courses.  If the cost for any course is zero, then students are not asked to pay for entry.  There is a site-wide cost that you set here as a default for the whole site and then a course setting that you can set for each course individually. The course cost overrides the site cost.';
$string['privacy:metadata:enrol_acecr:enrol_acecr'] = 'Information about the AcecrPayment transactions for AcecrPayment enrolments.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:business'] = 'Email address or AcecrPayment account ID of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:courseid'] = 'The ID of the course that is sold.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:instanceid'] = 'The ID of the enrolment instance in the course.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:item_name'] = 'The full name of the course that its enrolment has been sold.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:memo'] = 'A note that was entered by the buyer in AcecrPayment website payments note field.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:option_selection1_x'] = 'Full name of the buyer.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:parent_txn_id'] = 'In the case of a refund, reversal, or canceled reversal, this would be the transaction ID of the original transaction.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:payment_status'] = 'The status of the payment.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:payment_type'] = 'Holds whether the payment was funded with an eCheck (echeck), or was funded with AcecrPayment balance, credit card, or instant transfer (instant).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:pending_reason'] = 'The reason why payment status is pending (if that is).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:reason_code'] = 'The reason why payment status is Reversed, Refunded, Canceled_Reversal, or Denied (if the status is one of them).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:receiver_email'] = 'Primary email address of the payment recipient (that is, the merchant).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:receiver_id'] = 'Unique AcecrPayment account ID of the payment recipient (i.e., the merchant).';
$string['privacy:metadata:enrol_acecr:enrol_acecr:tax'] = 'Amount of tax charged on payment.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:timeupdated'] = 'The time of Moodle being notified by AcecrPayment about the payment.';
$string['privacy:metadata:enrol_acecr:enrol_acecr:txn_id'] = 'The merchant\'s original transaction identification number for the payment from the buyer, against which the case was registered';
$string['privacy:metadata:enrol_acecr:enrol_acecr:userid'] = 'The ID of the user who bought the course enrolment.';
$string['privacy:metadata:enrol_acecr:acecr_com'] = 'The AcecrPayment enrolment plugin transmits user data from Moodle to the AcecrPayment website.';
$string['privacy:metadata:enrol_acecr:acecr_com:address'] = 'Address of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:city'] = 'City of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:country'] = 'Country of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:custom'] = 'A hyphen-separated string that contains ID of the user (the buyer), ID of the course, ID of the enrolment instance.';
$string['privacy:metadata:enrol_acecr:acecr_com:email'] = 'Email address of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:first_name'] = 'First name of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:last_name'] = 'Last name of the user who is buying the course.';
$string['privacy:metadata:enrol_acecr:acecr_com:os0'] = 'Full name of the buyer.';
$string['processexpirationstask'] = 'AcecrPayment enrolment send expiry notifications task';
$string['sendpaymentbutton'] = 'Send payment via AcecrPayment';
$string['status'] = 'Allow AcecrPayment enrolments';
$string['status_desc'] = 'Allow users to use AcecrPayment to enrol into a course by default.';
$string['transactions'] = 'AcecrPayment transactions';
$string['unenrolselfconfirm'] = 'Do you really want to unenrol yourself from course "{$a}"?';
$string['assignrole'] = 'انتساب نقش';
$string['businessemail'] = 'ایمیل کسب وکار درگاه جهاددانشگاهی';
$string['businessemail_desc'] = 'آدرس ایمیل حساب کاربری درگاه جهاددانشگاهی کسب و کار شما';
$string['cost'] = 'هزینه ثبت نام';
$string['costerror'] = 'هزینه ثبت نام عددی نیست';
$string['costorkey'] = 'لطفا یکی از روش های زیر ثبت نام را انتخاب کنید.';
$string['currency'] = 'واحد پول';
$string['defaultrole'] = 'انتساب نقش به طور پیشفرض';
$string['defaultrole_desc'] = 'انتخاب نقشی که باید به کاربران پس از پرداخت در طول دوره اختصاص داده شود';
$string['enrolenddate'] = 'تاریخ پایان';
$string['enrolenddate_help'] = 'اگر فعال شود، کاربران می توانند فقط تا این روز ثبت نام نمایند';
$string['enrolenddaterror'] = 'تاریخ پایان ثبت نام نمیتواند زودتر از تاریخ شروع باشد';
$string['enrolperiod'] = 'مهلت ثبت نام';
$string['enrolperiod_desc'] = 'مدت زمان پیش فرض که ثبت نام معتبر است. اگر روی صفر تنظیم شود، مدت زمان ثبت نام به طور پیش فرض نامحدود خواهد بود.';
$string['enrolperiod_help'] = 'مدت زمانی که ثبت نام معتبر است، با لحظه ای که کاربر ثبت نام می کند آغاز می شود. اگر غیرفعال باشد، مدت زمان ثبت نام نامحدود خواهد بود.';
$string['enrolstartdate'] = 'تاریخ شروع';
$string['enrolstartdate_help'] = 'اگر فعال باشد، کاربران فقط می توانند از این تاریخ به بعد ثبت نام کنند.';
$string['expiredaction'] = 'عملیات انقضای ثبت نام';
$string['expiredaction_help'] = 'انتخاب عملیات برای زمانی که ثبت نام کاربر منقضی می شود. لطفا توجه داشته باشید که برخی تنظیمات و اطلاعات  کابر در هنگام غیرفعال کردن ثبت نام از دوره پاک شده است.';
$string['mailadmins'] = 'مدیر را آگاه کن';
$string['mailstudents'] = 'دانشجو را آگاه کن';
$string['mailteachers'] = 'مدرس را آگاه کن';
$string['messageprovider:acecr_enrolment'] = 'پیغام ثبت نام بعد از پرداخت';
$string['nocost'] = 'هیچ هزینه ای مرتبط با ثبت نام در این دوره وجود ندارد!';
$string['acecr:config'] = 'پیکربندی درگاه جهاددانشگاهی ثبت نام';
$string['acecr:manage'] = 'مدیریت کاربران ثبت نام شده';
$string['acecr:unenrol'] = 'غیرفعال کردن ثبت نام کاربران از دوره';
$string['acecr:unenrolself'] = 'غیرفعال کردن ثبت نام خود از دوره';
$string['acecraccepted'] = 'پرداخت های پذیرفته شده';
$string['pluginname'] = 'درگاه جهاددانشگاهی';
$string['pluginname_desc'] = 'ماژول پرداخت جهاددانشگاهی به شما اجازه می دهد که پرداخت برای دوره ها را راه اندازی کنید. اگر هزینه برای هر دوره صفر باشد، آنگاه از دانشجو درخواست نمیشود تا برای ورود پرداخت کند. یک هزینه  گستره سایت وجود دارد که شما در اینجا به طور پیش فرض برای کل سایت تنظیم می‌کنید و سپس یک تنظیمات برای دوره وجود دارد که شما می‌توانید برای هر دوره به صورت جداگانه تنظیمات انجام دهید. هزینه دوره هزینه سایت را لغو می کند';
$string['sendpaymentbutton'] = 'ارسال برای درگاه پرداخت';
$string['status'] = 'اجازه پرداخت ثبت نام';
$string['status_desc'] = 'اجازه به کاربران برای استفاده ازدرگاه پرداخت برای ثبت نام در یک دوره به طور پیش فرض';
$string['unenrolselfconfirm'] = 'آیا شما واقعا  قصد دارید ثبت نام خود از این دوره را غیر فعال کنید؟ "{$a}"?';
$string['terminal'] = 'ترمینال پذیرنده';
$string['username'] = 'نام کاربری';
$string['password'] = 'رمز عبور';
$string['terminal_desc'] = '';
$string['username_desc'] = '';
$string['password_desc'] = '';
$string['paymentsorryM'] = 'تراکنش نا موفق ، لطفا دوباره تلاش نمایید';
$string['paymentthanks'] = 'با تشکر، پرداخت شما با موفقیت ثبت شد.';
$string['success'] = 'تراکنش با موفقیت انجام شد.';
$string['e11'] = 'شماره کارت معتبر نیست.';
$string['e12'] = 'موجودی کافی نیست.';
$string['e13'] = 'رمز دوم شما صحیح نیست.';
$string['e14'] = 'دفعات مجاز ورود رمز بیش از حد است.';
$string['e15'] = 'کارت معتبر نیست.';
$string['e16'] = 'دفعات برداشت وجه بیش از حد مجاز است.';
$string['e17'] = 'شما از انجام تراکنش منصرف شده اید.';
$string['e18'] = 'تاریخ انقضای کارت گذشته است.';
$string['e19'] = 'مبلغ برداشت وجه بیش از حد مجاز است.';
$string['e111'] = 'صادر کننده کارت نامعتبر است.';
$string['e112'] = 'خطای سوییچ صادر کننده کارت رخ داده است.';
$string['e113'] = 'پاسخی از صادر کننده کارت دریافت نشد.';
$string['e114'] = 'دارنده کارت مجاز به انجام این تراکنش نمی باشد.';
$string['e21'] = 'پذیرنده معتبر نیست.';
$string['e23'] = 'خطای امنیتی رخ داده است.';
$string['e24'] = 'اطلاعات کاربری پذیرنده معتبر نیست.';
$string['e25'] = 'مبلغ نامعتبر است.';
$string['e31'] = 'پاسخ نامعتبر است.';
$string['e32'] = 'فرمت اطلاعات وارد شده صحیح نیست.';
$string['e33'] = 'حساب نامعتبر است.';
$string['e34'] = 'خطای سیستمی رخ داده است.';
$string['e35'] = 'تاریخ نامعتبر است.';
$string['e41'] = 'شماره درخواست تکراری است.';
$string['e42'] = 'همچین تراکنشی وجود ندارد.';
$string['e43'] = 'قبلا درخواست Verify داده شده است';
$string['e44'] = 'درخواست Verify یافت نشد.';
$string['e45'] = 'تراکنش قبلا Settle شده است.';
$string['e46'] = 'تراکنش Settle نشده است.';
$string['e47'] = 'تراکنش Settle یافت نشد.';
$string['e48'] = 'تراکنش قبلا Reverse شده است.';
$string['e49'] = 'تراکنش Refund یافت نشد.';
$string['e412'] = 'شناسه قبض نادرست است.';
$string['e413'] = 'شناسه پرداخت نادرست است.';
$string['e414'] = 'سازمان صادر کننده قبض معتبر نیست.';
$string['e415'] = 'زمان جلسه کاری به پایان رسیده است.';
$string['e416'] = 'خطا در ثبت اطلاعات رخ داده است.';
$string['e417'] = 'شناسه پرداخت کننده نامعتبر است.';
$string['e418'] = 'اشکال در تعریف اطلاعات مشتری رخ داده است.';
$string['e419'] = 'تعداد دفعات ورود اطلاعات بیش از حد مجاز است.';
$string['e421'] = 'IP معتبر نیست.';
$string['e51'] = 'تراکنش تکراری است.';
$string['e54'] = 'تراکنش مرجع موجود نیست.';
$string['e55'] = 'تراکنش نامعتبر است.';
$string['e61'] = 'خطا در واریز رخ داده است.';
$string['ex'] = 'در حین پرداخت خطای سیستمی رخ داده است.';
