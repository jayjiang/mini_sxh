<?php
namespace app\index\controller;

use think\facade\Session;
use think\facade\View;
use app\common\model\UsersAccept;

class AcceptController extends Base
{
    protected $middleware = [
        'app\index\middleware\AutherMiddleware'  =>  ['except' => ['login']],
    ];
    
    public function index(){
        if (!Session::has('user.id')) {
            return redirect('login');
        }
        UsersAccept::where('user_id',)->select()->toArray();
        return  View::fetch();
    }
    
    /**
     *
     */
    public function created(){
        
        if(request()->isPost()){
            
        }
        return VIew::fetch();
    }
}

