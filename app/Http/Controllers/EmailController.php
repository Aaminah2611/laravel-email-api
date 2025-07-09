<?php

namespace App\Http\Controllers;

use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Models\Email;
use App\Http\Controllers\Controller; 
use Illuminate\Support\Facades\Log;

class EmailController extends Controller
{
    public function send(Request $request)
    {
        $request->validate([
            'to' => 'required|email',
            'subject' => 'required|string',
            'body' => 'required|string',
        ]);

        // Log the validated request data
        Log::info('Email request data:', $request->only(['to', 'subject', 'body']));

        // Send the email (not really sending in local env)
        Mail::to($request->input('to'))
            ->send(new SendEmail($request->input('subject'), $request->input('body')));

        // Save to database with correct column name
        Email::create([
            'to' => $request->input('to'),  // ✅ correct
            'subject' => $request->input('subject'),
            'body' => $request->input('body'),
            'status' => 'sent',
        ]);

        return response()->json(['message' => 'Email sent successfully']);
    }
}
