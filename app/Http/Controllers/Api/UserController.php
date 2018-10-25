<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $this->authorize('view', User::class);

        $users = User::all();
        return response()->json([
            'users' => $users
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $request->validate([
            'user_name' => 'required|string',
            'user_email' => 'required|string|email|unique:users,email',
            'user_password' => 'required|string|min:6|confirmed',
            'user_role_id' => 'required'
        ]);

        $user = User::create([
            'name' => $request->user_name,
            'email' => $request->user_email,
            'password' => bcrypt($request->user_password),
            'role_id' => $request->user_role_id
        ]);

        return response()->json([
            'message' => __('User Created'),
            'user' => $user
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(User $user)
    {
        $this->authorize('view', $user);

        return response()->json([
            'user' => $user
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'user_name' => 'required|string',
            'user_email' => 'required|string|email|unique:users,email,'.$user->id,
            'user_role_id' => 'required'
        ]);

        $user->name = $request->user_name;
        $user->email = $request->user_email;
        $user->role_id = $request->user_role_id;
        $user->save();

        return response()->json([
            'message' => __('User Updated'),
            'user' => $user
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(User $user)
    {
        $this->authorize('delete', $user);

        $user->delete();
        return response()->json([
            'message' => __('User Deleted')
        ], 200);
    }

    /**
     * Change Role of the user
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function changeRole(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'user_role_id' => 'required'
        ]);

        $user->role_id = $request->user_role_id;
        $user->save();

        return response()->json([
            'message' => __('Role Changed'),
        ], 200);
    }

    /**
     * Change Password of the user
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\User $user
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $request->validate([
            'old_password' => [
                'required',
                function($attribute, $value, $fail) use ($user) {
                    if(!Hash::check($value, $user->password)) {
                        return $fail($attribute . __('is invalid.'));
                    }
                },
            ],
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $user->password = $request->new_password;
        $user->save();

        return response()->json([
            'message' => __('Password Changed')
        ], 200);
    }
}
