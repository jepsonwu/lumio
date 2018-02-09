<?php
/**
 * Created by IntelliJ IDEA.
 * User: apple
 * Date: 17/6/13
 * Time: 下午8:54
 */
use App\Components\BootstrapHelper\Widgets\Form\FieldWidget;

/**
 * @var $widget FieldWidget
 * @var $type   string
 * @var $this   \League\Plates\Template\Template
 */

?>
<div class="form-control-block form-file-upload qiniu-upload-wrapper" id="qiniu-upload-wrapper1">
    <div id="container">
        <a class="btn btn-default " id="pickfiles" href="#">
            <i class="glyphicon glyphicon-plus"></i>
            <span>选择文件</span>
        </a>
    </div>
    <div class="<?= $widget->formField->fieldName ?>">
        <input type="hidden" name="<?= $widget->formField->fieldName ?>"
               value="<?= $widget->formField->fieldValue ?>"
               class="data-qiniu-images data-<?= $widget->formField->fieldName ?>"/>
        <div class="clearfix">
            <?php
            if ($widget->formField->fieldValue) {
                foreach (explode("\r\n", $widget->formField->fieldValue) as $pic) {
                    ?>
                    <div class="pull-left">
                        <a href="<?= $pic ?>" target="_blank">
                            <img class="img-preview"
                                 src="<?= $pic ?>" style="max-height: 100px">
                        </a>
                    </div>
                <?php }
            }
            ?>
        </div>
    </div>
    <div style="display:none" id="success" class="success">
        <div class="alert alert-success">
            队列全部文件处理完毕
        </div>
    </div>
    <div class="">
        <table class="table table-striped table-hover text-left" style="display:none">
            <thead>
            <tr>
                <th class="col-md-4">文件名</th>
                <th class="col-md-2">尺寸</th>
                <th class="col-md-6">详情</th>
            </tr>
            </thead>
            <tbody id="fsUploadProgress">
            </tbody>
        </table>
    </div>
</div>

<script>
    $(function () {
        var domain = '<?= config('qiniu.in_qiniu.url') ?>';
        var $wrapper = $('#qiniu-upload-wrapper1');
        var uploader = Qiniu.uploader({
            runtimes: 'html5,flash,html4',
            browse_button: 'pickfiles',
            container: 'container',
            drop_element: 'container',
            max_file_size: '1000mb',
            flash_swf_url: '/bower_components/plupload/js/Moxie.swf',
            dragdrop: true,
            chunk_size: '4mb',
            multi_selection: !(moxie.core.utils.Env.OS.toLowerCase() === "ios"),
//            uptoken_url: '/qiniu-token',
            uptoken_func: function () {
                var token = null;
                $.ajax({
                    url: '/qiniu-token',
                    async: false,
                    success: function (data) {
                        console.log(data);
                        token = data.data.token;
                    },
                    error: function () {
                        console.log('fail to get token');
                        console.log(arguments);
                    }
                });
                return token;
            },
            domain: domain,
            get_new_uptoken: false,
            // downtoken_url: '/downtoken',
            // unique_names: true,
            // save_key: true,
            // x_vars: {
            //     'id': '1234',
            //     'time': function(up, file) {
            //         var time = (new Date()).getTime();
            //         // do something with 'time'
            //         return time;
            //     },
            // },
            auto_start: true,
            log_level: 5,
            init: {
                'BeforeChunkUpload': function (up, file) {
                    console.log("before chunk upload:", file.name);
                },
                'FilesAdded': function (up, files) {
                    $('table').show();
                    $('#success').hide();
                    plupload.each(files, function (file) {
                        var progress = new FileProgress(file, 'fsUploadProgress');
                        progress.setStatus("等待...");
                        progress.bindUploadCancel(up);
                    });
                },
                'BeforeUpload': function (up, file) {
                    console.log("this is a beforeupload function from init");
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                    if (up.runtime === 'html5' && chunk_size) {
                        progress.setChunkProgess(chunk_size);
                    }
                },
                'UploadProgress': function (up, file) {
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    var chunk_size = plupload.parseSize(this.getOption('chunk_size'));
                    progress.setProgress(file.percent + "%", file.speed, chunk_size);
                },
                'UploadComplete': function () {
                    $('#success').show();
                },
                'FileUploaded': function (up, file, info) {
                    var progress = new FileProgress(file, 'fsUploadProgress');
                    progress.setComplete(up, info);
                    console.log(arguments);

                    // save to input hidden
                    var images = $('.data-qiniu-images', $wrapper).val();
                    var imagesArr = images.split("\r\n");
                    imagesArr.push(domain + "/" + $.parseJSON(info).key);
                    $('.data-qiniu-images', $wrapper).val(imagesArr.join("\r\n"));
                },
                'Error': function (up, err, errTip) {
                    $('table').show();
                    var progress = new FileProgress(err.file, 'fsUploadProgress');
                    progress.setError();
                    progress.setStatus(errTip);
                }
                // ,
                // 'Key': function(up, file) {
                //     var key = "";
                //     // do something with key
                //     return key
                // }
            }
        });
        //uploader.init();
        uploader.bind('BeforeUpload', function () {
            console.log("hello man, i am going to upload a file");
        });
        uploader.bind('FileUploaded', function () {
            console.log('hello man,a file is uploaded');
        });
    });
</script>


