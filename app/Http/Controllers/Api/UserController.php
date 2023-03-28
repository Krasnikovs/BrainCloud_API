<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Resources\UserResource;
use App\Mail\MailNotify;
use App\Models\Folder;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('id', 'ASC')->paginate(10);
        return UserResource::collection($users);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'image' => '',
            'name' => '',
            'email' => 'email',
            'password' => 'nullable|min:8|regex:/[a-z]/|regex:/[A-Z]/|regex:/[0-9]/',
            'subscription_type' => '',
        ]);
        if($request->hasFile('image'))
        {
            Storage::disk('local')->delete('public/userAvatars/'.$user->image);
            $image = $validated['image'];
            $validated['image'] = $image->hashName();
            $image->store('public/userAvatars');
        }
        if (isset($validated['password'])){
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }
        $user->update($validated);
        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        if ($user->getRoleNames()[0] != 'Fake User') {
            $user->delete();
            Storage::disk('local')->delete('public/userAvatars/'.$user->image);
        }
        $user->delete();

        return new UserResource($user);
    }

    public function getFile (Request $request, User $user)
    {
        if(!$request->hasValidSignature()) return abort(401);
        $user->image = Storage::disk('local')->path('public/userAvatars/'.$user->image);
        return response()->file($user->image);
    }

    public function userFilter (Request $request)
    {
        $validated = $request->validate([
           'name' => 'sometimes'
        ]);
        if (!isset($validated['name'])) {
            $users = User::orderBy('id', 'ASC')->paginate(10);
        } else {
            $users = User::where('name', 'LIKE', "%{$validated['name']}%")
                ->orWhere('email', 'LIKE', "%{$validated['name']}%")
                ->paginate(10);
        }

        return UserResource::collection($users);
    }

    public function resetPassword (Request $request) {
        $validated = $request->validate([
            'email' => 'required|email'
        ]);
        $validated['password'] = Str::random(30);

        $user = User::where('email', $validated['email'])->first();
        if (isset($user)) {
            Mail::to($validated['email'])->send(new MailNotify($validated['email'], $validated['password']));
            $validated['password'] = Hash::make($validated['password']);
            $user->update(array('password' => $validated['password']));
        }

        return response()->json([
            'data' => 'If email exist then to the specified e-mail address password has been sent'
        ], 200);
    }
}
