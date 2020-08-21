<?php 
use think\facade\Route;
Route::group('v01',function(){
    //登陆注册
    Route::get('token','v01.auther/token');
    Route::post('auther/login','v01.auther/login');
    Route::post('auther/register','v01.auther/register');
    //受资助
    Route::get('accept/index','Accept/index');//受资助列表
    Route::post('accept/subsidize','Accept/subsidize');//提交受资助
    //提供资助
    Route::get('provide/index','Provide/index');//提供资助
    Route::post('provide/subsidize','Provide/subsidize');//提供资助
    //匹配
    
});


?>