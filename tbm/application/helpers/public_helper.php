<?php 
//获取指定GET参数
function require_get($str) {
    if (isset($_GET[$str]) && $_GET[$str] != null) {
        return htmlspecialchars($_GET[$str]);
    } else {
        exit(json_encode(array('code' => '0', 'msg' => 'miss ' . $str)));
    }
}

//获取指定POST参数
function require_post($str) {
    if (isset($_POST[$str]) && $_POST[$str] != null) {
        return htmlspecialchars($_POST[$str]);
    } else {
        exit(json_encode(array('code' => '0', 'msg' => 'miss ' . $str)));
    }
}

//页面打印输出方法
function pr($var) {
    echo '<pre>';
    print_r($var);
    echo '</pre>';
}
?>