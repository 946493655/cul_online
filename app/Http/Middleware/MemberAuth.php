<?php
/**
 * Created by PhpStorm.
 * User: liubin
 * Date: 15/4/20
 * Time: 22:46
 */

namespace App\Http\Middleware;

use Closure;
use Session;
use Redis;

class MemberAuth
{
    public function handle($request, Closure $next)
    {
        //判断会员后台有无此登录的用户
        if(!Session::get('user') && !Redis::get('cul_user_session')){
            return redirect('/login');
        }
        return $next($request);
    }
}