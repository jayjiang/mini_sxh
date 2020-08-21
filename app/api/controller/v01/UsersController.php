<?php
namespace app\api\controller\v01;

use app\api\controller\Base;

class UsersController extends Base
{
    protected $middleware = [
        'app\api\middleware\Api' => ['except' => []],//
    ];
    
    /**
     * @desc 个人中心
     * 
     */
    public function centre(){
        
    }
}

