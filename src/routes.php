<?php
/**
 * Created by PhpStorm.
 * User: minh
 * Date: 06/07/2017
 * Time: 09:26
 */


Route::post('message','minh\pusherchat\ChatController@sendNotification');


Route::get('chat','minh\pusherchat\ChatController@index');
Route::post('sendmessage','minh\pusherchat\ChatController@SendMessage');
Route::get('/FormReceive/{name}/{NameChannel}','minh\pusherchat\ChatController@getFormReceive');
Route::get('/FormSend/{name}/{NameChannel}','minh\pusherchat\ChatController@getFormsend');

