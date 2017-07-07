<?php

namespace Minh\PusherChat;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Minh\PusherChat\Event\SentMessage;
use Minh\PusherChat\Event\MemberOnline;
use Illuminate\Support\Facades\Auth;

use DB;

class ChatController extends Controller
{
    public $channel=null;
    public function __construct()
    {
        $this->middleware(['web','auth']);
    }
    public function index(){
        return view('ChatPusher.home');
    }
    public function sendNotification(Request $request)
    {


     //   }

        $NameChannel = $request->NameChannel;
        $user =  Auth::user();
        $user2 = $request->input('message');
        $CheckChannel= $request->CheckChannel;
        $data = DB::select("select * from channel where name like '%$NameChannel%'");

        if($data==null){
            $NameChannel = $NameChannel.'_'.$CheckChannel;
            $time = Carbon::now();
            DB::table('channel')->insertGetId(['name'=>$NameChannel,'created_at'=>$time]);

        }else{
           $NameChannel = $data[0]->name;
        }


        broadcast(new MemberOnline($user,$user2,$NameChannel,$CheckChannel))->toOthers();


        return $NameChannel;
        //
    }
    public function SendMessage(Request $request)
    {
        $message = $request->message;
        $name = $request->name;
        $user = Auth::user();
        $user_id= $user->id;
        $time= Carbon::now();

        DB::table('messages')->insertGetId(['user_id'=>$user_id,'message'=>$message,'created_at'=>$time]);
        broadcast(new SentMessage($user, $message, $name))->toOthers();
        return $message;
    }

    public function getFormReceive($name,$NameChannel)
    {

        $data = ' 
                <li style="float: left;width: 266px;" >
           <div class="clearfix" style="height: auto;float: left; width: 276px; clear: both;content:\'-\';display: block;position: fixed;bottom: 2px;;padding: 5px;margin-left: 10px ">
      
        <div class="panel panel-default " style="">
            <div class="panel-heading">'.$name.'  <button id="btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-success" > X </button> </div>
            <div class="panel-body" id="message-content-'.$NameChannel.'" >
                <ul style="overflow: scroll;max-height: 250px" id="list-message-'.$NameChannel.'">
                    
                </ul>
            </div>
            <div class="panel-footer" style="height: 50px">
             
                <input type="text" style="width: 70%;float: left;margin-right: 10px" id="text-message-'.$NameChannel.'"  class="form-control" placeholder="Enter message">
                <input type="button"  style="float: left" onclick="sendmessage_private(\''.$NameChannel.'\');"  value="send" class="btn btn-danger">

            </div>
        </div>
    </div>
    </li>
    
    <script>
    $(\'#btn-message-'.$NameChannel.'\').click(function() {
      $(\'#message-content-'.$NameChannel.'\').toggle();
    });
    
    </script>
    
    
    
    
    ';

        return $data;

    }

    public function getFormsend($name,$NameChannel){
        $data = '
        <li style="float: left;width: 266px;" >
           <div class="clearfix" style="height: auto;float: left; width: 276px; clear: both;content:\'-\';display: block;position: fixed;bottom: 2px;;padding: 5px;margin-left: 10px ">
      
        <div class="panel panel-default " style="">
            <div class="panel-heading">'.$name.' <button id="btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-success" > X</button> </div>
            <div class="panel-body" id="message-content-'.$NameChannel.'" >
                <ul style="overflow: scroll;max-height: 250px" id="list-message-'.$NameChannel.'">
                    
                </ul>
            </div>
            <div class="panel-footer" style="height: 50px">
             
                <input type="text" style="width: 70%;float: left;margin-right: 10px" id="text-message-'.$NameChannel.'"  class="form-control" placeholder="Enter message">
                <input type="button"  style="float: left" onclick="sendmessage_private(\''.$NameChannel.'\');"  value="send" class="btn btn-danger">

            </div>
        </div>
    </div>
    </li>
    <script>
    $(\'#btn-message-'.$NameChannel.'\').click(function() {
      $(\'#message-content-'.$NameChannel.'\').toggle();
    });
    
    </script>
    
    ';
        return $data;

    }



    //
}
