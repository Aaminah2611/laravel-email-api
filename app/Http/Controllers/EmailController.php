<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
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

        // Dispatch the SendEmailJob to process asynchronously
        SendEmailJob::dispatch($email);

        return response()->json([
            'message' => 'Email queued successfully',
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
