<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Message;
use Illuminate\Http\Request;
use Pusher\Pusher;
use DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $users = User::where('id', '!=', auth()->user()->id)->get();
        $users = DB::select('select users.id, users.name, users.avatar, users.email, count(is_read) as unread from users LEFT JOIN messages ON users.id = messages.from and is_read = 0 and messages.to = '.auth()->user()->id.' where users.id != '.auth()->user()->id.' group by users.id, users.name, users.avatar, users.email');

        return view('home', ['users' => $users]);
    } 

    /**
     * Gets message for receiver id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getMessage($id)
    {
        $my_id = auth()->user()->id;

        Message::where(['from' => $id, 'to' => $my_id])->update(['is_read' => 1]);
        $messages = Message::where(function ($query) use ($id, $my_id) {
            $query->where('from', $my_id)->where('to', $id);
        })->orWhere(function ($query) use ($id, $my_id)
        {
            $query->where('from', $id)->where('to', $my_id);
        })
        ->get();
        
        return view('messages.index', ['messages' => $messages]);
    }

    /**
     * Sends message
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function sendMessage()
    {
        $from    = auth()->user()->id;
        $to      = request('receiver_id');
        $message = request('message');

        $data          = new Message();
        $data->from    = $from;
        $data->to      = $to;
        $data->message = $message;
        $data->is_read = 0;
        $data->save();

        $options = array(
            'cluster' => 'eu',
            'useTLS' => true
        );

        $pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            $options
        );

        $data = ['from' => $from, 'to' => $to];
        $pusher->trigger('my-channel', 'my-event', $data);
    }
}
