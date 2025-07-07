<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Email;
use App\Http\Controllers\Controller; 

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        Mail::to($request->input('to'))
            ->send(new SendEmail($request->input('subject'), $request->input('body')));

        Email::create([
            'recipient' => $request->input('to'),
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'status' => 'sent',
        ]);

        return response()->json(['message' => 'Email sent successfully']);
    }
}
