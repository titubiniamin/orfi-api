<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateUserProfileRequest;
use App\Http\Resources\UserResource;
use App\Mail\PasswordChanged;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class UserProfileController extends Controller
{
    /**
     * @return Authenticatable|null
     */
    public function profile()
    {
        return Auth::user();
    }

    /**
     * @param UpdateUserProfileRequest $request
     * @param $id
     * @return JsonResponse
     */
    public function updateProfile(UpdateUserProfileRequest $request, $id): JsonResponse
    {
        $userInfo = $request->all();
        $user = User::find($id);
        if ($request->filled('password')) {
            $userPassword = Auth::user()->password;
            if (!Hash::check($request->current_password, $userPassword)) {
                return response()->json(['message' => 'Current password not match.', 'status' => 422]);
            }
            Mail::to($user->email)->queue(new PasswordChanged()); // Send email to user for password changed
        }
        $user->update($userInfo);
        return response()->json([
            'message' => 'Profile Information updated successfully.',
            'userInfo' => UserResource::make($user),
            'status' => 200
        ]);
    }
}
