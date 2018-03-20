<!-- BEGIN HEADER -->
<div class="page-header navbar navbar-fixed-top">
    <!-- BEGIN HEADER INNER -->
    <div class="page-header-inner">
        <!-- BEGIN LOGO -->
        <div class="page-logo">
            <a href="/admin">
                <h4 style="font-size: 22px;color: #d64635;">管理后台</h4>
            </a>
            <div class="menu-toggler sidebar-toggler hide">
                <!-- DOC: Remove the above "hide" to enable the sidebar toggler button on header -->
            </div>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN RESPONSIVE MENU TOGGLER -->
        <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse"
           data-target=".navbar-collapse">
        </a>
        <!-- END RESPONSIVE MENU TOGGLER -->
        <!-- BEGIN TOP NAVIGATION MENU -->
        <div class="top-menu">
            <ul class="nav navbar-nav pull-right">
                <!-- BEGIN NOTIFICATION DROPDOWN -->
<!--                <li class="dropdown dropdown-extended dropdown-notification" id="header_notification_bar">-->
<!--                    --><?php //$this->insert('layouts/notification') ?>
<!--                    --><?php //$this->insert('layouts/notice-and-reload') ?>
<!--                </li>-->
                <!-- END NOTIFICATION DROPDOWN -->
                <!-- BEGIN USER LOGIN DROPDOWN -->
                <li class="dropdown dropdown-user">
                    <?php $user=Auth::user();if($user){?>
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
                       data-close-others="true">

                        <span class="username username-hide-on-mobile"><?= $user->name ?> </span>
                        <i class="fa fa-angle-down"></i>
                    </a>
                    <?php }?>

                    <!--                    <ul class="dropdown-menu">-->
<!--                        <li class="divider">-->
<!--                        </li>-->
<!--                        <li>-->
<!--                            <a href="/super">-->
<!--                                <i class="icon-home"></i> 渠道首页 </a>-->
<!--                        </li>-->
<!--                    </ul>-->
                </li>
                <!-- END USER LOGIN DROPDOWN -->
                <!-- BEGIN QUICK SIDEBAR TOGGLER -->
<!--                <li class="dropdown dropdown-quick-sidebar-toggler">-->
<!--                    <a href="javascript:;" class="dropdown-toggle">-->
<!--                        <i class="icon-logout"></i>-->
<!--                    </a>-->
<!--                </li>-->
                <!-- END QUICK SIDEBAR TOGGLER -->
            </ul>
        </div>
        <!-- END TOP NAVIGATION MENU -->
    </div>
    <!-- END HEADER INNER -->
</div>
<!-- END HEADER -->
<div class="clearfix">
</div>
