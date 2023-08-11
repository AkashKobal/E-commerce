@extends('backend.layouts.app')

@section('content')
    @if(addon_is_activated('african_pg'))
        <div class="row">
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 h6 text-center">{{translate('MPesa Activation')}}</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="clearfix">
                            <img class="float-left" src="{{ url ('/assets/img/cards/mpesa.png') }}" height="30">
                            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                                <input type="checkbox" onchange="updateSettings(this, 'mpesa')" <?php if(\App\Models\BusinessSetting::where('type', 'mpesa')->first()->value == 1) echo "checked";?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="alert" style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                            {{ translate('You need to configure Mpesa correctly to enable this feature') }}. <a href="{{ route('payment_method.index') }}">{{ translate('Configure Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 h6 text-center">{{translate('flutterwave Activation')}}</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="clearfix">
                            <img class="float-left" src="{{ url ('/assets/img/cards/flutterwave.png') }}" height="30">
                            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                                <input type="checkbox" onchange="updateSettings(this, 'flutterwave')" <?php if(\App\Models\BusinessSetting::where('type', 'flutterwave')->first()->value == 1) echo "checked";?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="alert" style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                            {{ translate('You need to configure flutterwave correctly to enable this feature') }}. <a href="{{ route('payment_method.index') }}">{{ translate('Configure Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="mb-0 h6 text-center">{{translate('Payfast Activation')}}</h3>
                    </div>
                    <div class="card-body text-center">
                        <div class="clearfix">
                            <img class="float-left" src="{{ url ('/assets/img/cards/payfast.png') }}" height="30">
                            <label class="aiz-switch aiz-switch-success mb-0 float-right">
                                <input type="checkbox" onchange="updateSettings(this, 'payfast')" <?php if(\App\Models\BusinessSetting::where('type', 'payfast')->first()->value == 1) echo "checked";?>>
                                <span class="slider round"></span>
                            </label>
                        </div>
                        <div class="alert" style="color: #004085;background-color: #cce5ff;border-color: #b8daff;margin-bottom:0;margin-top:10px;">
                            {{ translate('You need to configure payfast correctly to enable this feature') }}. <a href="{{ route('payment_method.index') }}">{{ translate('Configure Now') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endsection

@section('script')
    <script type="text/javascript">
        function updateSettings(el, type){
            if($(el).is(':checked')){
                var value = 1;
            }
            else{
                var value = 0;
            }
            $.post('{{ route('business_settings.update.activation') }}', {_token:'{{ csrf_token() }}', type:type, value:value}, function(data){
                if(data == '1'){
                    AIZ.plugins.notify('success', 'Settings updated successfully');
                }
                else{
                    AIZ.plugins.notify('danger', 'Something went wrong');
                }
            });
        }
    </script>
@endsection
