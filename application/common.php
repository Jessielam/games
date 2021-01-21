<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

use think\exception\HttpResponseException;
use think\Response;

// 接口返回格式
function ajaxReturn($data, $code = 0, $msg = '', $type = 'json', array $header = [])
{
    $result = [
        'code' => $code,
        'msg'  => $msg,
        'data' => $data,
        'time' => $_SERVER['REQUEST_TIME'],
    ];
    $type     = $type ?: 'json';
    $response = Response::create($result, $type)->header($header);
    throw new HttpResponseException($response);
}