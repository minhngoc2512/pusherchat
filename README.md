package make by minhngoc.
Email: minhngoc2512@yahoo.com
Install and use package PUSHERCHAT:
<ol>
    <li>Run command artisan: "php artisan make:auth"</li>
    <li>Create a account on website <a href="http://pusher.com" >Pusher</a></li>
    <li>Insert:PUSHER_APP_ID,PUSHER_APP_KEY,PUSHER_APP_SECRET,BROADCAST_DRIVER=pusher in your server Pusher.</li>
    <li>In file config/app.php uncomment  App\Providers\BroadcastServiceProvider::class,</li>
    <li> Add service provider " Minh\PusherChat\ChatServiceProvider::class," in file config/app.php on array "provider"</li>
    <li> You may define a meta tag in your application's head HTML element: <meta name="csrf-token" content="{{ csrf_token() }}"> </li>
    <li> Run command artisan : "php artisan migrate"</li>
    <li> Include on blade template before tag </body>: " @include('ChatPusher.home');"</li>
    <li> Run command artisan : "php artisan serve" </li>
</ol>

