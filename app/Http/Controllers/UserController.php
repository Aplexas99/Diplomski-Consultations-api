<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->get('per_page') ?? 50;
        $users = User::query();

        /** Filters */
        if ($request->get('name')) {
            $users = $users->filterByName($request->get('name'));
        }
        if ($request->get('last_name')) {
            $users = $users->filterByLastName($request->get('last_name'));
        }
        if ($request->get('email')) {
            $users = $users->filterByEmail($request->get('email'));
        }
        if ($request->get('role')) {
            $users = $users->filterByRole($request->get('role'));
        }

        
        /** Sorts */
        if($request->get('sort_by')) {
            $sortBy = $request->get('sort_by');
            $sortDirection = $request->get('sort_direction') ? $request->get('sort_direction') : 'asc';
            if($sortBy == 'name') {
                $users = $users->sortByName($sortDirection);
            }
            if($sortBy == 'last-name') {
                $users = $users->sortByLastName($sortDirection);
            }
            if($sortBy == 'email') {
                $users = $users->sortByEmail($sortDirection);
            }
            if($sortBy == 'role') {
                $users = $users->sortByRole($sortDirection);
            }
        }

        $users = $users->paginate($perPage);

        return UserResource::collection($users);
    }

    public function create()
    {
        //
    }

    public function store(StoreUserRequest $request)
    {
        $user = new User($request->validated());
        $user->password = bcrypt("1234");
        $user->save();
        return new UserResource($user);
    }

    public function show(User $user)
    {
        return new UserResource($user);
    }

    public function edit(User $user)
    {
        //
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $user->update($request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return new UserResource($user);
    }
}
