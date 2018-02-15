{{-- Master Layout --}}
@extends('cortex/foundation::adminarea.layouts.auth')

{{-- Page Title --}}
@section('title')
    {{ config('app.name') }} » {{ trans('cortex/fort::common.verification_phone_request') }}
@endsection

{{-- Scripts --}}
@push('inline-scripts')
    {!! JsValidator::formRequest(Cortex\Fort\Http\Requests\Adminarea\PhoneVerificationSendRequest::class)->selector('#adminarea-verification-phone-request-form') !!}
@endpush

{{-- Main Content --}}
@section('content')

    <div class="login-box">
        <div class="login-logo">
            <a href="{{ route('adminarea.home') }}"><b>{{ config('app.name') }}</b></a>
        </div>

        <div class="login-box-body">
            <p class="login-box-msg">{{ trans('cortex/fort::common.account_verification_phone') }}</p>

            {{ Form::open(['url' => route('adminarea.verification.phone.send'), 'id' => 'adminarea-verification-phone-request-form', 'role' => 'auth']) }}

                <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
                    <div class="input-group input-group-lg">
                        @if (auth()->guard(request('guard'))->user())
                            {{ Form::number('phone', old('phone', $currentUser->phone), ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.phone'), 'required' => 'required', 'autofocus' => 'autofocus', 'disabled' => 'disabled']) }}
                            {{ Form::hidden('phone', old('phone', $currentUser->phone)) }}
                        @else
                            {{ Form::number('phone', old('phone'), ['class' => 'form-control', 'placeholder' => trans('cortex/fort::common.phone'), 'required' => 'required', 'autofocus' => 'autofocus']) }}
                        @endif

                        <div class="input-group-btn" data-toggle="buttons">
                            <label for="sms" class="btn btn-default @if(! old('method') || old('method') === 'sms') active @endif">
                                <input style="margin: 0 !important;" id="sms" name="method" type="radio" value="sms" autocomplete="off" @if(! old('method') || old('method') == 'sms') checked @endif> {{ trans('cortex/fort::common.sms') }}
                            </label>
                            <label for="call" class="btn btn-default @if(old('method') === 'call') active @endif">
                                <input style="margin: 0 !important;" id="call" name="method" type="radio" value="call" autocomplete="off" @if(old('method') === 'call') checked @endif> {{ trans('cortex/fort::common.call') }}
                            </label>
                        </div>
                    </div>

                    @if ($errors->has('phone'))
                        <span class="help-block">{{ $errors->first('phone') }}</span>
                    @endif
                </div>

                {{ Form::button('<i class="fa fa-phone"></i> '.trans('cortex/fort::common.verification_phone_request'), ['class' => 'btn btn-lg btn-primary btn-block', 'type' => 'submit']) }}

            {{ Form::close() }}

            {{ Html::link(route('adminarea.login'), trans('cortex/fort::common.account_login')) }}

        </div>

    </div>

@endsection