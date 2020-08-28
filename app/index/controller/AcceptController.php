<?php
namespace app\index\controller;

use think\facade\View;

class AcceptController extends Base
{
    public function index(){
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

