<?php
namespace App\Api\ApiImg;

use Redis;

class ApiBase
{
    /**
     * API公用文件
     */

    /**
     * 用户接口密匙
     */
    public static function getApiKey()
    {
//        return env('API_KEY', 'isduc3IKO3TTN5JUf8uLcC3ZxIrszuO9');
        return 'isduc3IKO3TTN5JUf8uLcC3ZxIrszuO9';
    }

    /**
     * 用户接口地址
     */
    public static function getApiCurl()
    {
//        return env('API_CURL', 'img_api.jiugewenhua.com');
        return 'img_api.jiugewenhua.com';
    }

    /**
     * 对象转为数组
     */
    public static function objToArr($obj)
    {
        return json_decode(json_encode($obj),true);
    }

    /**
     * 获取用户信息缓存
     */
    public static function getRedis($redisKey)
    {
        return Redis::get($redisKey);
    }
}