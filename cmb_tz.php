<?php

$res = file_get_contents('php://input');

if(!empty($res)){
    saveLog($res,'tz');
}


function saveLog($data,$type='a')
{
    $years = date('Y-m');
    //设置路径目录信息
    //$url = './log/' . $years . '/' . date('Ymd') . 'txt';
    $url = './' .$type.'_'. date('Ymd') . '.txt';
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