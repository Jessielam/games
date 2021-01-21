<?php

namespace app\api\controller;

use think\Container;
use Wechat\Jssdk;

class Wechat extends Container
{
    public function index()
    {
        $jssdk = new Jssdk();
        $package = $jssdk->getSignPackage();

        return json($package);
    }
}
