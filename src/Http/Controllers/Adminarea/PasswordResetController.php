<?php

declare(strict_types=1);

namespace Cortex\Auth\Http\Controllers\Adminarea;

use Illuminate\Support\Str;
use Rinvex\Auth\Contracts\PasswordResetBrokerContract;
use Cortex\Foundation\Http\Controllers\AbstractController;
use Cortex\Auth\Http\Requests\Adminarea\PasswordResetRequest;
use Cortex\Auth\Http\Requests\Adminarea\PasswordResetSendRequest;
use Cortex\Auth\Http\Requests\Adminarea\PasswordResetProcessRequest;
use Cortex\Auth\Http\Requests\Adminarea\PasswordResetPostProcessRequest;

class PasswordResetController extends AbstractController
{
    /**
     * Show the password reset request form.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\PasswordResetRequest
     *
     * @return \Illuminate\View\View
     */
    public function request(PasswordResetRequest $request)
    {
        return view('cortex/auth::adminarea.pages.passwordreset-request');
    }

    /**
     * Process the password reset request form.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\PasswordResetSendRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(PasswordResetSendRequest $request)
    {
        $result = app('auth.password')
            ->broker($request->passwordResetBroker())
            ->sendResetLink($request->only(['email']));

        switch ($result) {
            case PasswordResetBrokerContract::RESET_LINK_SENT:
                return intend([
                    'url' => route('adminarea.home'),
                    'with' => ['success' => trans($result)],
                ]);

            case PasswordResetBrokerContract::INVALID_USER:
            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Show the password reset form.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\PasswordResetProcessRequest $request
     *
     * @return \Illuminate\View\View
     */
    public function reset(PasswordResetProcessRequest $request)
    {
        $credentials = $request->only('email', 'expiration', 'token');

        return view('cortex/auth::adminarea.pages.passwordreset')->with($credentials);
    }

    /**
     * Process the password reset form.
     *
     * @param \Cortex\Auth\Http\Requests\Adminarea\PasswordResetPostProcessRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function process(PasswordResetPostProcessRequest $request)
    {
        $result = app('auth.password')
            ->broker($request->passwordResetBroker())
            ->reset($request->only(['email', 'expiration', 'token', 'password', 'password_confirmation']), function ($user, $password) {
                $user->fill([
                    'password' => $password,
                    'remember_token' => Str::random(60),
                ])->forceSave();
            });

        switch ($result) {
            case PasswordResetBrokerContract::PASSWORD_RESET:
                return intend([
                    'url' => route('adminarea.cortex.auth.account.login'),
                    'with' => ['success' => trans($result)],
                ]);

            case PasswordResetBrokerContract::INVALID_USER:
            case PasswordResetBrokerContract::INVALID_TOKEN:
            case PasswordResetBrokerContract::EXPIRED_TOKEN:
            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }
}
