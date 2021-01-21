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
        $jssdk = new Jssdk();
        $package = $jssdk->getSignPackage($url);

        return json($package);
    }
}
