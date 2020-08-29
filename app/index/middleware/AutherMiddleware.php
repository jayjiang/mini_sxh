<?php
namespace app\index\middleware;


use think\Request;
use think\facade\Session;

class AutherMiddleware
{
    public function handle(Request $request, \Closure $next){
        //var_dump($request);exit;
        if (!Session::has('user.id')) {
            $url= url('user/login');
           
            return redirect($url);
        }
        return $next($request);
    }
}

