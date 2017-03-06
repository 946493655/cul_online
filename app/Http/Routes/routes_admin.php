<?php

/**
 * 系统后台登录
 */
Route::group(['prefix'=>'admin','namespace'=>'Admin'], function(){
    Route::get('login', 'LoginController@index');
    Route::post('dologin', 'LoginController@dologin');
    Route::get('logout', 'LoginController@logout');
});

/**
 * 系统后台路由
 */
Route::group(['prefix'=>'admin','middleware' =>'AdminAuth','namespace'=>'Admin'], function(){
    //模板路由
    Route::get('/', 'TempProController@index');
    Route::get('temp', 'TempProController@index');
    Route::get('temp/clear', 'TempProController@clearTable');
    Route::post('temp/{id}', 'TempProController@update');
    Route::post('temp/link1/{id}', 'TempProController@setThumb');
    Route::post('temp/link2/{id}', 'TempProController@setLink');
    Route::post('temp/getshow/{id}', 'TempProController@setIsShow');
    Route::post('temp/delete/{id}', 'TempProController@forceDelete');
    Route::post('temp/bg/{id}', 'TempProController@setTempBg');
    Route::resource('temp', 'TempProController');
    Route::group(['prefix'=>'t/{tempid}'], function(){
        Route::get('layer/{layerid}', 'TLayerController@show');
        Route::post('layer/tolayer', 'TLayerController@setLayer');
        Route::post('layer/toattr', 'TLayerController@setAttr');
        Route::post('layer/totext', 'TLayerController@setText');
        Route::post('layer/toimg/{layerid}', 'TLayerController@setImg');
        Route::get('layer/cancel/{layerid}', 'TLayerController@delRedis');
        Route::get('layer/save/{layerid}', 'TLayerController@saveRedisToDB');
        Route::get('{layerid}/layer/setshow/{isshow}', 'TLayerController@setIsShow');
        Route::get('{layerid}/layer/delete', 'TLayerController@setDelete');
        Route::get('{layerid}/layer', 'TLayerController@index');
        Route::resource('layer', 'TLayerController');
    });
    Route::get('temp/preview/{id}', 'TempProController@getPreview');
    Route::get('temp/preview/layers/{id}', 'TempProController@getPreTemp');
    Route::group(['prefix'=>'t/{tempid}/{layerid}'], function(){
        Route::post('frame/toattr', 'TFrameController@selAttr');
        Route::post('frame/setval', 'TFrameController@setKeyVal');
        Route::get('frame/cancel', 'TFrameController@delRedis');
        Route::get('frame/save', 'TFrameController@saveRedisToDB');
        Route::post('frame/delete', 'TFrameController@delete');
        Route::resource('frame', 'TFrameController');
        Route::get('prelayer', 'TFrameController@getPreLayer');
        Route::get('keyvals', 'TFrameController@getKeyVals');
    });
    //用户产品路由
    Route::post('product/{id}', 'ProductController@update');
    Route::post('product/thumb/{pro_id}', 'ProductController@setThumb');
    Route::post('product/link/{pro_id}', 'ProductController@setLink');
    Route::post('product/getshow/{pro_id}', 'ProductController@setShow');
    Route::post('product/bg/{pro_id}', 'ProductController@setProBg');
    Route::resource('product', 'ProductController');
    Route::get('pro/preview/{id}', 'ProductController@getPreview');
    Route::get('pro/preview/layers/{id}', 'ProductController@getPrePro');
    Route::group(['prefix'=>'pro/{pro_id}'], function(){
        Route::get('layer/{layerid}', 'ProLayerController@show');
        Route::post('layer/tolayer', 'ProLayerController@setLayer');
        Route::post('layer/toattr', 'ProLayerController@setAttr');
        Route::post('layer/totext', 'ProLayerController@setText');
        Route::post('layer/toimg/{layerid}', 'ProLayerController@setImg');
        Route::get('{layerid}/layer/setshow/{isshow}', 'ProLayerController@setIsShow');
        Route::resource('layer', 'ProLayerController');
    });
    Route::group(['prefix'=>'pro/{pro_id}/{layerid}'], function(){
        Route::get('prelayer', 'ProFrameController@getPreLayer');
        Route::get('keyvals', 'ProFrameController@getKeyVals');
        Route::post('frame/toattr', 'ProFrameController@selAttr');
        Route::post('frame/setval', 'ProFrameController@setKeyVal');
        Route::resource('frame', 'ProFrameController');
    });
});