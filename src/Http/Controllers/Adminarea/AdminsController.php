<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Adminarea;

use Illuminate\Http\Request;
use Cortex\Auth\Models\Admin;
use Cortex\Foundation\Http\FormRequest;
use Cortex\Foundation\DataTables\LogsDataTable;
use Cortex\Foundation\Importers\InsertImporter;
use Cortex\Auth\DataTables\Adminarea\AdminsDataTable;
use Cortex\Foundation\DataTables\ActivitiesDataTable;
use Cortex\Foundation\Http\Requests\ImportFormRequest;
use Cortex\Auth\Http\Requests\Adminarea\AdminFormRequest;
use Cortex\Foundation\Http\Controllers\AuthorizedController;

class AdminsController extends AuthorizedController
{
    /**
     * {@inheritdoc}
     */
    protected $resource = 'cortex.auth.models.admin';

    /**
     * List all admins.
     *
     * @param \Cortex\Auth\DataTables\Adminarea\AdminsDataTable $adminsDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index(AdminsDataTable $adminsDataTable)
    {
        $countries = collect(countries())->map(function ($country, $code) {
            return [
                'id' => $code,
                'text' => $country['name'],
                'emoji' => $country['emoji'],
            ];
        })->values();

        $roles = app('cortex.auth.role')->pluck('title', 'id');
        $languages = collect(languages())->pluck('name', 'iso_639_1');
        $tags = app('rinvex.tags.tag')->all()->groupBy('group')->map->pluck('name', 'id')->sortKeys();
        $genders = ['male' => trans('cortex/auth::common.male'), 'female' => trans('cortex/auth::common.female')];

        return $adminsDataTable->with([
            'id' => 'adminarea-cortex-auth-admins-index',
            'countries' => $countries,
            'languages' => $languages,
            'genders' => $genders,
            'roles' => $roles,
            'tags' => $tags,
            'routePrefix' => 'adminarea.cortex.auth.admins',
            'pusher' => ['entity' => 'admin', 'channel' => 'cortex.auth.admins.index'],
        ])->render('cortex/auth::adminarea.pages.admins');
    }

    /**
     * List admin logs.
     *
     * @param \Cortex\Auth\Models\Admin                   $admin
     * @param \Cortex\Foundation\DataTables\LogsDataTable $logsDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function logs(Admin $admin, LogsDataTable $logsDataTable)
    {
        return $logsDataTable->with([
            'resource' => $admin,
            'tabs' => 'adminarea.cortex.auth.admins.tabs',
            'id' => "adminarea-cortex-auth-admins-{$admin->getRouteKey()}-logs",
        ])->render('cortex/foundation::adminarea.pages.datatable-tab');
    }

    /**
     * Get a listing of the resource activities.
     *
     * @param \Cortex\Auth\Models\Admin                         $admin
     * @param \Cortex\Foundation\DataTables\ActivitiesDataTable $activitiesDataTable
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function activities(Admin $admin, ActivitiesDataTable $activitiesDataTable)
    {
        return $activitiesDataTable->with([
            'resource' => $admin,
            'tabs' => 'adminarea.cortex.auth.admins.tabs',
            'id' => "adminarea-cortex-auth-admins-{$admin->getRouteKey()}-activities",
        ])->render('cortex/foundation::adminarea.pages.datatable-tab');
    }

    /**
     * Import admins.
     *
     * @param \Cortex\Foundation\Http\Requests\ImportFormRequest $request
     * @param \Cortex\Foundation\Importers\InsertImporter        $importer
     * @param \Cortex\Auth\Models\Admin                          $admin
     *
     * @return void
     */
    public function import(ImportFormRequest $request, InsertImporter $importer, Admin $admin)
    {
        $importer->withModel($admin)->import($request->file('file'));
    }

    /**
     * Create new admin.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Cortex\Auth\Models\Admin $admin
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, Admin $admin)
    {
        return $this->form($request, $admin);
    }

    /**
     * Edit given admin.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Cortex\Auth\Models\Admin $admin
     *
     * @return \Illuminate\View\View
     */
    public function edit(AdminFormRequest $request, Admin $admin)
    {
        return $this->form($request, $admin);
    }

    /**
     * Show admin create/edit form.
     *
     * @param \Illuminate\Http\Request  $request
     * @param \Cortex\Auth\Models\Admin $admin
     *
     * @return \Illuminate\View\View
     */
    protected function form(Request $request, Admin $admin)
    {
        if (! $admin->exists && $request->has('replicate') && $replicated = $admin->resolveRouteBinding($request->input('replicate'))) {
            $admin = $replicated->replicate();
        }

        $countries = collect(countries())->map(function ($country, $code) {
            return [
                'id' => $code,
                'text' => $country['name'],
                'emoji' => $country['emoji'],
            ];
        })->values();

        $tags = app('rinvex.tags.tag')->pluck('name', 'id');
        $languages = collect(languages())->pluck('name', 'iso_639_1');
        $genders = ['male' => trans('cortex/auth::common.male'), 'female' => trans('cortex/auth::common.female')];
        $abilities = $request->user()->getManagedAbilityIds();
        $roles = $request->user()->getManagedRoles();

        return view('cortex/auth::adminarea.pages.admin', compact('admin', 'abilities', 'roles', 'countries', 'languages', 'genders', 'tags'));
    }

    /**
     * Store new admin.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\AdminFormRequest $request
     * @param \Cortex\Auth\Models\Admin                             $admin
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function store(AdminFormRequest $request, Admin $admin)
    {
        return $this->process($request, $admin);
    }

    /**
     * Update given admin.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\AdminFormRequest $request
     * @param \Cortex\Auth\Models\Admin                             $admin
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AdminFormRequest $request, Admin $admin)
    {
        return $this->process($request, $admin);
    }

    /**
     * Process stored/updated admin.
     *
     * @param \Cortex\Foundation\Http\FormRequest $request
     * @param \Cortex\Auth\Models\Admin           $admin
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function process(FormRequest $request, Admin $admin)
    {
        // Prepare required input fields
        $data = $request->validated();

        ! $request->hasFile('profile_picture')
        || $admin->addMediaFromRequest('profile_picture')
                ->sanitizingFileName(function ($fileName) {
                    return md5($fileName).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
                })
                ->toMediaCollection('profile_picture', config('cortex.foundation.media.disk'));

        ! $request->hasFile('cover_photo')
        || $admin->addMediaFromRequest('cover_photo')
                ->sanitizingFileName(function ($fileName) {
                    return md5($fileName).'.'.pathinfo($fileName, PATHINFO_EXTENSION);
                })
                ->toMediaCollection('cover_photo', config('cortex.foundation.media.disk'));

        // Save admin
        $admin->fill($data)->save();

        return intend([
            'url' => route('adminarea.cortex.auth.admins.index'),
            'with' => ['success' => trans('cortex/foundation::messages.resource_saved', ['resource' => trans('cortex/auth::common.admin'), 'identifier' => $admin->getRouteKey()])],
        ]);
    }

    /**
     * Destroy given admin.
     *
     * @param \Cortex\Auth\Models\Admin $admin
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return intend([
            'url' => route('adminarea.cortex.auth.admins.index'),
            'with' => ['warning' => trans('cortex/foundation::messages.resource_deleted', ['resource' => trans('cortex/auth::common.admin'), 'identifier' => $admin->getRouteKey()])],
        ]);
    }
}
