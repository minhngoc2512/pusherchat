<?php

namespace Minh\PusherChat;

use App\Http\Controllers\Controller;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Minh\PusherChat\Event\SentMessage;
use Minh\PusherChat\Event\MemberOnline;
use Illuminate\Support\Facades\Auth;
use App\User;
use DB;

class ChatController extends Controller
{
    public $ChannelRefresh =[];
    public $channel=null;
    public function __construct()
    {
        $this->middleware(['web','auth']);
    }
    public function index(){
        return view('ChatPusher.home');
    }
    public function setStatusMessage($name,$channel){
        DB::table('messages')->where('to_user',$name)->where('channel',$channel)->update(['status'=>'readed']);
        return 'readed: ok';
    }

    function sendNotificationGroup(Request $request){
        $nameChannel = $request->nameChannel;
        return $nameChannel;

    }

    public function sendNotification(Request $request)
    {
        $NameChannel = $request->NameChannel;
        $user =  Auth::user();
        $user2 = $request->input('message');

        $user2_id_tmp = User::where('name',$user2)->first();
        $user2_id = $user2_id_tmp->id;
        $CheckChannel= $request->CheckChannel;
        $data = DB::select("select * from channel where name like '%$NameChannel%'");

        if($data==null){
            $NameChannel = $NameChannel.'_'.$CheckChannel;
            $time = Carbon::now();
            DB::table('channel')->insertGetId(['name'=>$NameChannel,'user_create'=>$user->id,'member_id'=>$user->id.','.$user2_id,'type'=>'double','status'=>1,'created_at'=>$time]);

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
        $user_id= $user->name;
        $to_user = $request->to_user;
        $time= Carbon::now();

        DB::table('messages')->insertGetId(['message'=>$message,'from_user'=>$user_id,'to_user'=>$to_user,'channel'=>$name,'status'=>'waiting','created_at'=>$time]);
        broadcast(new SentMessage($user, $message, $name))->toOthers();
        return $message;
    }
    public function getMessages($channel){
        $user_name = Auth::user()->name;
        $message2 ='';
        $message = DB::table('messages')->where('channel',$channel)->orderBy('id','desc')->limit(20)->get();
       if(!isset($message[0])){
           return;
       }else{
           for($i=count($message)-1;$i>=0;$i--) {
               if ($user_name == $message[$i]->from_user) {

                   $message2 .= ' <li class="alert alert-danger small"> ' .  $message[$i]->message . ' </li> ';
               } else {

                   $message2 .= ' <li class="alert alert-info small"> ' .  $message[$i]->message . ' </li> ';
               }
           }

          return $message2;
       }
    }

    public function getFormGroup($NameChannel){

        $userRead = Auth::user()->name;
        $data = ' 
                <li style="float: left;width: 266px;"  id="li_close-'.$NameChannel.'">
           <div class="clearfix" style="height: auto;float: left; width: 276px; clear: both;content:\'-\';display: block;position: fixed;bottom: 2px;;padding: 5px;margin-left: 10px ">
      
        <div class="panel panel-default " style="">
            <div class="panel-heading">'.$NameChannel.'  <button id="btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-success" > _ </button>  <button id="X_btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-danger" > X </button> </div>
            <div class="panel-body" id="message-content-'.$NameChannel.'" >
                <ul style="overflow: scroll;max-height: 250px ;     list-style-type: none;" id="list-message-'.$NameChannel.'">
                    
                </ul>
            </div>
            <div class="panel-footer" style="height: 50px">
             
                <input type="text" onclick="EventRead(\''.$userRead.'\',\''.$NameChannel.'\');" style="width: 70%;float: left;margin-right: 10px" id="text-message-'.$NameChannel.'"  class="form-control" placeholder="Enter message">
                <input type="button"  style="float: left" onclick="sendmessage_private(\''.$NameChannel.'\',\''.$NameChannel.'\');"  value="send" class="btn btn-danger">

            </div>
        </div>
    </div>
    </li>
    
    <script>
    $(\'#btn-message-'.$NameChannel.'\').click(function() {
      $(\'#message-content-'.$NameChannel.'\').toggle();
    });
    $(\'#X_btn-message-'.$NameChannel.'\').click(function() {
      $(\'#li_close-'.$NameChannel.'\').remove();
      
    });
    
    
    </script>
    ';
        return $data;


    }

    public function getFormReceive($name,$NameChannel)
    {
        $statusChannel =0;
        if(Session::has('channelChat')) {
            $ListChannel= Session::get('channelChat');
            foreach ($ListChannel as $data){
                if($NameChannel==$data['channel']){
                    $statusChannel=1;
                    break;
                }
            }
            if($statusChannel==0){
                $this->ChannelRefresh = Session::get('channelChat');

                array_push($this->ChannelRefresh, ['channel' => $NameChannel, 'user' => $name]);
                Session::put('channelChat', $this->ChannelRefresh);

            }
        }else{
            array_push($this->ChannelRefresh, ['channel' => $NameChannel, 'user' => $name]);
            Session::put('channelChat', $this->ChannelRefresh);
        }

        $userRead = Auth::user()->name;
        $data = ' 
                <li style="float: left;width: 266px;"  id="li_close-'.$NameChannel.'">
           <div class="clearfix" style="height: auto;float: left; width: 276px; clear: both;content:\'-\';display: block;position: fixed;bottom: 2px;;padding: 5px;margin-left: 10px ">
      
        <div class="panel panel-default " style="">
            <div class="panel-heading">'.$name.'  <button id="btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-success" > _ </button>  <button id="X_btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-danger" > X </button> </div>
            <div class="panel-body" id="message-content-'.$NameChannel.'" >
                <ul style="overflow: scroll;max-height: 250px ;     list-style-type: none;" id="list-message-'.$NameChannel.'">
                    
                </ul>
            </div>
            <div class="panel-footer" style="height: 50px">
             
                <input type="text" onclick="EventRead(\''.$userRead.'\',\''.$NameChannel.'\');" style="width: 70%;float: left;margin-right: 10px" id="text-message-'.$NameChannel.'"  class="form-control" placeholder="Enter message">
                <input type="button"  style="float: left" onclick="sendmessage_private(\''.$name.'\',\''.$NameChannel.'\');"  value="send" class="btn btn-danger">

            </div>
        </div>
    </div>
    </li>
    
    <script>
    $(\'#btn-message-'.$NameChannel.'\').click(function() {
      $(\'#message-content-'.$NameChannel.'\').toggle();
    });
    $(\'#X_btn-message-'.$NameChannel.'\').click(function() {
      $(\'#li_close-'.$NameChannel.'\').remove();
    });
    
    
    </script>
    ';

        return $data."+++".$this->getMessages($NameChannel);

    }

    public function getFormsend($name,$NameChannel){
        $statusChannel =0;
        if(Session::has('channelChat')) {
            $ListChannel= Session::get('channelChat');
            foreach ($ListChannel as $data){
                if($NameChannel==$data['channel']){
                    $statusChannel=1;
                    break;
                }
            }
            if($statusChannel==0){
                $this->ChannelRefresh = Session::get('channelChat');

                array_push($this->ChannelRefresh, ['channel' => $NameChannel, 'user' => $name]);
                Session::put('channelChat', $this->ChannelRefresh);

            }
        }else{
            array_push($this->ChannelRefresh, ['channel' => $NameChannel, 'user' => $name]);
            Session::put('channelChat', $this->ChannelRefresh);
        }
        $userRead = Auth::user()->name;
        $data = '
        <li style="float: left;width: 266px;" id="li_close-'.$NameChannel.'">
           <div class="clearfix" style="height: auto;float: left; width: 276px; clear: both;content:\'-\';display: block;position: fixed;bottom: 2px;;padding: 5px;margin-left: 10px " >
      
        <div class="panel panel-default " style="">
            <div class="panel-heading">'.$name.' <button id="btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-success" > _</button>  <button id="X_btn-message-'.$NameChannel.'" style="float: right; margin-top: -5px;" class="btn btn-danger" > X </button></div>
            <div class="panel-body" id="message-content-'.$NameChannel.'" >
                <ul style="overflow: scroll;max-height: 250px;list-style-type: none;" id="list-message-'.$NameChannel.'">
                    
                </ul>
            </div>
            <div class="panel-footer" style="height: 50px">
             
                <input type="text" style="width: 70%;float: left;margin-right: 10px"  onclick="EventRead(\''.$userRead.'\',\''.$NameChannel.'\');" id="text-message-'.$NameChannel.'"  class="form-control" placeholder="Enter message">
                <input type="button"  style="float: left" onclick="sendmessage_private(\''.$name.'\',\''.$NameChannel.'\');"  value="send" class="btn btn-danger">

            </div>
        </div>
    </div>
    </li>
    <script>
    $(\'#btn-message-'.$NameChannel.'\').click(function() {
      $(\'#message-content-'.$NameChannel.'\').toggle();
    });
     $(\'#X_btn-message-'.$NameChannel.'\').click(function() {
      $(\'#li_close-'.$NameChannel.'\').remove();
       for(i=0;i<ListFormStatus.length;i++){
                            if(ListFormStatus[i]===\''.$NameChannel.'\'){
                                ListFormStatus[i]=" ";
                            
                                break;
                            }
                    }
    });
    
    </script>
    
    ';
        return $data."+++".$this->getMessages($NameChannel);

    }



    //
}

