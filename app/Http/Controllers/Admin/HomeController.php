<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class HomeController extends Controller
{

    public function index()
    {
        $todoListJson = json_decode(file_get_contents("./todo_list.json"), true);
        return Inertia::render('Admin/Dashboard', compact('todoListJson'));
    }

    public function todolist(Request $request)
    {
        return file_put_contents("./todo_list.json", json_encode($request->all()), 1);

        return $request->all();
    }
}
