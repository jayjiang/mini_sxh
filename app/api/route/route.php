<?php 
use think\facade\Route;
Route::group('api',function(){
    //echo "FFFFFFFFFFFFFFFFFFFF";
    Route::group('v01.auther',function(){
        Route::get('token','auther/token');
    });
});

?>