<?php
require_once('../../config.php');
global $USER;
$systemcontext = context_system::instance();
if (!has_capability('moodle/site:config', $systemcontext) && $USER->id != "14334") {
    header('Location: '.$CFG->wwwroot);
    return;
}
?>

<!DOCTYPE html>
<html dir="rtl" lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/css/jquery-ui.min.css">
    <link rel="stylesheet" href="assets/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="assets/css/jquery-confirm.min.css">
    <link href="assets/fonts/fontawesome/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <title>همگام سازی اطلاعات سامانه</title>

</head>
<body class="h-100">
<div class="d-flex h-100 align-items-start py-2">
    <div class="mx-auto w-75">


        <ul class="nav nav-tabs" id="mainTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active"
                   id="all-data-tab"
                   data-toggle="tab"
                   href="#all"
                   role="tab"
                   aria-controls="all-data"
                   aria-selected="true">دریافت تمامی اطلاعات</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="lesson-tab"
                   data-toggle="tab"
                   href="#lesson"
                   role="tab"
                   aria-controls="lesson"
                   aria-selected="false">دریافت اطلاعات درس</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="professors-tab"
                   data-toggle="tab"
                   href="#professors"
                   role="tab"
                   aria-controls="professors"
                   aria-selected="false">دریافت دروس اساتید</a>
            </li>
            <li class="nav-item">
                <a class="nav-link"
                   id="students-tab"
                   data-toggle="tab"
                   href="#students"
                   role="tab"
                   aria-controls="students"
                   aria-selected="false">دریافت دروس دانشجو</a>
            </li>
        </ul>
        <div class="tab-content  position-relative" id="myTabContent">
            <div class="position-absolute w-100 h-100 loader-container" style="display: none">
                <div class="loader-bg w-100 h-100"></div>
                <div class="loader-holder">
                    <div class="loader mb-2"></div>
                    <a class="btn btn-cancel bg-danger text-white"
                       onclick="cancelOperation()"
                       data-text="لغو عملیات"
                       data-url="request_handler.php">لغو عملیات</a>
                </div>


            </div>
            <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                <div class="form-group">
                    <label for="term">کد ترم</label>
                    <input class="form-control numeric term"
                           id="all-term"
                           name="term"
                           placeholder="example: 14021"
                           required />
                </div>
                <div class="form-group d-none">
                    <label for="term">کد مقاطع تحصیلی</label>
                    <input type="hidden" class="form-control study-level"
                           id="all-study-level"
                           name="study_level"
                           placeholder="example: 5,7"
                           value="0"
                           required />
                </div>
                <input type="hidden" id="current-step" >
                <div class="form-controls">
                    <a class="mb-2 btn btn-op bg-warning btn-outline-danger text-black"
                       data-parent="all"
                       data-api="all"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت اطلاعات دانشپذیران"
                       data-url="request_handler.php">ثبت اطلاعات اتوماتیک</a>
                    <div class="w-100"></div>
                    <a class="mb-2 btn btn-op bg-info text-white"
                       data-action="enrol"
                       data-parent="all"
                       data-api="ImportStudents"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت اطلاعات دانشپذیران"
                       data-url="request_handler.php">ثبت اطلاعات دانشپذیران</a>


                    <a class="mb-2 btn btn-op bg-info RegisterData text-white"
                       data-parent="all"
                       data-api="ImportTeachers"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت اطلاعات اساتید"
                       data-url="request_handler.php">ثبت اطلاعات اساتید</a>
                    <a class="mb-2 btn btn-op bg-success RegisterData text-white"
                       data-parent="all"
                       data-api="ImportLessons"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت دوره ها"
                       data-url="request_handler.php">ثبت دوره ها</a>

                    <a class="mb-2 btn btn-op bg-success RegisterData text-white"
                       data-parent="all"
                       data-api="ImportEnrolments"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت نام دانشپذیران"
                       data-url="request_handler.php">ثبت نام دانشپذیران</a>
                    <a class="mb-2 btn btn-op bg-success RegisterData text-white"
                       data-parent="all"
                       data-api="ImportTeacherEnrolments"
                       onclick="getAPIDataLoop(this,false)"
                       data-text="ثبت نام اساتید"
                       data-url="request_handler.php">ثبت نام اساتید</a>

                </div>

            </div>
            <div class="tab-pane fade" id="lesson" role="tabpanel" aria-labelledby="lesson-tab">
                <div class="form">
                    <!--                    <div class="form-group">-->
                    <!--                        <label for="term">کد دانشکده</label>-->
                    <!--                        <input class="form-control numeric faculty" id="courses-faculty" name="faculty" placeholder="example: 13992" required/>-->
                    <!--                    </div>-->
                    <div class="form-group">
                        <label for="term">کد ترم</label>
                        <input class="form-control numeric term"
                               id="courses-term"
                               name="term"
                               placeholder="example: 14021"
                               required />
                    </div>
                    <div class="form-group">
                        <label for="term">کد درس</label>
                        <input class="form-control numeric lesson"
                               id="courses-lesson"
                               name="lesson"
                               placeholder="example: 1"
                               required />
                    </div>
                    <div class="form-group">
                        <label for="term">کد گروه درس</label>
                        <input class="form-control numeric group"
                               id="courses-group"
                               name="group"
                               placeholder="example: 1"
                               required />
                    </div>
                </div>
                <div class="form-controls">
                        <a class="mb-2 btn btn-op bg-info text-white"
                           data-parent="lesson"
                           data-api="ImportLessonEnrollments"
                           onclick="getAPIDataLoop(this,1)"
                           data-text="دریافت اطلاعات"
                           data-url="request_handler.php">دریافت اطلاعات</a>
                        <a class="mb-2 btn btn-op bg-success text-white"
                           data-parent="lesson"
                           data-api="ImportLessonEnrollments"
                           onclick="getAPIDataLoop(this)"
                           data-text="ثبت اطلاعات"
                           data-url="request_handler.php">ثبت اطلاعات</a>
                </div>

            </div>


            <div class="tab-pane fade" id="professors" role="tabpanel" aria-labelledby="professors-tab">

                <p class="d-none">
                    <i class="fa fa-info-circle text-danger"></i>
                    <span class="text-danger">
                        در حال حاضر به دلیل مشکل در وب سرویس سامانه سما، ممکن است برخی دروس با استفاده این روش دریافت نشوند، لطفا قبل از ثبت، از صحت اطلاعات دریافتی اطمینان حاصل نمایید
                    </span>
                </p>
                <div class="form">
                    <div class="form-group">
                        <label for="term">کد ترم</label>
                        <input class="form-control numeric term"
                               id="professors-term"
                               name="term"
                               placeholder="example: 14021"
                               required />
                    </div>
                    <div class="form-group">
                        <label for="term">کد استاد</label>
                        <input class="form-control numeric professor"
                               id="professors-code"
                               name="professor"
                               placeholder="example: 1"
                               required />
                    </div>
                </div>
                <div class="form-controls">
                    <a class="mb-2 btn btn-op bg-info text-white"
                       data-parent="professors"
                       data-api="ProfessorEnrollments"
                       onclick="getAPIDataLoop(this,1)"
                       data-text="دریافت اطلاعات"
                       data-url="request_handler.php">دریافت اطلاعات</a>
                    <a class="mb-2 btn btn-op bg-success text-white"
                       data-parent="professors"
                       data-api="ProfessorEnrollments"
                       onclick="getAPIDataLoop(this)"
                       data-text="ثبت اطلاعات"
                       data-url="request_handler.php">ثبت اطلاعات</a>
                </div>

            </div>
            <div class="tab-pane fade" id="students" role="tabpanel" aria-labelledby="students-tab">
                <div class="form">
                    <div class="form-group">
                        <label for="term">کد ترم</label>
                        <input class="form-control numeric term"
                               id="student-term"
                               name="term"
                               placeholder="example: 14021"
                               required />
                    </div>
                    <div class="form-group">
                        <label for="term">کد دانشجو</label>
                        <input class="form-control numeric student"
                               id="student-code"
                               name="professor"
                               placeholder="example: 981414084"
                               required />
                    </div>
                    <div class="form-group d-none">
                        <label for="term">کد مقاطع تحصیلی</label>
                        <input type="hidden" class="form-control study-level"
                               id="student-study-level"
                               name="study_level"
                               placeholder="example: 5,7"
                               value="5,7"
                               required />
                    </div>
                </div>
                <div class="form-controls">
                    <a class="mb-2 btn btn-op bg-info text-white"
                       data-parent="students"
                       data-api="studentEnrollments"
                       onclick="getAPIDataLoop(this,1)"
                       data-text="دریافت اطلاعات"
                       data-url="request_handler.php">دریافت اطلاعات</a>
                    <a class="mb-2 btn btn-op bg-success text-white"
                       data-parent="students"
                       data-api="studentEnrollments"
                       onclick="getAPIDataLoop(this)"
                       data-text="ثبت اطلاعات"
                       data-url="request_handler.php">ثبت اطلاعات</a>
                </div>
            </div>
        </div>

        <div class="export-panel">

            <div class="d-flex pager">
                <div class="w-75 text-center" id="pager">-</div>
                <div class="d-flex w-25 align-items-center">
                    <div class="w-50 text-center timer" id="timer">00:00</div>
                    |
                    <div class="w-50 text-center timerAll" id="timerAll">0:00:00</div>
                </div>

            </div>
            <div class="d-flex">
                <div class="w-100 text-center counter saved text-success ">
                    جدید:
                    <span class="value">0</span>
                </div>
                <div class="w-100 text-center counter edited text-warning">
                    بروزرسانی:
                    <span class="value">0</span>
                </div>
                <div class="w-100 text-center counter skipped text-info">
                    بدون تغییر:
                    <span class="value">0</span>
                </div>
                <div class="w-100 text-center counter failed text-danger">
                    خطا:
                    <span class="value">0</span>
                </div>
            </div>

            <div class="report-container" style="">
            </div>
            <div id="export" class="modal-body" style="height:300px;max-height: 300px;overflow-y: scroll">

            </div>


        </div>
    </div>
</div>
<input id="page-index" type="hidden">
<input id="study-level" type="hidden">
<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/jquery-confirm.min.js"></script>
<script src="assets/js/filesaver.min.js"></script>
<script src="assets/js/jquery.dataTables.min.js"></script>
<script src="assets/js/datetime.js"></script>
<script src="assets/js/scripts.js"></script>
</body>
</html>


