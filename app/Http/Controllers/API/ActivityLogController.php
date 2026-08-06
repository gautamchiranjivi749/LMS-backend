<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index()
    {
        $activityLogs = \App\Models\ActivityLog::with('user')->latest()->get();
        return response()->json($activityLogs);
    }

    public function show($id)
    {
        $activityLog = \App\Models\ActivityLog::with('user')->findOrFail($id);
        return response()->json($activityLog);
    }
}
