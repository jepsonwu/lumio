<?php
/** @var $this \League\Plates\Template\Template */

$this->layout('layouts/login-temp');
?>
<style>
    .login {
        position: relative;
        top: 250px;
        left: 350px;
    }
</style>
<div class="login">
    <form class="form-horizontal">
        <div class="form-group">
            <label for="user_name" class="col-sm-2 control-label">用户名</label>
            <div class="col-sm-10">
                <input type="text" class="form-control input-medium" id="user_name">
            </div>
        </div>
        <div class="form-group">
            <label for="password" class="col-sm-2 control-label">密码</label>
            <div class="col-sm-10">
                <input type="password" class="form-control input-medium" id="password">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="button" id="login-btn" class="btn btn-default btn-lg">登录</button>
            </div>
        </div>
    </form>
</div>
<script>
    $(function () {
        $('#login-btn').click(function () {
            var user_name = $('#user_name').val();
            var password = $('#password').val();
            $.ajax({
                url: '/admin/auth/login',
                method: 'post',
                data: {
                    user_name: user_name,
                    password: password
                },
                success: function (data) {
                    if (data.succ) {
                        //window.location.href = data.data.redirect_url;
                    } else {
                        alert(data.msg)
                    }
                    return;
                },
                complete: function (xhr) {
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.data._token) {
                            updateCsrfToken(xhr.responseJSON.data._token);
                        }
                    }
                }
            });
        });
    })
</script>
