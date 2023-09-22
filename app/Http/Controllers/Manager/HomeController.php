<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $todoListJson = json_decode(file_get_contents("./manager_todo_list.json"), true);

        return Inertia::render('Manager/Dashboard',compact('todoListJson'));
    }

    public function todolist(Request $request)
    {
        return file_put_contents("./manager_todo_list.json", json_encode($request->all()), 1);

        return $request->all();
    }
}
