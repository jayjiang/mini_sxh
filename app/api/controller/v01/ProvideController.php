<?php
namespace app\api\controller\v01;

use app\api\controller\Base;

class ProvideController extends Base
{
    protected $middleware = [
        'app\api\middleware\Api' => ['except' => []],//
    ];
    
    public function index(){
        
    }
    
    public function subsidize(){
        
    }
}

