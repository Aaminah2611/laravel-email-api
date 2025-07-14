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

    Log::info('Send email called with data', [
        'to' => $request->input('to'),
        'subject' => $request->input('subject'),
    ]);

    // Create email record with status 'pending'
    $email = Email::create([
        'to' => $request->input('to'),
        'subject' => $request->input('subject'),
        'body' => $request->input('body'),
        'status' => 'pending',
    ]);

    try {
        // Attempt to send the email
        Mail::to($email->to)->send(new SendEmail($email->subject, $email->body, $email->id));

        // Update status to 'sent' and set sent_at timestamp
        $email->status = 'sent';
        $email->sent_at = now();
        $email->save();

        Log::info('Email sent via Mail facade', ['email_id' => $email->id]);

    } catch (\Exception $e) {
        // Sending failed, update status to 'failed' and log error
        $email->status = 'failed';
        $email->save();

        Log::error('Failed to send email', ['error' => $e->getMessage(), 'email_id' => $email->id]);

        return response()->json(['error' => 'Failed to send email'], 500);
    }

    return response()->json([
        'message' => 'Email sent successfully',
        'email_id' => $email->id,
        'status' => $email->status,
    ]);
}


    public function checkStatus($id)
    {
        $email = Email::findOrFail($id);

        return response()->json([
            'email_id' => $email->id,
            'to' => $email->to,
            'subject' => $email->subject,
            'status' => $email->status,
            'sent_at' => $email->sent_at,
        ]);
    }
}
