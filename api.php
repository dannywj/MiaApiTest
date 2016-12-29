<?php
/**
 * API Test Controller
 * Created by DannyWang
 * wangjue@mia.com
 * 2016/05
 */
require_once(__DIR__ . '/rsa.php');

$data = $_POST['data'];
$api_name = $data['api_name'];

if ($api_name == 'call') { // 接口调用
    $api_path = trim($data['api']);
    $params = trim($data['params']);
    $version = trim($data['version']);
    $version = str_replace('.', '_', $version);
    $subParams = json_decode($params, true);
    // 防止php json_decode 以后自动将长数字转换成科学记数法
    foreach ($subParams as $key => $val) {
        if (!is_array($val) && !is_string($val) && !empty($val)) {
            $subParams[$key] = number_format($val, 0, '', '');
        }
    }
    $base_params = trim($data['base_params']);
    if (!isJson($base_params)) {
        exit('base_params is invalid json');
    }
    $baseParams = json_decode($base_params, true);
    $re = callApi($api_path, $baseParams, $subParams, $version);
    echo $re;
} elseif ($api_name == 'decode_params') {// 参数解码
    $params = trim($data['code']);
    $params = str_replace('-', '+', $params);
    $params = str_replace('_', '/', $params);
    $rsa = new Rsa();
    $decryptParams = $rsa->sectionPrivDecrypt($params);
    echo($decryptParams);
} else {
    exit('invalid api name');
}


///////////////////////  functions  /////////////////////////
function callApi($api_path, $baseParams, $data, $version = 'android_4_0') {
    $rsa = new Rsa();
    $cryptRes = $rsa->sectionPublicEncrypt(json_encode($data));
    $params = array(
        "timestamp" => time(),
        "version" => $version,
        "params" => $cryptRes,
    );
    $params = array_merge($params, $baseParams);
    ksort($params);
    $strParams = "";

    $appKeyConf = array(
        "ios_test_id" => "MIYABABY_IOS",
        "android_test_id" => "MIYABABY_ANDROID",
    );
    foreach ($params as $key => $value) {
        $strParams .= $key . $value;
    }
    $secret = $appKeyConf[$params['app_id']];
    $sign = array("sign" => md5($strParams . $secret));
    $params = array_merge($params, $sign);

    $res = curl_post("http://api.miyabaobei.com/" . $api_path, $params);
    header('content-type:text/html;charset=utf8');
    return $res;
}

function curl_post($url, $post = NULL, $options = array()) {
    $defaults = array(
        CURLOPT_POST => 1,
        CURLOPT_HEADER => 0,
        CURLOPT_URL => $url,
        CURLOPT_FRESH_CONNECT => 1,
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_FORBID_REUSE => 1,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_POSTFIELDS => $post
    );
    $ch = curl_init();
    curl_setopt_array($ch, ($options + $defaults));
    if (!$result = curl_exec($ch)) {
        $http_info= curl_getinfo($ch);
        var_dump('CURL ERROR');
        var_dump($http_info);
        var_dump(curl_error($ch), curl_errno($ch));
        trigger_error(curl_error($ch));
    }
    curl_close($ch);
    return $result;
}

function isJson($string) {
    json_decode($string);
    return (json_last_error() == JSON_ERROR_NONE);
}