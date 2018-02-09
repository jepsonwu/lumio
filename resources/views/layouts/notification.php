<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/12
 * Time: 下午9:49
 */


?>
<a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown"
   data-close-others="true">
    <i class="icon-bell"></i>
    <span class="badge badge-default">
					<?= \UserNotification::count('ticket'); ?> </span>
</a>
<ul class="dropdown-menu">
    <li>
        <p>
            你有 <?= \UserNotification::count('ticket'); ?> 条新消息
        </p>
    </li>
    <li>
        <ul class="dropdown-menu-list scroller" style="height: 250px;">
            <?php
            if ($list = \UserNotification::list('ticket')) {
                foreach ($list as $one) {
                    ?>
                    <li>
                        <a href="/admin/ticket/<?= $one->out_id ?>">
                                        <span class="label label-sm label-icon label-success">
                                        <i class="fa fa-plus"></i>
                                        </span>
                            <?= $one->title ?> <span class="time">
                                        <?= $one->created_at ?> </span>
                        </a>
                    </li>
                <?php }
            } ?>
        </ul>
    </li>
    <li class="external">
        <a href="#">
            显示所有代办消息 <i class="m-icon-swapright"></i>
        </a>
    </li>
</ul>

<script>
    
</script>