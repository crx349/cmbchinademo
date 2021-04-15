<?php
$config = [
    'use_sandbox' => true, // 是否使用 招商测试系统

    'branch_no' => 'xxxx',  // 商户分行号，4位数字
    'mch_id'    => 'xxxx', // 商户号，6位数字
    'mer_key'   => '1234567890123456', // 秘钥16位，包含大小写字母 数字

    // 招商的公钥，建议每天凌晨2:15发起查询招行公钥请求更新公钥。
    'cmb_pub_key' => 'xxxx',

    'op_pwd'    => 'xxxxxx', // 操作员登录密码。
    'sign_type' => 'SHA-256', // 签名算法,固定为“SHA-256”
    'limit_pay' => 'A', // 允许支付的卡类型,默认对支付卡种不做限制，储蓄卡和信用卡均可支付   A:储蓄卡支付，即禁止信用卡支付

    'notify_url' => 'http://cmb.admin.com/cmb_tz.php', // 支付成功的回调

    'sign_notify_url' => 'http://cmb.admin.com/cmb_tz.php', // 成功签约结果通知地址
    'sign_return_url' => 'http://cmb.admin.com', // 成功签约结果通知地址

    'return_url' => 'http://cmb.admin.com/cmb_ok.php', // 如果是h5支付，可以设置该值，返回到指定页面
];

//一网通获取公钥地址(测试)
$QueryKeyAPI_test = 'http://mobiletest.cmburl.cn/CmbBank_B2B/UI/NetPay/DoBusiness.ashx';
//一网通下单地址(测试)
$OneCardPayAPI_test = 'http://121.15.180.66:801/netpayment/BaseHttp.dll?MB_EUserPay';
//$OneCardPayAPI_test ='http://paytest.cmburl.cn:801/netpayment/BaseHttp.dll?MB_EUserPay';
//查询订单
$DoBusiness_test = 'http://121.15.180.66:801/netpayment_directlink_nosession/BaseHttp.dll?QuerySingleOrder';

$nowTime = time();

echo '1.请求公钥：<p>';
//数据结构
$pub_get_data = [

    'version'  => '1.0',
    'charset'  => 'UTF-8',
    'signType' => $config['sign_type'],
    'reqData'  => [
        'dateTime'   => date('YmdHis', $nowTime),
        'txCode'     => 'FBPK',
        'branchNo'   => $config['branch_no'],
        'merchantNo' => $config['mch_id'],
    ],

];

$baResult = make_sign($config['mer_key'],$pub_get_data['reqData']);
$pub_get_data['sign']=$baResult;
$formParams['jsonRequestData']=json_encode($pub_get_data, JSON_UNESCAPED_UNICODE);
echo '请求公钥报文:';
var_dump($formParams);
echo '<p>--------------------------------<p>';

echo '公钥:';
$ret = go_curl($QueryKeyAPI_test,'POST',$formParams);

var_dump($ret);
echo '<p>----------------请求公钥结束----------------<p>';


echo '2.下单：<p>';

$tradeNo = time() . rand(1000, 9999);
//$tradeNo = '66666' . rand(10000, 99999);
$timeExpire = "30";
saveLog($tradeNo,'order_id');//保存订单id
//数据结构
$pay_get_data = [

    'version'  => '1.0',
    'charset'  => 'UTF-8',
    'signType' => $config['sign_type'],
    'reqData'  => [
        'dateTime'         => date('YmdHis', $nowTime),
        'branchNo'         => $config['branch_no'],
        'merchantNo'       => $config['mch_id'],
        'date'             => date('Ymd',  $nowTime),
        'orderNo'          => $tradeNo,
        'amount'           => "0.01", // 固定两位小数，最大11位整数
        'expireTimeSpan'   => $timeExpire,
        'payNoticeUrl'     => $config['notify_url'],
        //'payNoticePara'    =>  '',
        'returnUrl'        => $config['return_url'],
        //'clientIP'         =>  '',
        //'cardType'         => $config['limit_pay'], // A:储蓄卡支付，即禁止信用卡支付
        // 'agrNo'            => '',
        // 'merchantSerialNo' =>  '',
        //'userID'           => '',
        // 'mobile'           =>  '',
        // 'lon'              => '',
        // 'lat'              => '',
        // 'riskLevel'        => '',
        'signNoticeUrl'    => $config['sign_notify_url'],
        // 'signNoticePara'   =>  '',
    ],

];


$baResult1 = make_sign($config['mer_key'],$pay_get_data['reqData']);
//$pay_get_data['sign']=$baResult1;
$pay_get_data['sign']=$baResult1;
$formParams1['charset'] = 'UTF-8';
$formParams1['jsonRequestData']=json_encode($pay_get_data);
echo '请求支付报文:';
var_dump($formParams1);
echo '<p>--------------------------------<p>';
?> 2.1测试订单提交：<p>
    订单号：<?php echo $tradeNo?>
<form action="<?php echo $OneCardPayAPI_test?>" method="post" />
<input type="hidden" name="jsonRequestData" value='<?php echo $formParams1['jsonRequestData']?>' />
<input type="hidden" name="charset" value='UTF-8' />
<input type="submit" value="提交订单">
</form>
<?php
//echo '返回结果:';
//$ret = go_curl($OneCardPayAPI_test,'POST',$formParams1);


//saveLog($ret);
//var_dump($ret);
echo '<p>----------------请求下单结束----------------<p>';
//$tradeNo = '6666620122';
echo '3.查询订单：<p>订单号:'.$tradeNo.'<p>';

//$toDay = new \DateTime();
$reqData = [
    'dateTime' => date('YmdHis', $nowTime),
    'branchNo' => $config['branch_no'],
    'merchantNo' => $config['mch_id'],
    'date' => date('Ymd',  time()),
    'type' => 'B',
   // 'bankSerialNo' => '',
    'orderNo' => $tradeNo
];
$baResult2 = make_sign($config['mer_key'],$reqData);


//var_dump($reqData);
$jsonRequestData = [
    'version'  => '1.0',
    'charset'  => 'UTF-8',
    'signType' => $config['sign_type'],
    'sign' => $baResult2,
    'reqData' => $reqData
];

$formParams2['charset'] = 'UTF-8';
$formParams2['jsonRequestData']=json_encode($jsonRequestData);
echo '请求报文:<p>';
var_dump($formParams2);
?> <p>3.查询订单：<p>
    订单号：<?php echo $tradeNo?>
    <form action="<?php echo $DoBusiness_test?>" method="post" />
    <input type="hidden" name="jsonRequestData" value='<?php echo $formParams2['jsonRequestData']?>' />
    <input type="hidden" name="charset" value='UTF-8' />
    <input type="submit" value="查询订单">
    </form>
<?php
/*
echo '<p>返回结果:';
$ret = go_curl($DoBusiness_test,'POST',$formParams2);
var_dump($ret);*/
//exit;



//======================================================函数============================================================
//签名
function make_sign($mer_key,$str){

    $reqData = arraySort($str);
    $signStr = createLinkString($reqData);

    $signStr .= '&'.$mer_key;
//SHA-256签名
    $baSrc = mb_convert_encoding($signStr,"UTF-8");
    $baResult = hash('sha256', $baSrc);
//转为16进制字符串（可选）
//$sign = bin2hex($baResult);

    return $baResult;
}



//排序
function arraySort(array $param)
{
    ksort($param);
    reset($param);

    return $param;
}
//转换url
function createLinkString($para)
{


    reset($para);
    $arg = '';
    foreach ($para as $key => $val) {
        if (is_array($val)) {
            continue;
        }

        $arg .= $key . '=' . urldecode($val) . '&';
    }
    //去掉最后一个&字符
    $arg && $arg = substr($arg, 0, -1);

    //如果存在转义字符，那么去掉转义
    if (get_magic_quotes_gpc()) {
        $arg = stripslashes($arg);
    }

    return $arg;
}


//提交
function go_curl($url, $type, $data = false, &$err_msg = null, $timeout = 20, $cert_info = array())
{
    $type = strtoupper($type);
    if ($type == 'GET' && is_array($data)) {
        $data = http_build_query($data);
    }
    $option = array();
    if ( $type == 'POST' ) {
        $option[CURLOPT_POST] = 1;
    }
    if ($data) {
        if ($type == 'POST') {
            $option[CURLOPT_POSTFIELDS] = $data;
        } elseif ($type == 'GET') {
            $url = strpos($url, '?') !== false ? $url.'&'.$data :  $url.'?'.$data;
        }
    }
    $option[CURLOPT_URL]            = $url;
    $option[CURLOPT_FOLLOWLOCATION] = TRUE;
    $option[CURLOPT_MAXREDIRS]      = 4;
    $option[CURLOPT_RETURNTRANSFER] = TRUE;
    $option[CURLOPT_TIMEOUT]        = $timeout;
    //设置证书信息
    if(!empty($cert_info) && !empty($cert_info['cert_file'])) {
        $option[CURLOPT_SSLCERT]       = $cert_info['cert_file'];
        $option[CURLOPT_SSLCERTPASSWD] = $cert_info['cert_pass'];
        $option[CURLOPT_SSLCERTTYPE]   = $cert_info['cert_type'];
    }
    //设置CA
    if(!empty($cert_info['ca_file'])) {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 1;
        $option[CURLOPT_CAINFO] = $cert_info['ca_file'];
    } else {
        // 对认证证书来源的检查，0表示阻止对证书的合法性的检查。1需要设置CURLOPT_CAINFO
        $option[CURLOPT_SSL_VERIFYPEER] = 0;
    }
    $ch = curl_init();
    curl_setopt_array($ch, $option);
    $response = curl_exec($ch);
    $curl_no  = curl_errno($ch);
    $curl_err = curl_error($ch);
    curl_close($ch);
    // error_log
    if($curl_no > 0) {
        if($err_msg !== null) {
            $err_msg = '('.$curl_no.')'.$curl_err;
        }
    }
    return $response;
}


function saveLog($data,$str='test')
{
    $years = date('Y-m');
    //设置路径目录信息
    //$url = './log/' . $years . '/' . date('Ymd') . 'txt';
    $url = './'.$str.'_' . date('Ymd') . '.txt';
    $dir_name = dirname($url);
    //目录不存在就创建
    if (!file_exists($dir_name)) {
        //iconv防止中文名乱码
        $res = mkdir(iconv("UTF-8", "GBK", $dir_name), 0777, true);
    }
    $fp = fopen($url, "a");//打开文件资源通道 不存在则自动创建
    fwrite($fp, date("Y-m-d H:i:s") . var_export($data, true) . "\r\n");//写入文件
    fclose($fp);//关闭资源通道
}

function cutstr_html($string, $sublen){

    $string = strip_tags($string);

    $string = trim($string);

    $string = ereg_replace("\t","",$string);

    $string = ereg_replace("\r\n","",$string);

    $string = ereg_replace("\r","",$string);

    $string = ereg_replace("\n","",$string);

    $string = ereg_replace(" ","",$string);

    return trim($string);

}