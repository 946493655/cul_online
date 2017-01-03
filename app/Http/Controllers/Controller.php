<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;
use Illuminate\Support\Facades\Session as Session;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests;

    protected $userid;
    protected $model;
    protected $redisTime = 5;       //session在redis中缓存时长，单位分钟

    public function __construct()
    {
        define("DOMAIN",strtoupper(getenv('DOMAIN')));
        define("PUB",strtoupper(getenv('PUB')));
        $this->setSessionInRedis();     //同步缓存中session
    }

    /**
     * 判断session、缓存
     */
    public function setSessionInRedis()
    {
        $redisTime = 5;     //redis中缓存时间，单位分钟
        //假如session中有，缓存中没有，则同步为有
        if ($userInfo=Session::has('user') && !\Redis::get('cul_session')) {
            $userInfo['cookie'] = $_COOKIE;
            \Redis::setex('cul_session',$this->redisTime*60,serialize($userInfo));
        }
        //假如session中没有，缓存中有，则同步为有
        if (!Session::has('user') && $cul_session=\Redis::get('cul_session')) {
            $cul_session = unserialize($cul_session);
            $cul_session['cookie'] = $_COOKIE;
//            dd($cul_session,$_COOKIE);
            if ($cul_session['cookie']['laravel_session']!=$_COOKIE['laravel_session']) {
                echo 'no';exit;
            }
            Session::put('user',$cul_session);
        }
        //更新session中的cookie值
        if ($cul_session = \Session::get('user')) {
            $cul_session['cookie'] = $_COOKIE;
            \Redis::setex('cul_session',$this->redisTime*60,serialize($cul_session));
            \Session::put('user',$cul_session);
        }
    }

    /**
     * 接口分页处理
     */
    public function getPageList($datas,$prefix_url,$limit,$pageCurr=1)
    {
        $currentPage = $pageCurr;                               //当前页
        $lastPage = ($pageCurr - 1) ? ($pageCurr - 1) : 1;      //上一页
        $total = count($datas);                                 //总记录数
        //上一页路由
        if ($pageCurr<=1) {
            $previousPageUrl = $prefix_url;
        } else {
            $previousPageUrl = $prefix_url.'?page='.($pageCurr-1);
        }
        //下一页路由
        if (count($datas) <= $limit) {
            $nextPageUrl = $prefix_url;
        } elseif ($pageCurr * $limit >= count($datas)) {
            $nextPageUrl = $prefix_url.'?page='.$pageCurr;
        } else {
            $nextPageUrl = $prefix_url.'?page='.($pageCurr+1);
        }
        return array(
            'currentPage'   =>  $currentPage,
            'lastPage'      =>  $lastPage,
            'total'         =>  $total,
            'limit'         =>  $limit,
            'previousPageUrl'   =>  $previousPageUrl,
            'nextPageUrl'   =>  $nextPageUrl,
        );
    }
}
