<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        $ancillaryRequests = \App\Models\AncillaryRequest::with(['consultation.patient', 'consultation.doctor'])
            ->whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->get();

        return view('lab.dashboard', compact('user', 'ancillaryRequests'));
    }

    public function completeRequest(Request $request, \App\Models\AncillaryRequest $ancillary)
    {
        $request->validate([
            'result_text' => 'required|string'
        ]);

        $ancillary->update([
            'status' => 'completed',
            'result_text' => $request->result_text
        ]);

        return back()->with('success', 'Ancillary Request completed successfully. The doctor has been notified.');
    }
}
