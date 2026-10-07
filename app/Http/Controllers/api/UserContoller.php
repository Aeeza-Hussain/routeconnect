<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
class UserContoller extends Controller
{
   Public function index(){
    $users = User::select ('name')->find(3);
    return response()->json([
        'status' => true,
        'users' => $users
    ]);
   }
}
