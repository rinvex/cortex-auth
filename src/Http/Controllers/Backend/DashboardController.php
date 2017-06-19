<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Controllers\Backend;

use Cortex\Fort\Models\Role;
use Cortex\Fort\Models\User;
use Cortex\Fort\Models\Ability;
use Rinvex\Fort\Models\Session;
use Illuminate\Support\Facades\DB;
use Cortex\Foundation\Http\Controllers\AuthorizedController;

class DashboardController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'dashboard';

    /**
     * {@inheritdoc}
     */
    protected $resourceAbilityMap = ['home' => 'access'];

    /**
     * Show the dashboard home.
     *
     * @return \Illuminate\Http\Response
     */
    public function home()
    {
        // Get statistics
        $stats = [
            'abilities' => ['route' => route('backend.abilities.index'), 'count' => Ability::count()],
            'roles' => ['route' => route('backend.roles.index'), 'count' => Role::count()],
            'users' => ['route' => route('backend.users.index'), 'count' => User::count()],
        ];

        // Get online users
        $sessions = Session::users(config('rinvex.fort.online_interval'))->groupBy(['user_id'])->with(['user'])->get(['user_id', DB::raw('MAX(last_activity) as last_activity')]);

        return view('cortex/fort::backend.pages.home', compact('sessions', 'stats'));
    }
}
