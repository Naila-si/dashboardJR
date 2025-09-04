<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Kecelakaan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ActivityController extends Controller
{
    public function index(Kecelakaan $kecelakaan)
    {
        $activities = $kecelakaan->activities()->latest()->get();
        return response()->json($activities); // Return JSON supaya AJAX bisa ambil data
    }

    public function store(Request $request, Kecelakaan $kecelakaan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|max:50',
            'date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('activities', 'public');
        }

        $validated['kecelakaan_id'] = $kecelakaan->id;
        $activity = Activity::create($validated);

        return response()->json([
            'message' => 'Activity berhasil disimpan!',
            'activity' => $activity
        ]);
    }

    public function edit(Kecelakaan $kecelakaan, Activity $activity)
    {
        // Bisa kembalikan JSON untuk modal
        return response()->json($activity);
    }

    public function update(Request $request, Kecelakaan $kecelakaan, Activity $activity)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'priority' => 'required|string|max:50',
            'date' => 'nullable|date',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($activity->image) {
                Storage::disk('public')->delete($activity->image);
            }
            $validated['image'] = $request->file('image')->store('activities', 'public');
        }

        $activity->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Activity berhasil diupdate',
            'activity' => $activity
        ]);
    }

    public function destroy(Kecelakaan $kecelakaan, Activity $activity)
    {
        if ($activity->image) {
            Storage::disk('public')->delete($activity->image);
        }

        $activity->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Activity berhasil dihapus'
        ]);
    }
}
