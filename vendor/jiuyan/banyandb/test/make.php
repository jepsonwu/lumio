<?php
$data = file_get_contents('../api/php/banyan_api.php');
// echo $data;
// exit;
$reg = "/class ([\w]*) [\s\S]*?\n\}/i";
preg_match_all($reg, $data, $match);
// 
//var_dump($match);exit;
foreach ($match[0] as $key => $classCt) {
    file_put_contents('../src/'.$match[1][$key].'.php',"<?php \r\n\r\nnamespace BanyanDB;\r\n\r\n".$classCt);
}
