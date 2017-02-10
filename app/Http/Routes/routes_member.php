<?php

/**
 * 会员后台路由
 */


/**
 * 前台模板大厅
 */
Route::group(['prefix'=>'/','namespace'=>'Home'], function(){
    Route::get('t/{tempid}', 'HomeController@show');
    Route::resource('t', 'HomeController');
    Route::resource('o', 'OrderController');
});


/**
 * 用户登录
 */
Route::group(['prefix'=>'login'], function(){
    Route::get('/', 'LoginController@index');
    Route::post('dologin', 'LoginController@dologin');
    Route::get('logout', 'LoginController@logout');
});


/**
 * 用户房间
 */
Route::group(['prefix'=>'u','middleware' =>'MemberAuth','namespace'=>'Member'], function(){
    Route::get('product/s/{cate}', 'HomeController@index');
    Route::post('product/apply', 'HomeController@getApply');
    Route::post('product/{id}', 'HomeController@update');
    Route::post('product/link1/{id}', 'HomeController@setThumb');
    Route::post('product/link2/{id}', 'HomeController@setLink');
    Route::get('product/getshow/{id}', 'HomeController@setIsShow');
    Route::get('product/delete/{id}', 'HomeController@forceDelete');
    Route::resource('product', 'HomeController');
    Route::get('pro/preview/{pro_id}', 'HomeController@getPreview');
    Route::get('pro/preview/layers/{pro_id}', 'HomeController@getPrePro');
    Route::group(['prefix'=>'pro/{pro_id}'],function(){
        Route::post('layer/tolayer', 'LayerController@setLayer');
        Route::post('layer/toattr', 'LayerController@setAttr');
        Route::post('layer/totext', 'LayerController@setText');
        Route::get('layer/cancel/{layerid}', 'LayerController@delRedis');
        Route::get('layer/save/{layerid}', 'LayerController@saveRedisToDB');
        Route::resource('layer', 'LayerController');
    });
    Route::group(['prefix'=>'pro/{pro_id}/{layerid}'],function(){
        Route::get('prelayer', 'FrameController@getPreLayer');
        Route::get('keyvals', 'FrameController@getKeyVals');
    });
});
//渲染订单路由
Route::group(['prefix'=>'o','middleware' =>'MemberAuth','namespace'=>'Member'], function(){
    Route::get('pro/{pro_id}/create', 'OrderController@create');
    Route::resource('s/{cate}', 'OrderController@index');
    Route::resource('/', 'OrderController');
});
//用户账户路由
Route::group(['prefix'=>'myinfo','middleware' =>'MemberAuth','namespace'=>'Member'], function(){
    Route::get('wealbysign/{weal}', 'AccountController@getWealBySign');
    Route::get('wealbygold/{weal}', 'AccountController@getWealByGold');
    Route::get('wealbytip/{weal}', 'AccountController@getWealByTip');
    Route::resource('/', 'AccountController');
});