$(function () {

    var _bug_message = '处理失败，请联系写程序那小伙';

    //global setup
    $.ajaxSetup({
        data: {
            '_token': getCsrfToken()
        },
        error: function () {
            console.log(arguments);
            alert(_bug_message);
        }
    });

    // list remove
    $('.list-btn-ajax-remove').click(function () {
        // send remote for list
        var $btn = $(this);
        var id = 0;
        var $tr = $btn.parents('tr:first');
        if ($tr.length) {
            id = $tr.data('id');
        }
        if (confirm('确认删除[#' + id + ']')) {
            var url = $btn.data('url');
        }

        if (!url) {
            if (!id) {
                console.log('没有找到资源id，需要使用 list view 生成的table，在tr 上带了 data-id 才可以');
                alert(_bug_message);
                return;
            }
            url = window.location.origin + window.location.pathname;
            if (url.substr(-1, 1) == '/') {
                url = url.substr(0, url.length - 1);
            }
            if (url.substr(0))
            url = url + "/" + id;
        }

        $.ajax({
            url: url,
            method: 'delete',
            success: function (data) {
                if (data.succ) {
                    alert('删除成功');
                    $btn.parents('tr:first').remove();
                    return;
                }
                alert('删除失败【' + data.message + '】');
            },
            complete: function(xhr){
                console.log(xhr.responseJSON);
                if(xhr.responseJSON){
                    console.log(xhr.responseJSON.data._token);
                    if(xhr.responseJSON.data._token){
                        updateCsrfToken(xhr.responseJSON.data._token);
                    }
                }
            }
        });
    });
});