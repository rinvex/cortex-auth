{{-- Master Layout --}}
@extends('cortex/foundation::frontarea.layouts.default')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('cortex/fort::common.settings') }}
@endsection

@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Fort\Http\Requests\Frontarea\AccountSettingsRequest::class)->selector('#frontarea-account-settings-form') !!}

    <script>
        window.countries = {!! $countries !!};
        window.selectedCountry = '{{ old('country_code', $currentUser->country_code) }}';
    </script>
@endpush

{{-- Main Content --}}
@section('content')

    <div class="container">
        <div class="row profile">
            <div class="col-md-3">
                <div class="profile-sidebar">
                    <div class="profile-usertitle">
                        <div class="profile-usertitle-name">
                            {{ $currentUser->name ?: $currentUser->username }}
                        </div>
                        @if($currentUser->title)
                            <div class="profile-usertitle-job">
                                {{ $currentUser->title }}
                            </div>
                        @endif
                    </div>
                    <div class="profile-usermenu">
                        {!! Menu::render('frontarea.user.sidebar', 'user.sidebar', []) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="profile-content">

                    {{ Form::model($currentUser, ['url' => route('frontarea.account.settings.update'), 'id' => 'frontarea-account-settings-form']) }}

                        <!-- Tab panes -->
                        <div class="tab-content">

                            <div role="tabpanel" class="tab-pane active" id="settings-tab">

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                            {{ Form::label('email', trans('cortex/fort::common.email')) }}

                                            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.email'), 'required' => 'required', 'autofocus' => 'autofocus']) }}

                                            @if ($currentUser->email_verified)
                                                <small class="text-success">{!! trans('cortex/fort::common.email_verified', ['date' => $currentUser->email_verified_at]) !!}</small>
                                            @elseif($currentUser->email)
                                                <small class="text-danger">{!! trans('cortex/fort::common.email_unverified', ['href' => route('frontarea.verification.email.request')]) !!}</small>
                                            @endif

                                            @if ($errors->has('email'))
                                                <span class="help-block">{{ $errors->first('email') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                            {{ Form::label('username', trans('cortex/fort::common.username')) }}

                                            {{ Form::text('username', null, ['class' => 'form-control', 'placeholder' => $currentUser->username, 'required' => 'required']) }}

                                            @if ($errors->has('username'))
                                                <span class="help-block">{{ $errors->first('username') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <hr />

                                <div class="row">

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
                                            {{ Form::label('first_name', trans('cortex/fort::common.first_name')) }}

                                            {{ Form::text('first_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->first_name ?: trans('cortex/fort::common.first_name')]) }}

                                            @if ($errors->has('first_name'))
                                                <span class="help-block">{{ $errors->first('first_name') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('middle_name') ? ' has-error' : '' }}">
                                            {{ Form::label('middle_name', trans('cortex/fort::common.middle_name')) }}

                                            {{ Form::text('middle_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->middle_name ?: trans('cortex/fort::common.middle_name')]) }}

                                            @if ($errors->has('middle_name'))
                                                <span class="help-block">{{ $errors->first('middle_name') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
                                            {{ Form::label('last_name', trans('cortex/fort::common.last_name')) }}

                                            {{ Form::text('last_name', null, ['class' => 'form-control', 'placeholder' => $currentUser->last_name ?: trans('cortex/fort::common.last_name')]) }}

                                            @if ($errors->has('last_name'))
                                                <span class="help-block">{{ $errors->first('last_name') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('name_prefix') ? ' has-error' : '' }}">
                                            {{ Form::label('name_prefix', trans('cortex/fort::common.name_prefix')) }}

                                            {{ Form::text('name_prefix', null, ['class' => 'form-control', 'placeholder' => $currentUser->name_prefix ?: trans('cortex/fort::common.name_prefix')]) }}

                                            @if ($errors->has('name_prefix'))
                                                <span class="help-block">{{ $errors->first('name_prefix') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('name_suffix') ? ' has-error' : '' }}">
                                            {{ Form::label('name_suffix', trans('cortex/fort::common.name_suffix')) }}

                                            {{ Form::text('name_suffix', null, ['class' => 'form-control', 'placeholder' => $currentUser->name_suffix ?: trans('cortex/fort::common.name_suffix')]) }}

                                            @if ($errors->has('name_suffix'))
                                                <span class="help-block">{{ $errors->first('name_suffix') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-4">

                                        <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                                            {{ Form::label('title', trans('cortex/fort::common.title')) }}

                                            {{ Form::text('title', null, ['class' => 'form-control', 'placeholder' => $currentUser->title ?: trans('cortex/fort::common.title')]) }}

                                            @if ($errors->has('title'))
                                                <span class="help-block">{{ $errors->first('title') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <hr />

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('gender') ? ' has-error' : '' }}">
                                            {{ Form::label('gender', trans('cortex/fort::common.gender')) }}

                                            {{ Form::hidden('gender') }}
                                            {{ Form::hidden('gender', '') }}
                                            {{ Form::select('gender', $genders, null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/fort::common.select_gender'), 'data-allow-clear' => 'true', 'data-minimum-results-for-search' => 'Infinity', 'data-width' => '100%']) }}

                                            @if ($errors->has('gender'))
                                                <span class="help-block">{{ $errors->first('gender') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group has-feedback{{ $errors->has('birthday') ? ' has-error' : '' }}">
                                            {{ Form::label('birthday', trans('cortex/fort::common.birthday')) }}

                                            {{ Form::text('birthday', null, ['class' => 'form-control datepicker', 'data-auto-update-input' => 'false']) }}

                                            @if ($errors->has('birthday'))
                                                <span class="help-block">{{ $errors->first('birthday') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('country_code') ? ' has-error' : '' }}">
                                            {{ Form::label('country_code', trans('cortex/fort::common.country')) }}

                                            {{ Form::hidden('country_code', '') }}
                                            {{ Form::select('country_code', [], null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/fort::common.select_country'), 'data-allow-clear' => 'true', 'data-width' => '100%']) }}

                                            @if ($errors->has('country_code'))
                                                <span class="help-block">{{ $errors->first('country_code') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group{{ $errors->has('language_code') ? ' has-error' : '' }}">
                                            {{ Form::label('language_code', trans('cortex/fort::common.language')) }}

                                            {{ Form::hidden('language_code', '') }}
                                            {{ Form::select('language_code', $languages, null, ['class' => 'form-control select2', 'placeholder' => trans('cortex/fort::common.select_language'), 'data-allow-clear' => 'true', 'data-width' => '100%']) }}

                                            @if ($errors->has('language_code'))
                                                <span class="help-block">{{ $errors->first('language_code') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <div class="row">

                                    <div class="col-md-12">

                                        <div class="form-group has-feedback{{ $errors->has('phone') ? ' has-error' : '' }}">
                                            {{ Form::label('phone', trans('cortex/fort::common.phone')) }}

                                            {{ Form::number('phone', null, ['class' => 'form-control', 'placeholder' => $currentUser->phone ?: trans('cortex/fort::common.phone')]) }}

                                            @if ($currentUser->phone_verified)
                                                <small class="text-success">{!! trans('cortex/fort::common.phone_verified', ['date' => $currentUser->phone_verified_at]) !!}</small>
                                            @elseif($currentUser->phone)
                                                <small class="text-danger">{!! trans('cortex/fort::common.phone_unverified', ['href' => route('frontarea.verification.phone.request')]) !!}</small>
                                            @endif

                                            @if ($errors->has('phone'))
                                                <span class="help-block">{{ $errors->first('phone') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                                <hr />

                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group has-feedback{{ $errors->has('password') ? ' has-error' : '' }}">
                                            {{ Form::label('password', trans('cortex/fort::common.password')) }}

                                            {{ Form::password('password', ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.password')]) }}
                                            <span class="fa fa-key form-control-feedback"></span>

                                            @if ($errors->has('password'))
                                                <span class="help-block">{{ $errors->first('password') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group has-feedback{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                                            {{ Form::label('password_confirmation', trans('cortex/fort::common.password_confirmation')) }}

                                            {{ Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.password_confirmation')]) }}
                                            <span class="fa fa-key form-control-feedback"></span>

                                            @if ($errors->has('password_confirmation'))
                                                <span class="help-block">{{ $errors->first('password_confirmation') }}</span>
                                            @endif
                                        </div>

                                    </div>

                                </div>

                            </div>

                            <div role="tabpanel" class="tab-pane" id="attributes-tab">

                                @attributes($currentUser)

                            </div>

                            <div class="row">
                                <div class="col-md-12 text-center">
                                    {{ Form::button('<i class="fa fa-user"></i> '.trans('cortex/fort::common.update_basic_info'), ['class' => 'btn btn-primary btn-flat', 'type' => 'submit']) }}
                                </div>
                            </div>

                        </div>

                    {{ Form::close() }}

                </div>
            </div>
        </div>
    </div>

@endsection
