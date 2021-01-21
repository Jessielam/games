<?php

namespace app\api\controller;

use think\Container;
use think\Request;
use Wechat\Jssdk;

class Wechat extends Container
{
    public function index(Request $request)
    {
        $url = $request->param('url');
        if(!$url) {
            // 报错
            ajaxReturn([], 1, 'url参数错误');
        } else {
            $jssdk = new Jssdk();
            $package = $jssdk->getSignPackage($url);
        }

        return ajaxReturn($package, 0, '获取成功');
    }
}
