<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmailController extends Controller
{
    public function email()
    {
        $title = 'Email';
        return view('users.Email', compact('title'));
    }
}
