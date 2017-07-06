<?php

namespace Chat\PusherChat;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;


class ChatServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if(!is_dir(resource_path('/views/ChatPusher'))){
            mkdir(resource_path('/views/ChatPusher'));
            copy(__DIR__.'/home.blade.php.tmp',resource_path('/views/ChatPusher/home.blade.php'));
        }else if(!file_exists(resource_path('/views/ChatPusher/home.blade.php'))){
            copy(__DIR__.'/home.blade.php.tmp',resource_path('/views/ChatPusher/home.blade.php'));
        }

        $this->loadRoutesFrom(__DIR__.'/routes.php');
        $this->loadMigrationsFrom(__DIR__.'/migrations');
     //   $this->loadMigrationsFrom(__DIR__.'/migrations/2017_07_06_121536_create_channel_table.php');

        $data_channel= file_get_contents(base_path('routes/channels.php'));
        if(preg_match('/memberOnline/',$data_channel)){

        }else{
            file_put_contents(base_path('routes/channels.php'),file_get_contents(__DIR__.'/channels.php.tmp',FILE_APPEND));
        }


    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //include __DIR__.'/routes.php';
        $this->app->make('Chat\PusherChat\ChatController');
        //
    }
}
