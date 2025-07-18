<?php

namespace App\Http\Controllers;


use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;


class EmailTemplateController extends Controller
{
    public function index()
    {
        return EmailTemplate::paginate(10);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
        ]);

        $template = EmailTemplate::create($validated);

        return response()->json($template, 201);
    }

    public function show(EmailTemplate $emailTemplate)
    {
        return $emailTemplate;
    }

    public function update(Request $request, EmailTemplate $emailTemplate)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'subject' => 'sometimes|string|max:255',
            'body' => 'sometimes|string',
        ]);

        $emailTemplate->update($validated);

        return response()->json($emailTemplate);
    }

    public function destroy(EmailTemplate $emailTemplate)
    {
        $emailTemplate->delete();

        return response()->json(null, 204);
    }

    public function restore($id)
{
    $template = EmailTemplate::onlyTrashed()->findOrFail($id);
    $template->restore();

    return response()->json(['message' => 'Template restored successfully.']);
}

public function trashed()
{
    $trashedTemplates = EmailTemplate::onlyTrashed()->paginate(10);
    return response()->json($trashedTemplates);
}


}
