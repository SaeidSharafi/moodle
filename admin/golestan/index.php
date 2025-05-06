<!DOCTYPE html>
<html lang="en" class="h-100">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <title>همگام سازی اطلاعات سامانه</title>
    <style>
        .info, .info-header {
            font-size: 14px;
            border-bottom: 1px solid #ccc;
            padding: 5pt 0;
        }

        .info:nth-child(odd) {
            background: #ececec;
        }

        .info-header {
            background: #39cc52;
            text-align: center;
            font-weight: bold;
        }

        .modal-dialog {
            max-width: 800px;
        }
        .arrow {
            border: solid white;
            border-width: 0 3px 3px 0;
            display: inline-block;
            padding: 3px;
            transform: rotate(-135deg);
            -webkit-transform: rotate(-135deg);
        }
        .collapsed .arrow {
            transform: rotate(45deg);
            -webkit-transform: rotate(45deg);
        }
    </style>
</head>
<body class="h-100">
<div class="d-flex h-100 align-items-center">
    <div class="jumbotron m-auto w-50">
        <div class="form-group">
            <label for="term">کد ترم</label>
            <input class="form-control" id="term" name="term" value="4001"/>
        </div>
        <div class="form-group">
            <label for="centers">کد های مراکز آموزشی</label>
            <input class="form-control" id="centers" name="centers" value="1,2,3,4,5,6,7,8,9,10"/>
            <small id="centersHelp" class="form-text text-muted">کد ها را با , از یکدیگر جدا نمایید مانند: 1,2,3,4,5
            </small>
        </div>
        <hr/>
        <button class="btn btn-info collapsed w-100" style="direction: rtl" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false"
                aria-controls="collapseExample">
            اطلاعات درس
            <i class="arrow"></i>
        </button>

        <div class="collapse" id="collapseExample">
            <div class="form-group">
                <label for="term">کد مقطع</label>
                <input class="form-control" id="crs_degree" name="crs_degree" placeholder="1-10"/>
                <small id="centersHelp" class="form-text text-muted" style="direction: rtl !important;">
                    کدها را به صورت تک و یا به صورت از-تا وارد کنید:
                    1
                    یا
                    <span style="direction: ltr !important;" class="d-inline-block"> 1-10</span>
                </small>
            </div>
            <div class="form-group">
                <label for="term">کد گروه آموزشی</label>
                <input class="form-control" id="edu_group" name="edu_group" placeholder="12"/>
                <small id="centersHelp" class="form-text text-muted" style="direction: rtl !important;">
                    کدها را به صورت تک و یا به صورت از-تا وارد کنید:
                    1
                    یا
                    <span style="direction: ltr !important;" class="d-inline-block"> 1-14</span>
                </small>
            </div>
            <div class="form-group">
                <label for="term">کد گروه درسی</label>
                <input class="form-control" id="crs_group" name="crs_group" placeholder="20"/>
                <small id="centersHelp" class="form-text text-muted" style="direction: rtl !important;">
                    کدها را به صورت تک و یا به صورت از-تا وارد کنید (ساختار عدد 2 رقمی باشد) :
                    01
                    یا
                    <span style="direction: ltr !important;" class="d-inline-block"> 01-21</span>
                </small>
            </div>
            <div class="form-group">
                <label for="term">کد درس</label>
                <input class="form-control" id="course" name="course" placeholder="028"/>
                <small id="centersHelp" class="form-text text-muted" style="direction: rtl !important;">
                    کدها را به صورت تک و یا به صورت از-تا وارد کنید (ساختار عدد 3 رقمی باشد):
                    028
                    یا
                    <span style="direction: ltr !important;" class="d-inline-block"> 028-030</span>
                </small>
            </div>

        </div>
        <hr/>
        <div class="form-group">
            <label for="key">کد امنیتی</label>
            <input class="form-control" id="key" name="key" placeholder="ex: t8fpLwWw"/>
        </div>

        <div class="form-group">
            <a class="mb-2 btn bg-info RegisterData text-white" data-action="enroll" data-url="enroll_1171.php">ثبت نام
                دانشپذریران</a>
            <a class="mb-2 btn bg-info RegisterData text-white" data-action="courses" data-url="courses_1248.php">ثبت دوره ها</a>
            <a class="mb-2 btn bg-success RegisterData text-white" data-action="teachers" data-url="teachers_1131.php">ثبت اطلاعات
                اساتید</a>
            <a class="mb-2 btn bg-success RegisterData text-white" data-action="students" data-url="students_1132.php">ثبت اطلاعات
                دانشپذیران</a>

        </div>
        <div class="modal fade" id="ex-modal" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title w-100" id="exampleModalLongTitle">

                            در حال ثبت اطلاعات، لطفا تا پایان عملیات این صفحه را نبندید</h5>

                    </div>
                    <div id="export" class="modal-body" style="height:300px;max-height: 300px;overflow-y: scroll">

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close-btn"
                                data-dismiss="modal" style="display: none">بستن
                        </button>
                    </div>
                </div>
            </div>

        </div>

    </div>
</div>


<script src="assets/js/jquery-3.5.1.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/scripts.js?v=1.2"></script>
</body>
</html>


