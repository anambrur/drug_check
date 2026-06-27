<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsortiumEnrollment;
use App\Models\Admin\Favicon;
use App\Models\Admin\PanelImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConsortiumEnrollmentAdminController extends Controller
{
    /**
     * Display a listing of all consortium enrollments.
     */
    public function index()
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $enrollments = ConsortiumEnrollment::orderBy('created_at', 'desc')->paginate(15);

        return view('admin.consortium_enrollments.index', compact('favicon', 'panel_image', 'enrollments'));
    }

    /**
     * Display the specified enrollment details.
     */
    public function show($id)
    {
        $favicon = Favicon::first();
        $panel_image = PanelImage::first();
        $enrollment = ConsortiumEnrollment::findOrFail($id);

        return view('admin.consortium_enrollments.show', compact('favicon', 'panel_image', 'enrollment'));
    }

    /**
     * Update the enrollment status.
     */
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:Pending Payment,Payment Completed,Under Review,Contacted,Credentials Sent,Active,Cancelled',
        ]);

        if ($validator->fails()) {
            toastr()->error($validator->errors()->first(), 'content.error');
            return back();
        }

        $enrollment = ConsortiumEnrollment::findOrFail($id);
        $oldStatus = $enrollment->status;
        $newStatus = $request->input('status');

        $enrollment->update([
            'status' => $newStatus,
        ]);

        // Append to internal notes that the status changed
        $timestamp = now()->toDateTimeString();
        $statusLog = "\n[System - {$timestamp}]: Status updated from '{$oldStatus}' to '{$newStatus}'.";
        $enrollment->update([
            'internal_notes' => $enrollment->internal_notes . $statusLog,
        ]);

        toastr()->success('Enrollment status updated successfully.', 'content.success');

        return back();
    }

    /**
     * Update the internal notes log.
     */
    public function updateNotes(Request $request, $id)
    {
        $enrollment = ConsortiumEnrollment::findOrFail($id);
        
        $newNote = $request->input('note');
        if ($newNote) {
            $timestamp = now()->toDateTimeString();
            $author = auth()->user() ? auth()->user()->name : 'Admin';
            $logEntry = "\n[{$author} - {$timestamp}]: {$newNote}";
            
            $enrollment->update([
                'internal_notes' => $enrollment->internal_notes . $logEntry,
            ]);

            toastr()->success('Note added successfully.', 'content.success');
        }

        return back();
    }
}
