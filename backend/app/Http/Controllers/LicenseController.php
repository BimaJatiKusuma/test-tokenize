<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\License;
use Illuminate\Support\Str;
use Carbon\Carbon;

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

        if (!$license) {
            return response()->json([
                'success' => false,
                'message' => 'License Key not found!'
            ], 404);
        }

        if ($license->status === 'REVOKED') {
            return response()->json([
                'success' => false,
                'message' => 'License has been revoked!'
            ], 403);
        }

        // Allow activation if device matches, or if it is still available (empty device_id)
        if ($license->device_id !== null && $license->device_id !== $request->device_id) {
            return response()->json([
                'success' => false,
                'message' => 'License is registered to another device!'
            ], 400);
        }

        // Activate the license
        $license->device_id = $request->device_id;
        $license->status = 'ACTIVE';
        $license->save();

        // simple encyption base64: device_id + '|' + expires_at timestamp
        $rawToken = $license->device_id . '|' . $license->expires_at->toIso8601String();
        $encryptedToken = base64_encode($rawToken);

        return response()->json([
            'success' => true,
            'message' => 'Activation successful!',
            'encrypted_token' => $encryptedToken,
            'expires_at' => $license->expires_at->toIso8601String(),
        ]);
    }
}
