<?php
namespace app\api\controller\v01;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use app\Request;
use app\api\controller\Base;
use app\api\service\JwtAuth;
use app\common\model\Users;
use think\facade\Validate;
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
      dump($token);
    }
    
    /**
     * @desc 登陆
     */
    public function login(){
         $phone = request()->post('phone',null);
         $password = request()->post('password',null);
         if(empty($phone)|| empty($password)){
             return $this->result([],1,'帐号、密码不能为空');
         }
         if(!is_phone($phone)){
             return $this->result([],1,'电话不正确');
         }
         $userModel = Users::where('phone',$phone)->where('status',1)->field('id,name,avatar,password,remember_token')->find();
         if(empty($userModel)){
             return $this->result([],1,'帐号不存在或未通过审核');
         }
         $passwrod_hash = encrypt_password($password,$userModel['remember_token']);
         if($passwrod_hash != $userModel['password']){
             return $this->result([],1,'密码不正确');
         }
         $jwtAuth = JwtAuth::getInstance();
         $token = $jwtAuth->setUid($userModel['id'])->encode()->getToken();
         //更新信息
         Users::where('id', $userModel['id'])
         ->update(['last_login_time' => time(), 'last_login_ip' => Request::ip()]);
         $data = [
             'token'     => $token,
             'user'      => $userModel->toArray()
         ];
         return $this->result($data,0);
         
         
    }
    
    /**
     * @desc 注册
     * @api {post} /auther/register 01、会员注册
     * @apiParam (请求参数：) {string}     		phone 登录电话
     * @apiParam (请求参数：) {string}     		email 登录邮箱
     * @apiParam (请求参数：) {string}     		pwd 登录密码
     * @apiParam (请求参数：) {string}     		repassword 确认登录密码
     * @return \think\Response
     */
    public function register(){
        //validate();
        $phone = request()->post('phone');
        $email = request()->post('email');
        $pwd   = request()->post('pwd');
        $repassword = request()->post('repassword');
        $rule = [
            'email'      => 'require|email',
            'phone'      => 'require|mobile',
            'pwd'        => 'require|max:25',
            'repassword' => 'require|max:25|confirm:pwd'
        ];
        $validate = Validate::rule($rule);
        $data = [
            'phone'  => $phone,
            'email'  => $email,
            'pwd'    => $pwd,
            'repassword'  => $repassword,
        ];
        if(!$validate->check($data)){
            $arr_err = $validate->getError();
            $error = $arr_err;
            if(is_array($arr_err)){
                $error = implode(',',$arr_err);
            }
            return $this->result([],1,$error);
        }
        
        if(!Users::register($data)){
            return $this->result([],1,'注册失败');
        }else{
            return $this->result([],0);
        }
    }
    
    
}
