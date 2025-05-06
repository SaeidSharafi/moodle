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
 * @package    core
 * @subpackage analytics
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['analysablenotused'] = 'تجزیه و تحلیل {$a->analysableid} استفاده نشده است: {$a->errors}';
$string['analysablenotvalidfortarget'] = 'تجزیه و تحلیل {$a->analysableid} برای این هدف معتبر نیست: {$a->result}';
$string['analysisinprogress'] = 'هنوز توسط یک اجرای قبلی در حال تجزیه و تحلیل است';
$string['analyticsdisabled'] = 'تجزیه و تحلیل غیرفعال است. می‌توانید آن را در «مدیریت سایت > ویژگی‌های پیشرفته» فعال کنید.';
$string['analyticslogstore'] = 'ذخیره log مورد استفاده برای تجزیه و تحلیل';
$string['analyticslogstore_help'] = 'ذخیره log که توسط API تجزیه و تحلیل برای خواندن فعالیت کاربران استفاده خواهد شد';
$string['calclifetime'] = 'محاسبات تجزیه و تحلیل برای';
$string['configlcalclifetime'] = 'این گزینه مدت زمانی را که می‌خواهید داده‌های محاسباتی را نگه دارید مشخص می‌کند - این کار پیش‌بینی‌ها را حذف نمی‌کند، اما داده‌های مورد استفاده برای تولید پیش‌بینی‌ها را حذف می‌کند. استفاده از گزینه پیش‌فرض در اینجا بهترین است زیرا استفاده از دیسک شما را تحت کنترل نگه می‌دارد، اما اگر از جداول محاسبه برای اهداف دیگر استفاده می‌کنید، ممکن است بخواهید این مقدار را افزایش دهید.';
$string['defaultpredictionsprocessor'] = 'پردازنده پیش‌بینی پیش‌فرض';
$string['defaultpredictoroption'] = 'پردازنده پیش‌فرض ({$a})';
$string['defaulttimesplittingmethods'] = 'فواصل تجزیه و تحلیل پیش فرض برای ارزیابی مدل';
$string['defaulttimesplittingmethods_help'] = 'بازه تجزیه و تحلیل تعیین می‌کند که سیستم چه زمانی پیش بینی‌ها و بخشی از گزارش‌های فعالیت را که برای آن پیش بینی‌ها در نظر گرفته شود، محاسبه می‌کند. فرآیند ارزیابی مدل در این فواصل تحلیلی تکرار می‌شود مگر اینکه بازه تحلیل خاصی مشخص شده باشد.';
$string['erroralreadypredict'] = 'فایل {$a} قبلاً برای ایجاد پیش‌بینی استفاده شده است.';
$string['errorcannotreaddataset'] = 'فایل مجموعه داده {$a} قابل خواندن نیست.';
$string['errorcannotusetimesplitting'] = 'بازه تجزیه و تحلیل ارائه شده را نمی‌توان در این مدل استفاده کرد.';
$string['errorcannotwritedataset'] = 'فایل مجموعه داده {$a} قابل نوشتن نیست.';
$string['errorexportmodelresult'] = 'مدل یادگیری ماشین را نمی توان صادر کرد.';
$string['errorimport'] = 'خطا در وارد کردن فایل JSON ارائه شده';
$string['errorimportmissingclasses'] = 'اجزای تجزیه و تحلیل زیر در این سایت موجود نیست: {$a->missingclasses}.';
$string['errorimportmissingcomponents'] = 'مدل ارائه شده نیاز به نصب پلاگین‌های زیر دارد: {$a}. توجه داشته باشید که لزوماً لازم نیست نسخه‌ها با نسخه‌های نصب شده در سایت شما مطابقت داشته باشند. نصب همان یا نسخه جدیدتر افزونه در بیشتر موارد خوب است.';
$string['errorimportversionmismatches'] = 'نسخه اجزای زیر با نسخه نصب شده در این سایت متفاوت است: {$a}. برای نادیده گرفتن این تفاوت‌ها می‌توانید از گزینه "نادیده گرفتن عدم تطابق نسخه" استفاده کنید.';
$string['errorinvalidcontexts'] = 'برخی از زمینه‌های انتخاب شده را نمی‌توان در این هدف استفاده کرد.';
$string['errorinvalidindicator'] = 'شاخص {$a} نامعتبر است';
$string['errorinvalidtarget'] = 'هدف {$a} نامعتبر است';
$string['errorinvalidtimesplitting'] = 'فاصله تجزیه و تحلیل نامعتبر؛ لطفاً مطمئن شوید که نام کلاس کاملاً واجد شرایط را اضافه کنید.';
$string['errornocontextrestrictions'] = 'هدف انتخاب شده از محدودیت‌های زمینه پشتیبانی نمی‌کند';
$string['errornoexportconfig'] = 'مشکلی در صدور پیکربندی مدل وجود داشت.';
$string['errornoexportconfigrequirements'] = 'فقط مدل‌های غیر استاتیک با بازه تجزیه و تحلیل را می‌توان صادر کرد.';
$string['errornoindicators'] = 'این مدل هیچ شاخصی ندارد.';
$string['errornopredictresults'] = 'هیچ نتیجه‌ای از پردازشگر پیش‌بینی‌ها برگردانده نشد. برای اطلاعات بیشتر محتویات دایرکتوری خروجی را بررسی کنید.';
$string['errornoroles'] = 'نقش دانش آموز یا معلم تعریف نشده است. آنها را در صفحه تنظیمات تجزیه و تحلیل تعریف کنید.';
$string['errornotarget'] = 'این مدل هیچ هدفی ندارد.';
$string['errornotimesplittings'] = 'این مدل بازه تجزیه و تحلیلی ندارد.';
$string['errorpredictioncontextnotavailable'] = 'این زمینه پیش‌بینی دیگر در دسترس نیست.';
$string['errorpredictionformat'] = 'فرمت محاسبات پیش بینی اشتباه است';
$string['errorpredictionsprocessor'] = 'خطای پردازنده پیش‌بینی: {$a}';
$string['errorpredictwrongformat'] = 'بازگشتی پردازشگر پیش‌بینی‌ها را نمی‌توان رمزگشایی کرد: "{$a}';
$string['errorprocessornotready'] = 'پردازشگر پیش‌بینی انتخابی آماده نیست: {$a}';
$string['errorsamplenotavailable'] = 'نمونه پیش‌بینی‌شده دیگر در دسترس نیست.';
$string['errorunexistingmodel'] = 'مدل ناموجود {$a}';
$string['errorunexistingtimesplitting'] = 'فاصله تجزیه و تحلیل انتخاب شده در دسترس نیست.';
$string['errorunknownaction'] = 'اقدام ناشناخته';
$string['eventinsightsviewed'] = 'بینش مشاهده شد';
$string['eventpredictionactionstarted'] = 'فرآیند پیش بینی آغاز شد';
$string['fixedack'] = 'قبول';
$string['incorrectlyflagged'] = 'به اشتباه پرچم گذاری شده است';
$string['insightinfomessagehtml'] = 'سیستم یک بینش برای شما ایجاد کرد.';
$string['insightinfomessageplain'] = 'سیستم یک اطلاعات بینش برای شما ایجاد کرد: {$a}';
$string['insightmessagesubject'] = 'بینش جدید برای "{$a}"';
$string['invalidanalysablefortimesplitting'] = 'نمی‌توان آن را با استفاده از بازه تحلیل {$a} تجزیه و تحلیل کرد.';
$string['invalidtimesplitting'] = 'مدل با شناسه {$a} قبل از استفاده از آن برای آموزش نیاز به یک بازه تحلیل دارد.';
$string['levelinstitution'] = 'سطح آموزش';
$string['levelinstitutionisced0'] = 'آموزش در دوران کودکی (برای پیشرفت تحصیلی «کمتر از ابتدایی»)';
$string['levelinstitutionisced1'] = 'آموزش ابتدایی';
$string['levelinstitutionisced2'] = 'تحصیلات متوسطه پایین';
$string['levelinstitutionisced3'] = 'تحصیلات متوسطه بالا';
$string['levelinstitutionisced4'] = 'آموزش غیر عالی پس از متوسطه (ممکن است شامل آموزش شرکتی یا اجتماعی/NGO باشد)';
$string['levelinstitutionisced5'] = 'دوره کوتاه آموزش عالی (ممکن است شامل آموزش شرکتی یا اجتماعی/NGO باشد)';
$string['levelinstitutionisced6'] = 'مقطع کارشناسی یا معادل آن';
$string['levelinstitutionisced7'] = 'مقطع کارشناسی ارشد یا معادل آن';
$string['levelinstitutionisced8'] = 'مقطع دکترا یا معادل آن';
$string['modeinstruction'] = 'شیوه‌های آموزش';
$string['modeinstructionblendedhybrid'] = 'ترکیبی';
$string['modeinstructionfacetoface'] = 'حضوری';
$string['modeinstructionfullyonline'] = 'آنلاین';
$string['modeloutputdir'] = 'دایرکتوری خروجی مدل ها';
$string['modeloutputdirinfo'] = 'فهرستی که پردازنده‌های پیش‌بینی تمام اطلاعات ارزیابی را در آن ذخیره می‌کنند. برای رفع اشکال و تحقیق مفید است.';
$string['modeloutputdirwithdefaultinfo'] = 'فهرستی که پردازنده‌های پیش‌بینی تمام اطلاعات ارزیابی را در آن ذخیره می‌کنند. برای رفع اشکال و تحقیق مفید است. اگر خالی باشد، از {$a} به عنوان پیش‌فرض استفاده می‌شود.';
$string['modeltimelimit'] = 'محدودیت زمانی تجزیه و تحلیل برای هر مدل';
$string['modeltimelimitinfo'] = 'این تنظیم زمانی را که هر مدل صرف تجزیه و تحلیل محتوای سایت می‌کند محدود می‌کند.';
$string['neutral'] = 'خنثی';
$string['neverdelete'] = 'هرگز محاسبات را حذف نشود.';
$string['noevaluationbasedassumptions'] = 'مدل‌های مبتنی بر فرضیات را نمی‌توان ارزیابی کرد.';
$string['noinsights'] = 'بینشی گزارش نشده است';
$string['noinsightsmodel'] = 'این مدل بینش ایجاد نمی‌کند';
$string['nonewranges'] = 'هنوز هیچ پیش بینی جدیدی وجود ندارد. مدل بعد از بازه تحلیل بعدی مورد تجزیه و تحلیل قرار خواهد گرفت.';
$string['nopredictionsyet'] = 'هیچ پیش‌بینی در دسترس نیست';
$string['noranges'] = 'هنوز هیچ پیش بینی وجود ندارد';
$string['notapplicable'] = 'قابل اعمال نیست';
$string['notrainingbasedassumptions'] = 'مدل‌های مبتنی بر فرضیات نیازی به آموزش ندارند';
$string['onlycli'] = 'تجزیه و تحلیل اجرا را فقط از طریق خط فرمان پردازش می‌کند';
$string['onlycliinfo'] = 'فرآیندهای تجزیه و تحلیل مانند ارزیابی مدل‌ها، آموزش الگوریتم‌های یادگیری ماشین یا گرفتن پیش‌بینی ممکن است کمی طول بکشد. آنها به عنوان وظایف cron اجرا می شوند یا ممکن است از طریق خط فرمان مجبور شوند. اگر غیرفعال باشد، فرآیندهای تجزیه و تحلیل را می توان به صورت دستی از طریق رابط وب اجرا کرد.';
$string['percentonline'] = 'درصد آنلاین';
$string['percentonline_help'] = 'اگر سازمان شما دوره های ترکیبی ارائه می‌دهد، چند درصد از کار دانشجویی به صورت آنلاین در مودل انجام می‌شود؟ عددی بین 0 تا 100 وارد کنید.';
$string['predictionsprocessor'] = 'پردازنده پیش بینی';
$string['predictionsprocessor_help'] = 'یک پردازنده پیش‌بینی، پشتیبان یادگیری ماشینی است که مجموعه داده‌های تولید شده با محاسبه شاخص‌ها و اهداف مدل‌ها را پردازش می‌کند. هر مدل می تواند از یک پردازنده متفاوت استفاده کند. موردی که در اینجا مشخص شده است پیش فرض خواهد بود.';
$string['privacy:metadata:analytics:analyticsmodels'] = 'مدل‌های تجزیه و تحلیل';
$string['privacy:metadata:analytics:analyticsmodels:usermodified'] = 'کاربری که مدل را تغییر داده است';
$string['privacy:metadata:analytics:analyticsmodelslog'] = 'log مورد استفاده برای مدل‌های تجزیه و تحلیل';
$string['privacy:metadata:analytics:analyticsmodelslog:usermodified'] = 'کاربری که log را تغییر داده است';
$string['privacy:metadata:analytics:indicatorcalc'] = 'محاسبات شاخص';
$string['privacy:metadata:analytics:indicatorcalc:contextid'] = 'زمینه';
$string['privacy:metadata:analytics:indicatorcalc:indicator'] = 'کلاس ماشین حساب شاخص';
$string['privacy:metadata:analytics:indicatorcalc:sampleid'] = 'شناسه نمونه';
$string['privacy:metadata:analytics:indicatorcalc:sampleorigin'] = 'جدول مبدا نمونه';
$string['privacy:metadata:analytics:indicatorcalc:timecreated'] = 'زمانی که پیش بینی انجام شده';
$string['privacy:metadata:analytics:predictionactions'] = 'اقدامات پیش بینی';
$string['privacy:metadata:analytics:predictionactions:actionname'] = 'نام عمل';
$string['privacy:metadata:analytics:predictionactions:predictionid'] = 'شناسه پیش بینی';
$string['privacy:metadata:analytics:predictionactions:timecreated'] = 'زمانی که عمل پیش بینی انجام شد';
$string['privacy:metadata:analytics:predictionactions:userid'] = 'کاربری که اقدام را انجام داده است';
$string['privacy:metadata:analytics:predictions:calculations'] = 'محاسبات شاخص';
$string['privacy:metadata:analytics:predictions:contextid'] = 'زمینه';
$string['privacy:metadata:analytics:predictions:modelid'] = 'شناسه مدل';
$string['privacy:metadata:analytics:predictions:rangeindex'] = 'شاخص فاصله تحلیل';
$string['privacy:metadata:analytics:predictions:sampleid'] = 'شناسه نمونه';
$string['privacy:metadata:analytics:predictions:timecreated'] = 'زمانی که پیش بینی انجام شده';
$string['processingsitecontents'] = 'در حال پردازش محتویات سایت';
$string['timesplittingmethod'] = 'بازه تجزیه و تحلیل';
$string['timesplittingmethod_help'] = 'بازه تجزیه و تحلیل تعیین می کند که سیستم چه زمانی پیش بینی ها و بخشی از گزارش های فعالیت را که برای آن پیش بینی ها در نظر گرفته می شود، محاسبه می کند. به عنوان مثال، مدت دوره ممکن است به بخش‌هایی تقسیم شود که در پایان هر بخش یک پیش‌بینی ایجاد می‌شود.';
$string['typeinstitution'] = 'نوع موسسه';
$string['typeinstitutionacademic'] = 'آکادمیک';
$string['typeinstitutionngo'] = 'سازمان غیردولتی (NGO)';
$string['typeinstitutiontraining'] = 'آموزش مشارکتی';
$string['useful'] = 'مفید';
$string['viewdetails'] = 'مشاهده جزئیات';
$string['viewinsight'] = 'مشاهده بینش';
$string['viewinsightdetails'] = 'مشاهده جزئیات بینش';
$string['viewprediction'] = 'مشاهده جزئیات پیش بینی';
$string['washelpful'] = 'آیا این مفید بود؟';
