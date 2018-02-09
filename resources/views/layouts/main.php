<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="/bower_components/tether/dist/css/tether.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="/admin-dist/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="/admin-dist/css/dashboard.css" rel="stylesheet">

    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]>
    <script src="/admin-dist/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="/admin-dist/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <script src="/bower_components/jquery/dist/jquery.js"></script>
    <script src="/bower_components/underscore/underscore.js"></script>
</head>

<div class="container">
    <?= $this->section('content') ?>
</div>
<script src="/bower_components/tether/dist/js/tether.min.js"></script>
<script src="/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script src="/bower_components/highcharts/highcharts.js"></script>
<script src="/bower_components/highcharts/themes/grid.js"></script>
<script src="/bower_components/highcharts/modules/exporting.js"></script>
<!--<script src="/bower_components/highcharts/highstock.js"></script>-->
<!--<script src="/bower_components/highcharts/modules/map.js"></script>-->
<!--<script src="/bower_components/highcharts/highmaps.js"></script>-->
<!-- Just to make our placeholder images work. Don't actually copy the next line! -->
<script src="/admin-dist/js/holder.min.js"></script>
<!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
<script src="/admin-dist/js/ie10-viewport-bug-workaround.js"></script>

<link href="/bower_components/chosen/chosen.css" rel="stylesheet">
<script src="/bower_components/chosen/chosen.jquery.js"></script>

<script>
    $().ready(function(){
        $('.sel-chosen').chosen();
    });
</script>

</body>
</html>
