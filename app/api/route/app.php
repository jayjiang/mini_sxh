<?php 
use think\facade\Route;
Route::group('v01',function(){
    Route::get('token','v01.auther/token');
    Route::post('auther/login','v01.auther/login');
    Route::post('auther/register','v01.auther/register');
});


?>