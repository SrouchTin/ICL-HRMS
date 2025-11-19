<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HRController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $branch = $user->branch;
        return view('hr.dashboard',compact('user','branch'));
    }
}
