<?php

declare(strict_types=1);

Route::name('frontend.')
     ->middleware(['web', 'nohttpcache'])
     ->namespace('Cortex\Fort\Http\Controllers\Frontend')
     ->prefix(config('rinvex.cortex.route.locale_prefix') ? '{locale}' : '')->group(function () {

    // Homepage Routes
    Route::get('/')->name('home')->uses('HomeController@index');

    // Authentication Routes
    Route::name('auth.')->prefix('auth')->group(function () {

        // Login Routes
        Route::get('login')->name('login')->uses('AuthenticationController@form');
        Route::post('login')->name('login.process')->uses('AuthenticationController@login');
        Route::post('logout')->name('logout')->uses('AuthenticationController@logout');

        // Registration Routes
        Route::get('register')->name('register')->uses('RegistrationController@form');
        Route::post('register')->name('register.process')->uses('RegistrationController@register');

        // Social Authentication Routes
        Route::get('github')->name('social.github')->uses('SocialAuthenticationController@redirectToGithub');
        Route::get('github/callback')->name('social.github.callback')->uses('SocialAuthenticationController@handleGithubCallback');
    });

    // User Account Routes
    Route::name('account.')->prefix('account')->group(function () {

        // Account Page Routes
        Route::get('settings')->name('settings')->uses('AccountSettingsController@edit');
        Route::post('settings')->name('settings.update')->uses('AccountSettingsController@update');

        // Sessions Manipulation Routes
        Route::get('sessions')->name('sessions')->uses('AccountSessionsController@index');
        Route::delete('sessions/{token?}')->name('sessions.flush')->uses('AccountSessionsController@flush');

        // Two-Factor Authentication Routes
        Route::name('twofactor.')->prefix('twofactor')->group(function () {

            // Two-Factor TOTP Routes
            Route::name('totp.')->prefix('totp')->group(function () {
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enableTotp');
                Route::post('update')->name('update')->uses('TwoFactorSettingsController@updateTotp');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disableTotp');
                Route::get('backup')->name('backup')->uses('TwoFactorSettingsController@backupTotp');
            });

            // Two-Factor Phone Routes
            Route::name('phone.')->prefix('phone')->group(function () {
                Route::get('enable')->name('enable')->uses('TwoFactorSettingsController@enablePhone');
                Route::get('disable')->name('disable')->uses('TwoFactorSettingsController@disablePhone');
            });
        });
    });

    // Password Reset Routes
    Route::name('passwordreset.')->prefix('passwordreset')->group(function () {
        Route::get('request')->name('request')->uses('PasswordResetController@request');
        Route::post('send')->name('send')->uses('PasswordResetController@send');
        Route::get('reset')->name('reset')->uses('PasswordResetController@reset');
        Route::post('process')->name('process')->uses('PasswordResetController@process');
    });

    // Verification Routes
    Route::name('verification.')->prefix('verification')->group(function () {

        // Phone Verification Routes
        Route::name('phone.')->prefix('phone')->group(function () {
            Route::get('request')->name('request')->uses('PhoneVerificationController@request');
            Route::post('send')->name('send')->uses('PhoneVerificationController@send');
            Route::get('verify')->name('verify')->uses('PhoneVerificationController@verify');
            Route::post('process')->name('process')->uses('PhoneVerificationController@process');
        });

        // Email Verification Routes
        Route::name('email.')->prefix('email')->group(function () {
            Route::get('request')->name('request')->uses('EmailVerificationController@request');
            Route::post('send')->name('send')->uses('EmailVerificationController@send');
            Route::get('verify')->name('verify')->uses('EmailVerificationController@verify');
        });
    });
     });
