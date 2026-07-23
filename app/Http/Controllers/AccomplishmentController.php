<?php

namespace App\Http\Controllers;

use App\Models\Accomplishment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AccomplishmentController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->input('month', Carbon::now('Asia/Manila')->month);
        $year = $request->input('year', Carbon::now('Asia/Manila')->year);

        $accomplishments = Accomplishment::where('user_id', Auth::id())
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->orderBy('date')
            ->orderBy('created_at')
            ->get()
            ->groupBy(fn($a) => $a->date->format('Y-m-d'));

        return view('accomplishments', compact('accomplishments', 'month', 'year'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:1000',
            'date' => 'nullable|date',
            'photo' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        $user = Auth::user();
        $date = $request->input('date', Carbon::now('Asia/Manila')->toDateString());
        $now = Carbon::now('Asia/Manila');
        $photoPath = null;

        $photoData = $request->input('photo');
        if ($photoData && str_starts_with($photoData, 'data:image')) {
            $image = preg_replace('/^data:image\/\w+;base64,/', '', $photoData);
            $image = base64_decode($image);

            $folder = "accomplishments/{$now->format('Y/m')}";
            $filename = "{$user->bio_id}_{$date}_{$now->format('His')}.jpg";
            $photoPath = "{$folder}/{$filename}";

            try {
                Storage::disk('synology')->put($photoPath, $image);
            } catch (\Exception $e) {
                Storage::disk('public')->makeDirectory($folder);
                Storage::disk('public')->put($photoPath, $image);
            }
        }

        Accomplishment::create([
            'user_id' => $user->id,
            'date' => $date,
            'description' => $request->description,
            'photo_path' => $photoPath,
            'latitude' => $request->input('latitude'),
            'longitude' => $request->input('longitude'),
            'address' => $request->input('address'),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Work accomplishment added with photo.']);
        }

        return back()->with('success', 'Work accomplishment added with photo.');
    }

    public function destroy(Accomplishment $accomplishment)
    {
        if ($accomplishment->user_id !== Auth::id()) {
            abort(403);
        }

        $accomplishment->delete();
        return back()->with('success', 'Accomplishment removed.');
    }
}
