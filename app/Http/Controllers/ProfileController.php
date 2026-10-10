<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        return response()->json(['user' => Auth::user()->load('role')]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'username' => ['required', 'string', 'max:50'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id_user . ',id_user'],
            'contact' => ['nullable', 'string', 'max:255'],
            'aboutme' => ['nullable', 'string', 'max:5000'],
            'photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'social_links' => ['nullable', 'array'],
            'social_links.instagram' => ['nullable', 'url', 'max:2048'],
            'social_links.facebook' => ['nullable', 'url', 'max:2048'],
            'social_links.linkedin' => ['nullable', 'url', 'max:2048'],
            'social_links.email' => ['nullable', 'email', 'max:255'],
            'social_links.whatsapp' => ['nullable', 'string', 'max:50'],
            'social_links.twitter' => ['nullable', 'url', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            if ($user->photo) Storage::disk('public')->delete($user->photo);
            $validated['photo'] = $request->file('photo')->store('users', 'public');
        }

        $socialLinks = $validated['social_links'] ?? [];
        foreach ($socialLinks as $key => $value) {
            $socialLinks[$key] = is_string($value) ? trim($value) : $value;
        }
        $validated['social_links'] = $socialLinks;
        $user->fill($validated);
        $user->save();

        return response()->json(['message' => 'Profile updated successfully.', 'user' => $user->fresh()->load('role')]);
    }
}
