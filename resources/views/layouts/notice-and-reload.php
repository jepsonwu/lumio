<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/7/21
 * Time: 下午12:35
 */

?>

<button id="button">
    通知
</button>

<audio id="chatAudio">
    <source src="/assets/admin/6809.mp3" type="audio/mpeg">
</audio>

<script>
    $(function () {

        if (window.Notification) {

//            var intv = setInterval(function () {
//                // notification
//                fetchNotify();
//            }, 10000);
//            clearInterval(intv);

            function fetchNotify() {
                $.ajax({
                    url: '/admin/user-notification/fetch',
                    success: function (data) {
                        if (!data.succ) {
                            alert(_bug_message);
                            return;
                        }

                        if (!data.data.notification) {
                            return;
                        }

                        sendNotify(data.data.notification.title + "-" + data.data.notification.sub_title, data.data.notification.message, data.data.notification.out_id);
                    }
                });
            }

            function sendNotify(title, body, id) {

                if (Notification.permission == "granted") {
                    popNotice(title, body, id);
                } else if (Notification.permission != "denied") {
                    Notification.requestPermission(function (permission) {
                        popNotice();
                    });
                }
            }

            function popNotice(title, body, id) {

                if (Notification.permission == "granted") {
                    var notification = new Notification(title, {
                        body: body,
                        tag: Math.random(),
                        renotify: false
//                        sound: '/assets/admin/6809.wav'
                    });

                    notification.onclick = function () {
                        // put model readed
                        window.open("/admin/ticket/" + id);
                        $.ajax({
                            url: '/admin/user-notification/fetch',
                            data: {
                                id: id
                            },
                            success: function (data) {
                                if (!data.succ) {
                                    alert(data.message);
                                    return;
                                }
                                notification.close();
                            }
                        });
                    };

                    $('#chatAudio')[0].play();
                }
            }

            var button = document.getElementById('button'), text = document.getElementById('text');
            button.onclick = function () {
//                sendNotify("新的工单", '可以加你为好友吗？', 0);
                fetchNotify();
            };


        } else {
            alert('浏览器不支持Notification');
        }
    });
</script>
