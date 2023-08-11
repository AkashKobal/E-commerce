@extends('backend.layouts.app')

@section('content')

<div class="row">
    @if(addon_is_activated('african_pg'))
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0 h6">{{translate('Mpesa Credential')}}</h5>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="mpesa">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_CONSUMER_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA CONSUMER KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_CONSUMER_KEY" value="{{  env('MPESA_CONSUMER_KEY') }}" placeholder="{{ translate('MPESA_CONSUMER_KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_CONSUMER_SECRET">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA CONSUMER SECRET')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_CONSUMER_SECRET" value="{{  env('MPESA_CONSUMER_SECRET') }}" placeholder="{{ translate('MPESA_CONSUMER_SECRET') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_SHORT_CODE">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA SHORT CODE')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_SHORT_CODE" value="{{  env('MPESA_SHORT_CODE') }}" placeholder="{{ translate('MPESA_SHORT_CODE') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_USERNAME">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA USERNAME')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_USERNAME" value="{{  env('MPESA_USERNAME') }}" placeholder="{{ translate('MPESA_USERNAME') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_PASSWORD">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA PASSWORD')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_PASSWORD" value="{{  env('MPESA_PASSWORD') }}" placeholder="{{ translate('MPESA_PASSWORD') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_PASSKEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA PASSKEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="MPESA_PASSKEY" value="{{  env('MPESA_PASSKEY') }}" placeholder="{{ translate('MPESA_PASSKEY') }}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="MPESA_ENV">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('MPESA SANDBOX ACTIVATION')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <select name="MPESA_ENV" class="form-control aiz-selectpicker" required>
                                    @if(env('MPESA_ENV') == 'sandbox')
                                        <option value="live">live</option>
                                        <option value="sandbox" selected>sandbox</option>
                                    @else
                                        <option value="live" selected>live</option>
                                        <option value="sandbox">sandbox</option>
                                    @endif
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('Flutterwave Credential')}}</h3>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="flutterwave">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="FLW_PUBLIC_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('FLW_PUBLIC_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="FLW_PUBLIC_KEY" value="{{  env('FLW_PUBLIC_KEY') }}" placeholder="{{ translate('FLW_PUBLIC_KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="FLW_SECRET_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('FLW_SECRET_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="FLW_SECRET_KEY" value="{{  env('FLW_SECRET_KEY') }}" placeholder="{{ translate('FLW_SECRET_KEY') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="FLW_SECRET_HASH">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('FLW_SECRET_HASH')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="FLW_SECRET_HASH" value="{{  env('FLW_SECRET_HASH') }}" placeholder="{{ translate('FLW_SECRET_HASH') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="FLW_PAYMENT_CURRENCY_CODE">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('FLW_PAYMENT_CURRENCY_CODE')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="FLW_PAYMENT_CURRENCY_CODE" value="{{  env('FLW_PAYMENT_CURRENCY_CODE') }}" placeholder="{{ translate('FLW_PAYMENT_CURRENCY_CODE') }}" required>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0 h6">{{translate('PAYFAST Credential')}}</h3>
                </div>
                <div class="card-body">
                    <form class="form-horizontal" action="{{ route('payment_method.update') }}" method="POST">
                        @csrf
                        <input type="hidden" name="payment_method" value="payfast">
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYFAST_MERCHANT_ID">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('PAYFAST_MERCHANT_ID')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="PAYFAST_MERCHANT_ID" value="{{  env('PAYFAST_MERCHANT_ID') }}" placeholder="{{ translate('PAYFAST_MERCHANT_ID') }}" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <input type="hidden" name="types[]" value="PAYFAST_MERCHANT_KEY">
                            <div class="col-lg-4">
                                <label class="col-from-label">{{translate('PAYFAST_MERCHANT_KEY')}}</label>
                            </div>
                            <div class="col-lg-8">
                                <input type="text" class="form-control" name="PAYFAST_MERCHANT_KEY" value="{{  env('PAYFAST_MERCHANT_KEY') }}" placeholder="{{ translate('PAYFAST_MERCHANT_KEY') }}" required>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-from-label">{{translate('PAYFAST Sandbox Mode')}}</label>
                            </div>
                            <div class="col-md-8">
                                <label class="aiz-switch aiz-switch-success mb-0">
                                    <input value="1" name="payfast_sandbox" type="checkbox" @if (\App\Models\BusinessSetting::where('type', 'payfast_sandbox')->first()->value == 1)
                                        checked
                                    @endif>
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                        <div class="form-group mb-0 text-right">
                            <button type="submit" class="btn btn-sm btn-primary">{{translate('Save')}}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection
