<?php

namespace App\Http\Controllers\User;

use App\Events\NotificationEvent;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {


        $todoListJson = [];// json_decode(file_get_contents("./user_todo_list.json"), true);

        return Inertia::render('User/Dashboard', compact('todoListJson'));
    }

    public function websocket()
    {
        $notification = Notification::find(1);
        broadcast(new NotificationEvent($notification));
        return true;
    }

    public function todolist(Request $request)
    {
        return [];
        return file_put_contents("./user_todo_list.json", json_encode($request->all()), 1);

        return $request->all();
    }
}
