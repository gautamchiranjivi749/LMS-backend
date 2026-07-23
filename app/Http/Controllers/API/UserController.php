<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\UserResource;
use App\Traits\ApiResponse;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
        use ApiResponse;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
          $query = User::query();

    // Search by name or email
    if ($request->filled('search')) {
        $search = $request->search;

        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // Filter by role
    if ($request->filled('role')) {
        $query->role($request->role);
    }

    $users = $query
        ->latest()
        ->paginate(10);

    return $this->success(
        'Users retrieved successfully.',
        [
            'users' => UserResource::collection($users),
            'pagination' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ]
        ]
    );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
          $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
    ]);

    $user->assignRole($request->role);

    return $this->success(
        'User created successfully.',
        new UserResource($user->load('roles')),
        201
    );
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return $this->success(
        'User retrieved successfully.',
        new UserResource($user)
    );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        $user->syncRoles([$request->role]);

        return $this->success(
            'User updated successfully.',
            new UserResource($user->fresh())
        );
    }

    /**
     * Remove the specified resource from storage.
     */
   public function destroy(User $user)
{
    $user->delete();

    return $this->success(
        'User deleted successfully.'
    );
}
}
