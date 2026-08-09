<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('role')->latest();
        if ($request->filled('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', '%'.$request->input('search').'%')->orWhere('email', 'like', '%'.$request->input('search').'%'));
        }

        return UserResource::collection($query->paginate($request->integer('per_page', 10)));
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = User::create($request->validated());

        return response()->json(['message' => 'User berhasil dibuat.', 'data' => new UserResource($user->load('role'))], 201);
    }

    public function show(User $user): UserResource
    {
        return new UserResource($user->load('role'));
    }

    public function update(UpdateUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();
        if (empty($data['password'])) {
            unset($data['password']);
        } $user->update($data);

        return response()->json(['message' => 'User berhasil diperbarui.', 'data' => new UserResource($user->fresh('role'))]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        abort_if($request->user()->is($user), 422, 'User tidak dapat menghapus akun sendiri.');
        $user->delete();

        return response()->json(['message' => 'User berhasil dihapus.']);
    }
}
