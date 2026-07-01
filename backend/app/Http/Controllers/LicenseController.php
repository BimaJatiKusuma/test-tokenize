<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\License;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class LicenseController extends Controller
{
    public function index()
    {
        $licenses = License::orderBy('created_at', 'desc')->get();
        return view('licenses.index', compact('licenses'));
    }

    public function generate()
    {
        License::create([
            'license_key' => 'LIC-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4)),
            'status' => 'AVAILABLE',
            'expires_at' => Carbon::now()->addDays(30),
        ]);

        return redirect()->back()->with('success', 'License Key successfully generated!');
    }

    public function forceExpire($id)
    {
        $license = License::findOrFail($id);
        $license->update([
            'expires_at' => Carbon::now()->subMinute(),
        ]);

        return redirect()->back()->with('success', 'License successfully set to expired!');
    }

    public function activate(Request $request)
    {
        $request->validate([
            'license_key' => 'required|string',
            'device_id' => 'required|string',
        ]);

        $license = License::where('license_key', $request->license_key)->first();
        $user = Auth::user(); // Diambil dari token Sanctum

        if (!$license || $license->status === 'REVOKED') {
            return response()->json(['success' => false, 'message' => 'Lisensi tidak valid/dicabut!'], 403);
        }

        if ($license->device_id !== null && $license->device_id !== $request->device_id) {
            return response()->json(['success' => false, 'message' => 'Lisensi terdaftar di perangkat lain!'], 400);
        }

        // Bind lisensi ke User dan Device
        $license->device_id = $request->device_id;
        $license->user_id = $user->id; 
        $license->status = 'ACTIVE';
        $license->save();

        // GENERATE SECURE OFFLINE TOKEN (HMAC SHA256)
        $payload = json_encode([
            'device_id' => $license->device_id,
            'expires_at' => $license->expires_at->timestamp
        ]);
        
        // Gunakan config('app.key') agar tidak null saat config di-cache pada production
        $signature = hash_hmac('sha256', $payload, config('app.key'));
        $secureOfflineToken = base64_encode($payload . '.' . $signature);

        return response()->json([
            'success' => true,
            'message' => 'Aktivasi berhasil!',
            'offline_token' => $secureOfflineToken,
            'expires_at' => $license->expires_at->toIso8601String(),
        ]);
    }
}