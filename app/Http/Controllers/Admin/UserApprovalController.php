<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccessRequest;
use App\Actions\Admin\ApproveAccessRequestAction;
use App\Actions\Admin\RejectAccessRequestAction;
use Illuminate\Http\Request;

class UserApprovalController extends Controller
{
    public function index()
    {
        $pendingRequests = AccessRequest::with('user')
            ->where('status', 'pending')
            ->latest()
            ->get();

        return view('admin.approvals.index', compact('pendingRequests'));
    }

    public function approve(AccessRequest $accessRequest, ApproveAccessRequestAction $action)
    {
        try {
            $action->execute($accessRequest, auth()->id());

            return back()->with('success', "Request for {$accessRequest->user->username} has been approved.");
        } catch (\Exception $e) {
            return back()->with('error', "Failed to approve: " . $e->getMessage());
        }
    }

    public function reject(AccessRequest $accessRequest, Request $request, RejectAccessRequestAction $action)
    {
        $request->validate([
            'reason' => 'required|string|min:5|max:255'
        ]);

        try {
            $action->execute($accessRequest, $request->reason, auth()->id());
            return back()->with('success', "Request for {$accessRequest->user->username} has been rejected.");
        } catch (\Exception $e) {
            return back()->with('error', "Failed to reject: " . $e->getMessage());
        }
    }
}
