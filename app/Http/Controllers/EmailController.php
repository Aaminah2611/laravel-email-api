<?php

namespace App\Http\Controllers;

use App\Jobs\SendEmailJob;
use Illuminate\Http\Request;
use App\Models\Email;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Response;


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

    public function listEmails(Request $request)
{
    $perPage = $request->query('per_page', 10); // default 10 per page
    $emails = Email::orderBy('created_at', 'desc')->paginate($perPage);

    return response()->json([
        'data' => $emails->items(),
        'current_page' => $emails->currentPage(),
        'per_page' => $emails->perPage(),
        'total' => $emails->total(),
        'last_page' => $emails->lastPage(),
    ]);
}

public function destroy($id)
{
    $email = Email::findOrFail($id);
    $email->delete(); // This triggers a soft delete
    return response()->json(['message' => 'Email soft-deleted successfully.']);
}


public function trashed()
{
    $trashed = Email::onlyTrashed()->orderBy('deleted_at', 'desc')->paginate(10);

    return response()->json([
        'data' => $trashed->items(),
        'current_page' => $trashed->currentPage(),
        'per_page' => $trashed->perPage(),
        'total' => $trashed->total(),
        'last_page' => $trashed->lastPage(),
    ]);
}

public function restore($id)
{
    $email = Email::onlyTrashed()->findOrFail($id);
    $email->restore();

    return response()->json([
        'message' => 'Email restored successfully.',
        'email' => $email
    ], Response::HTTP_OK);
}

}
