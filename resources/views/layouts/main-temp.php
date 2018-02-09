<!DOCTYPE html>
<!--
Template Name: Metronic - Responsive Admin Dashboard Template build with Twitter Bootstrap 3.2.0
Version: 3.1.3
Author: KeenThemes
Website: http://www.xiujie.cn/
Contact: support@xiujie.cn
Follow: www.twitter.com/keenthemes
Like: www.facebook.com/keenthemes
Purchase: http://themeforest.net/item/metronic-responsive-admin-dashboard-template/4021469?ref=keenthemes
License: You must have a valid license purchased only from themeforest(the above link) in order to legally use the theme for your project.
-->
<!--[if IE 8]>
<html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]>
<html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="zh-CN" class="no-js">
<!--<![endif]-->
<!-- BEGIN HEAD -->
<head>
    <meta charset="utf-8"/>
    <title>管理后台</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport"/>
    <meta content="" name="description"/>
    <meta content="" name="author"/>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/simple-line-icons/simple-line-icons.min.css"
          rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css"
          rel="stylesheet" type="text/css"/>
    <!-- END GLOBAL MANDATORY STYLES -->
    <!-- BEGIN PAGE LEVEL PLUGIN STYLES -->
    <link href="/bower_components/metronic/assets/global/plugins/gritter/css/jquery.gritter.css" rel="stylesheet"
          type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/bootstrap-daterangepicker/daterangepicker-bs3.css"
          rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/fullcalendar/fullcalendar/fullcalendar.css"
          rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/jqvmap.css" rel="stylesheet"
          type="text/css"/>
    <!-- END PAGE LEVEL PLUGIN STYLES -->
    <!-- BEGIN PAGE STYLES -->
    <link href="/bower_components/metronic/assets/admin/pages/css/tasks.css" rel="stylesheet" type="text/css"/>
    <!-- END PAGE STYLES -->
    <!-- BEGIN THEME STYLES -->
    <link href="/bower_components/metronic/assets/global/css/components.css" rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/global/css/plugins.css" rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/admin/layout/css/layout.css" rel="stylesheet" type="text/css"/>
    <link href="/bower_components/metronic/assets/admin/layout/css/themes/default.css" rel="stylesheet" type="text/css"
          id="style_color"/>
    <link href="/bower_components/metronic/assets/admin/layout/css/custom.css" rel="stylesheet" type="text/css"/>

    <link rel="stylesheet" href="/bower_components/dropload/dist/dropload.css">

    <link href="/assets/admin/css/main.css" rel="stylesheet" type="text/css"/>

    <!-- END THEME STYLES -->
    <link rel="shortcut icon" href="/favicon.ico"/>

    <!-- BEGIN JAVASCRIPTS(Load javascripts at bottom, this will reduce page load time) -->
    <!-- BEGIN CORE PLUGINS -->
    <!--[if lt IE 9]>
    <script src="/bower_components/metronic/assets/global/plugins/respond.min.js"></script>
    <script src="/bower_components/metronic/assets/global/plugins/excanvas.min.js"></script>
    <![endif]-->
    <script src="/bower_components/metronic/assets/global/plugins/jquery-1.11.0.min.js" type="text/javascript"></script>
    <script src="/bower_components/metronic/assets/global/plugins/jquery-migrate-1.2.1.min.js"
            type="text/javascript"></script>
</head>

<!-- END HEAD -->
<!-- BEGIN BODY -->
<!-- DOC: Apply "page-header-fixed-mobile" and "page-footer-fixed-mobile" class to body element to force fixed header or footer in mobile devices -->
<!-- DOC: Apply "page-sidebar-closed" class to the body and "page-sidebar-menu-closed" class to the sidebar menu element to hide the sidebar by default -->
<!-- DOC: Apply "page-sidebar-hide" class to the body to make the sidebar completely hidden on toggle -->
<!-- DOC: Apply "page-sidebar-closed-hide-logo" class to the body element to make the logo hidden on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-hide" class to body element to completely hide the sidebar on sidebar toggle -->
<!-- DOC: Apply "page-sidebar-fixed" class to have fixed sidebar -->
<!-- DOC: Apply "page-footer-fixed" class to the body element to have fixed footer -->
<!-- DOC: Apply "page-sidebar-reversed" class to put the sidebar on the right side -->
<!-- DOC: Apply "page-full-width" class to the body element to have full width page without the sidebar menu -->
<body class="page-header-fixed page-quick-sidebar-over-content">

<?php $this->insert('layouts/header-temp'); ?>
<!-- BEGIN CONTAINER -->
<div class="page-container">
    <?php $this->insert('layouts/sidebar-temp'); ?>
    <div class="page-content-wrapper">
        <div class="page-content">
            <?php $this->insert('layouts/page-bar-temp') ?>
            <!-- END PAGE HEADER-->
            <!-- BEGIN DASHBOARD STATS -->

            <?= $this->section('content'); ?>
        </div>
    </div>
    <a href="javascript:;" class="page-quick-sidebar-toggler"><i class="icon-close"></i></a>
    <?php $this->insert('layouts/quick-sidebar-swapper-temp'); ?>
</div>

<div class="modal fade" id="basic" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Modal Title</h4>
            </div>
            <div class="modal-body">
                Modal body goes here
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">Close</button>
                <button type="button" class="btn blue">Save changes</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>

<!-- END CONTAINER -->

<?php $this->insert('layouts/footer-temp'); ?>

<!-- IMPORTANT! Load jquery-ui-1.10.3.custom.min.js before bootstrap.min.js to fix bootstrap tooltip conflict with jquery ui tooltip -->
<script src="/bower_components/metronic/assets/global/plugins/jquery-ui/jquery-ui-1.10.3.custom.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/bootstrap/js/bootstrap.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery-slimscroll/jquery.slimscroll.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery.blockui.min.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery.cokie.min.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/uniform/jquery.uniform.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"
        type="text/javascript"></script>
<!-- END CORE PLUGINS -->
<!-- BEGIN PAGE LEVEL PLUGINS -->
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/jquery.vmap.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.russia.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.world.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.europe.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.germany.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/maps/jquery.vmap.usa.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jqvmap/jqvmap/data/jquery.vmap.sampledata.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/flot/jquery.flot.min.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/flot/jquery.flot.resize.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/flot/jquery.flot.categories.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery.pulsate.min.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/bootstrap-daterangepicker/moment.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/bootstrap-daterangepicker/daterangepicker.js"
        type="text/javascript"></script>
<!-- IMPORTANT! fullcalendar depends on jquery-ui-1.10.3.custom.min.js for drag & drop support -->
<script src="/bower_components/metronic/assets/global/plugins/fullcalendar/fullcalendar/fullcalendar.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery-easypiechart/jquery.easypiechart.min.js"
        type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/global/plugins/gritter/js/jquery.gritter.js"
        type="text/javascript"></script>
<!-- END PAGE LEVEL PLUGINS -->
<!-- BEGIN PAGE LEVEL SCRIPTS -->
<script src="/bower_components/metronic/assets/global/scripts/metronic.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/admin/layout/scripts/layout.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/admin/layout/scripts/quick-sidebar.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/admin/layout/scripts/demo.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/admin/pages/scripts/index.js" type="text/javascript"></script>
<script src="/bower_components/metronic/assets/admin/pages/scripts/tasks.js" type="text/javascript"></script>

<script type="text/javascript" src="/bower_components/plupload/js/moxie.js"></script>
<script type="text/javascript" src="/bower_components/plupload/js/plupload.dev.js"></script>
<!-- <script type="text/javascript" src="bower_components/plupload/js/plupload.full.min.js"></script> -->
<script type="text/javascript" src="/bower_components/plupload/js/i18n/zh_CN.js"></script>
<script type="text/javascript" src="/assets/admin/scripts/ui.js"></script>
<script type="text/javascript" src="/bower_components/qiniu/src/qiniu.js"></script>

<script type="text/javascript" src="/bower_components/underscore/underscore-min.js"></script>

<script src="/bower_components/dropload/dist/dropload.min.js"></script>

<script type="text/javascript" src="/assets/admin/scripts/main.js"></script>

<!-- END PAGE LEVEL SCRIPTS -->
<script>


    var _bug_message = "系统错误，请联系程序小哥";
    var _csrf_token = "<?= csrf_token() ?>";
    var getCsrfToken = function (val) {
        return _csrf_token;
    };
    var updateCsrfToken = function (val) {
        $('.csrf_token').val(val);
        _csrf_token = val;
    };
    jQuery(document).ready(function () {
        Metronic.init(); // init metronic core componets
        Layout.init(); // init layout
        QuickSidebar.init(); // init quick sidebar
//        Demo.init(); // init demo features
//        Index.init();
//        Index.initDashboardDaterange();
//        Index.initJQVMAP(); // init index page's custom scripts
//        Index.initCalendar(); // init index page's custom scripts
//        Index.initCharts(); // init index page's custom scripts
//        Index.initChat();
//        Index.initMiniCharts();
//        Index.initIntro();
        Tasks.initDashboardWidget();
        $('.scroller').slimScroll();
    });
</script>
<!-- END JAVASCRIPTS -->

</body>

<!-- END BODY -->
</html>