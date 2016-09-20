<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
class Hook_Controller extends Controller
{
    public function index(Request $request){
        $token = $request->input()
        var_dump($token);
    }
}
