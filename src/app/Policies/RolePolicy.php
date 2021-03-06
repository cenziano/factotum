<?php

namespace Kaleidoscope\Factotum\Policies;

use Kaleidoscope\Factotum\User;
use Kaleidoscope\Factotum\Role;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
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

	public function update(User $user, $roleID)
	{
		$role = Role::find($roleID);
		return ( $role->editable || !$role->editable && auth()->user()->isAdmin() ? true : false );
	}

	public function delete(User $user, $roleID)
	{
		$role = Role::find($roleID);
		return ( $role->editable || !$role->editable && auth()->user()->isAdmin() ? true : false );
	}

	public function manageSettings(User $user)
	{
		return ( $user->role->backend_access && $user->role->manage_settings ? true : false );
	}
}
