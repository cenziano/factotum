<?php

namespace Kaleidoscope\Factotum\Policies;

use Kaleidoscope\Factotum\User;
use Kaleidoscope\Factotum\Capability;
use Illuminate\Auth\Access\HandlesAuthorization;

class CapabilityPolicy
{
    use HandlesAuthorization;

	public function before($user)
	{
		return ( $user->role->backend_access && $user->role->manage_users ? true : false );
	}
}
