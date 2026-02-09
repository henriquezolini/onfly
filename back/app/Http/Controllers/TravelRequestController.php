<?php

namespace App\Http\Controllers;

use App\Models\TravelRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class TravelRequestController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $user = Auth::user();
        $query = TravelRequest::query();

        if (!$user->is_admin) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('destination')) {
            $query->where('destination', 'like', '%' . $request->destination . '%');
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereBetween('departure_date', [$request->start_date, $request->end_date])
                  ->orWhereBetween('return_date', [$request->start_date, $request->end_date]);
            });
        }

        return response()->json($query->with('user')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'destination' => 'required|string',
            'departure_date' => 'required|date',
            'return_date' => 'required|date|after:departure_date',
        ]);

        $travelRequest = $request->user()->travelRequests()->create($validated);

        return response()->json($travelRequest, 201);
    }

    public function show(TravelRequest $travelRequest)
    {
        $this->authorize('view', $travelRequest);
        return response()->json($travelRequest->load('user'));
    }

    public function updateStatus(Request $request, TravelRequest $travelRequest)
    {
        $this->authorize('updateStatus', $travelRequest);

        $validated = $request->validate([
            'status' => 'required|in:approved,cancelled',
        ]);

        if ($validated['status'] === 'cancelled' && $travelRequest->status === 'approved') {
             return response()->json(['message' => 'Cannot cancel an approved request.'], 403);
        }

        $travelRequest->update(['status' => $validated['status']]);

        $travelRequest->user->notify(new \App\Notifications\TravelRequestStatusChanged($travelRequest));
        
        return response()->json($travelRequest);
    }
}