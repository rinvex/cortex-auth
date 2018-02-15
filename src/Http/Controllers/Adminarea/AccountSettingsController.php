<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Controllers\Adminarea;

use Illuminate\Http\Request;
use Cortex\Fort\Http\Requests\Adminarea\AccountSettingsRequest;
use Cortex\Foundation\Http\Controllers\AuthenticatedController;

class AccountSettingsController extends AuthenticatedController
{
    /**
     * Show account settings.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        return intend([
            'url' => route('adminarea.account.settings'),
        ]);
    }

    /**
     * Edit account settings.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\View\View
     */
    public function edit(Request $request)
    {
        $countries = collect(countries())->map(function ($country, $code) {
            return [
                'id' => $code,
                'text' => $country['name'],
                'emoji' => $country['emoji'],
            ];
        })->values();
        $languages = collect(languages())->pluck('name', 'iso_639_1');
        $genders = ['male' => trans('cortex/fort::common.male'), 'female' => trans('cortex/fort::common.female')];

        return view('cortex/fort::adminarea.pages.account-settings', compact('countries', 'languages', 'genders'));
    }

    /**
     * Update account settings.
     *
     * @param \Cortex\Fort\Http\Requests\Adminarea\AccountSettingsRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(AccountSettingsRequest $request)
    {
        $data = $request->validated();
        $currentUser = $request->user($this->getGuard());

        // Update profile
        $currentUser->fill($data)->save();

        return intend([
            'back' => true,
            'with' => ['success' => trans('cortex/fort::messages.account.updated_account')]
                      + (isset($data['two_factor']) ? ['warning' => trans('cortex/fort::messages.verification.twofactor.phone.auto_disabled')] : []),
        ]);
    }
}