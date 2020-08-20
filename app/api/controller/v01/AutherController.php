<?php
namespace app\api\controller\v01;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\api\controller\Base;
use app\api\service\JwtAuth;
/**
 * Description of AutherController
 *
 * @author Administrator
 */
class AutherController  extends Base{
    
    protected $middleware = [
        'app\api\middleware\Api' => ['except' => ['token','login','register']],//
    ];
    
    //put your code here
    public function token(){
     // $uid= 
      $jwtAuth =  JwtAuth::getInstance();
      $token = $jwtAuth->encode()->getToken();
    }
    
    /**
     * @desc 登陆
     */
    public function login(){
         $username = request()->post('username',null);
         $password = request()->post('password',null);
         if(empty($username)|| empty($password)){
             return $this->result([],1,'帐号、密码不能为空');
         }
         
         
    }
    
    public function register(){
        
    }
}
