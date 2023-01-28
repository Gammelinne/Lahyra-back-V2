<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mail as MailClass;

class MailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);
        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
        ];
        
        //send mail
        Mail::to($request->email)->send(new MailClass($data));
    }
}
