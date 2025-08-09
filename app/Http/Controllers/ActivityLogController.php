<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return \Inertia\Response
     */
    public function index(Request $request)
    {
        $query = Activity::with(['causer', 'subject']);

        // Filter by causer name
        if ($request->filled('causer')) {
            $causer = $request->causer;
            $query->whereHas('causer', function ($q) use ($causer) {
                $q->where('name', 'like', "%{$causer}%");
            });
        }

        // Filter by description
        if ($request->filled('description')) {
            $query->where('description', 'like', "%{$request->description}%");
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        // Custom pagination
        $perPage    = (int) $request->input('per_page', 10);
        $activities = $query->paginate($perPage)->appends($request->query());

        return Inertia::render('Dashboard/ActivityLogs/Index', [
            'activities' => $activities,
            'filters'    => $request->only(['causer', 'description', 'date_from', 'date_to', 'sort_field', 'sort_order', 'per_page']),
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Inertia\Response
     */
    public function show($id)
    {
        // Find activity by id and load related causer and subject
        $activity = Activity::with(['causer', 'subject'])->findOrFail($id);
        return Inertia::render('Dashboard/ActivityLogs/Show', [
            'activity' => $activity,
        ]);
    }


}
