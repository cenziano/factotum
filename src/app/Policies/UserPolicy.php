<?php

namespace Kaleidoscope\Factotum\Policies;

use Kaleidoscope\Factotum\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

	public function view(User $user)
	{
		return ( $user->role->backend_access && $user->role->manage_users ? true : false );
	}

	public function create(User $user)
	{
		return ( $user->role->backend_access && $user->role->manage_users ? true : false );
	}

	public function update(User $user, $userID)
	{
		$userOnEdit = User::find($userID);
		return ( $userOnEdit->editable || !$userOnEdit->editable && auth()->user()->isAdmin() ? true : false );
	}

	public function delete(User $user, $userID)
	{
		$userOnEdit = User::find($userID);
		return ( $userOnEdit->editable || !$userOnEdit->editable && auth()->user()->isAdmin() ? true : false );
	}
}
