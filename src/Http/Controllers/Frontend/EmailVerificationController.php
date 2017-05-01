<?php

declare(strict_types=1);

namespace Cortex\Fort\Http\Controllers\Frontend;

use Carbon\Carbon;
use Cortex\Fort\Models\User;
use Illuminate\Http\Request;
use Cortex\Foundation\Http\Controllers\AbstractController;
use Rinvex\Fort\Contracts\EmailVerificationBrokerContract;
use Cortex\Fort\Http\Requests\Frontend\EmailVerificationRequest;

class EmailVerificationController extends AbstractController
{
    /**
     * Show the email verification request form.
     *
     * @param \Cortex\Fort\Http\Requests\Frontend\EmailVerificationRequest $request
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function request(EmailVerificationRequest $request)
    {
        if (! $this->isValid($request)) {
            return $this->redirect();
        }

        return view('cortex/fort::frontend.verification.email-request');
    }

    /**
     * Process the email verification request form.
     *
     * @param \Cortex\Fort\Http\Requests\Frontend\EmailVerificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function send(EmailVerificationRequest $request)
    {
        if (! $this->isValid($request)) {
            return $this->redirect();
        }

        $result = app('rinvex.fort.emailverification')
            ->broker($this->getBroker())
            ->sendVerificationLink($request->only(['email']));

        switch ($result) {
            case EmailVerificationBrokerContract::LINK_SENT:
                return intend([
                    'url' => '/',
                    'with' => ['success' => trans($result)],
                ]);

            default:
                return intend([
                    'back' => true,
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Process the email verification.
     *
     * @param \Cortex\Fort\Http\Requests\Frontend\EmailVerificationRequest $request
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function verify(EmailVerificationRequest $request)
    {
        if (! $this->isValid($request)) {
            return $this->redirect();
        }

        $result = app('rinvex.fort.emailverification')->broker($this->getBroker())->verify($request->only(['email', 'token']), function ($user) {
            $user->fill([
                'email_verified' => true,
                'email_verified_at' => new Carbon(),
            ])->forceSave();
        });

        switch ($result) {
            case EmailVerificationBrokerContract::EMAIL_VERIFIED:
                return intend([
                    'url' => $request->user($this->getGuard()) ? route('frontend.account.settings') : route('frontend.auth.login'),
                    'with' => ['success' => trans($result)],
                ]);

            case EmailVerificationBrokerContract::INVALID_USER:
            case EmailVerificationBrokerContract::INVALID_TOKEN:
            default:
                return intend([
                    'url' => route('frontend.verification.email.request'),
                    'withInput' => $request->only(['email']),
                    'withErrors' => ['email' => trans($result)],
                ]);
        }
    }

    /**
     * Check whether the given request is valid.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    protected function isValid(Request $request)
    {
        return ! ((($user = $request->user($this->getGuard())) && $user->email_verified) || ($request->get('email') && array_get(User::where('email', $request->get('email'))->first(), 'email_verified')));
    }

    /**
     * Return redirect response.
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    protected function redirect()
    {
        return intend([
            'url' => route('frontend.account.settings'),
            'with' => ['warning' => trans('cortex/fort::messages.verification.email.already')],
        ]);
    }
}
