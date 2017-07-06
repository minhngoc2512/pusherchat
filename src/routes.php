<?php
/**
 * Created by PhpStorm.
 * User: minh
 * Date: 06/07/2017
 * Time: 09:26
 */


Route::post('message','chat\pusherchat\ChatController@sendNotification');


Route::get('chat','chat\pusherchat\ChatController@index');
Route::post('sendmessage','chat\pusherchat\ChatController@SendMessage');
Route::get('/FormReceive/{name}/{NameChannel}','chat\pusherchat\ChatController@getFormReceive');
Route::get('/FormSend/{name}/{NameChannel}','chat\pusherchat\ChatController@getFormsend');

