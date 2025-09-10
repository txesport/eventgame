<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $stats = [
            'groups' => $user->groups()->count(),
            'events' => $user->events()->count(),
            'photos' => $user->eventPhotos()->count(),
        ];

        $recentEvents = $user->events()->latest()->take(5)->get();
        $recentGroups = $user->groups()->latest()->take(5)->get();

        return view('dashboard', compact('user', 'stats', 'recentEvents', 'recentGroups'));
    }
}
