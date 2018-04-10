<?php

?>

<div class="modal fade" id="verify_fail" tabindex="-1" role="" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title" id="verify_fail_title"></h4>
            </div>
            <div class="modal-body">
                <input type="text" id="verify_fail_reason" name="verify_fail_reason" class="form-control input-medium"
                       value=""/>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn default" data-dismiss="modal">关闭</button>
                <button type="button" id="verify_fail_btn" class="btn blue">保存</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>


<script>
    function verifyFailClick(route, btn) {
        $('#verify_fail_title').text('审核失败');
        $('#verify_fail_btn').data('id', $(btn).parents('tr:first').data('id'));
        $('#verify_fail_btn').data('route', route);
    }

    $(function () {

        $('#verify_fail_btn').click(function () {
            if (!confirm("确认审核失败？")) {
                return;
            }

            var verify_reason = $('#verify_fail_reason').val();
            var id = $(this).data('id');
            var route = $(this).data('route');
            $.ajax({
                url: route + "/verify-fail",
                method: 'post',
                data: {
                    reason: verify_reason,
                    id: id
                },
                success: function (data) {
                    if (data.succ) {
                        // $('tr[data-id="' + id + '"]:first').find('.column-verify_remark').text(verify_reason);
                        window.location.reload()
                        $('#verify_fail').modal('hide')
                    } else {
                        alert("操作失败：" + data.msg)
                    }
                    return;
                },
                complete: function (xhr) {
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.data._token) {
                            updateCsrfToken(xhr.responseJSON.data._xtoken);
                        }
                    }
                }
            });
        });

        $('.list-btn-ajax-verify-pass').click(function () {
            if (!confirm("确认审核成功？")) {
                return;
            }

            var id = $(this).parents('tr:first').data('id');
            var url = window.location.origin + window.location.pathname;
            if (url.substr(-1, 1) == '/') {
                url = url.substr(0, url.length - 1);
            }
            if (url.substr(0))
                url = url + "/verify-pass";

            $.ajax({
                url: url,
                method: 'post',
                data: {
                    id: id
                },
                success: function (data) {
                    if (data.succ) {
                        window.location.reload()
                    } else {
                        alert("操作失败：" + data.msg);
                    }

                    return;
                },
                complete: function (xhr) {
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.data._token) {
                            updateCsrfToken(xhr.responseJSON.data._xtoken);
                        }
                    }
                }
            });
        });

    });
</script>