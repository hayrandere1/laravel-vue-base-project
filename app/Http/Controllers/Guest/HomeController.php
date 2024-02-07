<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use App\Models\GuestDashboard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use SebastianBergmann\CodeCoverage\Report\Html\Dashboard;

class HomeController extends Controller
{
    public function index()
    {

        return redirect()->route('user.home');
        $dashboard = GuestDashboard::whereNull('company_id')->first()->toArray();
        $dashboard['content'] = json_decode($dashboard['content'], true);

        return Inertia::render('Guest/Dashboard', compact('dashboard'));
    }
}
