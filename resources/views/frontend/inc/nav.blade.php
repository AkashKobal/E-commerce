<style>
    @media (min-width: 768px) {

        .h-md-40px,
        .size-md-40px {
            height: 80px;
        }
    }

    .nav-bar-cat {
        display: none;
        position: absolute;
        width: 100%;
        height: 157px;
        background: white;
        left: 0%;
    }

    .nav-bar-cat-menu {
        display: none;
        position: absolute;
        width: 100%;
    }

    .dropdown-cat:hover .nav-bar-cat {
        display: flex;
    }
</style>

<body>
    <script>
        window.addEventListener('mouseover', initLandbot, {
            once: true
        });
        window.addEventListener('touchstart', initLandbot, {
            once: true
        });
        var myLandbot;

        function initLandbot() {
            if (!myLandbot) {
                var s = document.createElement('script');
                s.type = 'text/javascript';
                s.async = true;
                s.addEventListener('load', function() {
                    var myLandbot = new Landbot.Livechat({
                        configUrl: 'https://storage.googleapis.com/landbot.online/v3/H-1647110-S8IMQMZ8CDV9QJU7/index.json',
                    });
                });
                s.src = 'https://cdn.landbot.io/landbot-3/landbot-3.0.0.js';
                var x = document.getElementsByTagName('script')[0];
                x.parentNode.insertBefore(s, x);
            }
        }
    </script>
</body>


@if (get_setting('topbar_banner') != null)
    <div class="position-relative top-banner removable-session z-1035 d-none" data-key="top-banner" data-value="removed">
        <a href="{{ get_setting('topbar_banner_link') }}" class="d-block text-reset">
            <img src="{{ uploaded_asset(get_setting('topbar_banner')) }}" class="w-100 mw-100 h-50px h-lg-auto img-fit">
        </a>
        <button class="btn text-white absolute-top-right set-session" data-key="top-banner" data-value="removed"
            data-toggle="remove-parent" data-parent=".top-banner">
            <i class="la la-close la-2x"></i>
        </button>
    </div>
@endif
<!-- Top Bar -->
<div class="top-navbar bg-white border-bottom border-soft-secondary z-1035">
    <div class="container">
        <div class="row">
            <div class="col-lg-7 col">
                <ul class="list-inline d-flex justify-content-between justify-content-lg-start mb-0">
                    @if (get_setting('show_language_switcher') == 'on')
                        <li class="list-inline-item dropdown mr-3" id="lang-change">
                            @php
                                if (Session::has('locale')) {
                                    $locale = Session::get('locale', Config::get('app.locale'));
                                } else {
                                    $locale = 'en';
                                }
                            @endphp
                            <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2" data-toggle="dropdown"
                                data-display="static">
                                <img src="{{ url('/assets/img/placeholder.jpg') }}"
                                    data-src="{{ url('/assets/img/flags/' . $locale . '.png') }}" class="mr-2 lazyload"
                                    alt="{{ \App\Models\Language::where('code', $locale)->first()->name }}"
                                    height="11">
                                <span
                                    class="opacity-60">{{ \App\Models\Language::where('code', $locale)->first()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-left">
                                @foreach (\App\Models\Language::all() as $key => $language)
                                    <li>
                                        <a href="javascript:void(0)" data-flag="{{ $language->code }}"
                                            class="dropdown-item @if ($locale == $language) active @endif">
                                            <img src="{{ url('/assets/img/placeholder.jpg') }}"
                                                data-src="{{ url('/assets/img/flags/' . $language->code . '.png') }}"
                                                class="mr-1 lazyload" alt="{{ $language->name }}" height="11">
                                            <span class="language">{{ $language->name }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif

                    @if (get_setting('show_currency_switcher') == 'on')
                        <li class="list-inline-item dropdown ml-auto ml-lg-0 mr-0" id="currency-change">
                            @php
                                if (Session::has('currency_code')) {
                                    $currency_code = Session::get('currency_code');
                                } else {
                                    $currency_code = \App\Models\Currency::findOrFail(get_setting('system_default_currency'))->code;
                                }
                            @endphp
                            <a href="javascript:void(0)" class="dropdown-toggle text-reset py-2 opacity-60"
                                data-toggle="dropdown" data-display="static">
                                {{ \App\Models\Currency::where('code', $currency_code)->first()->name }}
                                {{ \App\Models\Currency::where('code', $currency_code)->first()->symbol }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-right dropdown-menu-lg-left">
                                @foreach (\App\Models\Currency::where('status', 1)->get() as $key => $currency)
                                    <li>
                                        <a class="dropdown-item @if ($currency_code == $currency->code) active @endif"
                                            href="javascript:void(0)"
                                            data-currency="{{ $currency->code }}">{{ $currency->name }}
                                            ({{ $currency->symbol }})
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="col-5 text-right d-none d-lg-block">
                <ul class="list-inline mb-0 h-100 d-flex justify-content-end align-items-center">
                    @if (get_setting('helpline_number'))
                        <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                            <a href="tel:{{ get_setting('helpline_number') }}"
                                class="text-reset d-inline-block opacity-60 py-2">
                                <i class="la la-phone"></i>
                                <span>{{ translate('Help line') }}</span>
                                <span>{{ get_setting('helpline_number') }}</span>
                            </a>
                        </li>
                    @endif
                    @auth
                        @if (isAdmin())
                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                                <a href="{{ route('admin.dashboard') }}"
                                    class="text-reset d-inline-block  h6 font-weight-boldpy-2">{{ translate('My Panel') }}</a>

                            </li>
                        @else
                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0 dropdown">
                                <a class="dropdown-toggle no-arrow text-reset" data-toggle="dropdown"
                                    href="javascript:void(0);" role="button" aria-haspopup="false" aria-expanded="false">
                                    <span class="">
                                        <span class="position-relative d-inline-block">
                                            <i class="las la-bell fs-18"></i>
                                            @if (count(Auth::user()->unreadNotifications) > 0)
                                                <span
                                                    class="badge badge-sm badge-dot badge-circle badge-primary position-absolute absolute-top-right"></span>
                                            @endif
                                        </span>
                                    </span>
                                </a>
                                <div class="dropdown-menu dropdown-menu-right dropdown-menu-lg py-0">
                                    <div class="p-3 bg-light border-bottom">
                                        <h6 class="mb-0">{{ translate('Notifications') }}</h6>
                                    </div>
                                    <div class="px-3 c-scrollbar-light overflow-auto " style="max-height:300px;">
                                        <ul class="list-group list-group-flush">
                                            @forelse(Auth::user()->unreadNotifications as $notification)
                                                <li class="list-group-item">
                                                    @if ($notification->type == 'App\Notifications\OrderNotification')
                                                        @if (Auth::user()->user_type == 'customer')
                                                            <a href="javascript:void(0)"
                                                                onclick="show_purchase_history_details({{ $notification->data['order_id'] }})"
                                                                class="text-reset">
                                                                <span class="ml-2">
                                                                    {{ translate('Order code: ') }}
                                                                    {{ $notification->data['order_code'] }}
                                                                    {{ translate('has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                                                </span>
                                                            </a>
                                                        @elseif (Auth::user()->user_type == 'seller')
                                                            @if (Auth::user()->id == $notification->data['user_id'])
                                                                <a href="javascript:void(0)"
                                                                    onclick="show_purchase_history_details({{ $notification->data['order_id'] }})"
                                                                    class="text-reset">
                                                                    <span class="ml-2">
                                                                        {{ translate('Order code: ') }}
                                                                        {{ $notification->data['order_code'] }}
                                                                        {{ translate('has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                                                    </span>
                                                                </a>
                                                            @else
                                                                <a href="javascript:void(0)"
                                                                    onclick="show_order_details({{ $notification->data['order_id'] }})"
                                                                    class="text-reset">
                                                                    <span class="ml-2">
                                                                        {{ translate('Order code: ') }}
                                                                        {{ $notification->data['order_code'] }}
                                                                        {{ translate('has been ' . ucfirst(str_replace('_', ' ', $notification->data['status']))) }}
                                                                    </span>
                                                                </a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </li>
                                            @empty
                                                <li class="list-group-item">
                                                    <div class="py-4 text-center fs-16">
                                                        {{ translate('No notification found') }}
                                                    </div>
                                                </li>
                                            @endforelse
                                        </ul>
                                    </div>
                                    <div class="text-center border-top">
                                        <a href="{{ route('all-notifications') }}" class="text-reset d-block py-2">
                                            {{ translate('View All Notifications') }}
                                        </a>
                                    </div>
                                </div>
                            </li>

                            <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                                <a href="{{ route('dashboard') }}"
                                    class="text-resett d-inline-block h6 font-weight-boldpy-2">{{ translate('My Panel') }}
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">
                                        <g style="display:block">
                                            <path fill="none" stroke="#07A889" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="12.6"
                                                d="m302.17 410.289-19.938-17.164-7.594-29.05h-49.276l-7.595 29.05-19.937 17.164h104.34z" />
                                            <path fill="none" stroke="#121330" stroke-linecap="round"
                                                stroke-linejoin="round" stroke-width="12.6"
                                                d="M412.097 305.162H87.903V116.078h324.194v189.084zm6.476 59.01H81.427c-10.729 0-19.427-8.698-19.427-19.427V109.601c0-10.729 8.698-19.427 19.427-19.427h337.146c10.729 0 19.427 8.698 19.427 19.427v235.144c0 10.729-8.698 19.427-19.427 19.427z" />
                                            <path fill="#07A889"
                                                d="M261.549 334.764c0 6.378-5.171 11.549-11.549 11.549-6.378 0-11.549-5.171-11.549-11.549 0-6.378 5.171-11.549 11.549-11.549 6.378 0 11.549 5.171 11.549 11.549z" />
                                        </g>
                                    </svg>
                                </a>
                            </li>
                        @endif
                        <li class="list-inline-item">
                            <a href="{{ route('logout') }}"
                                class="text-resettt d-inline-block h6 font-weight-boldpy-2">{{ translate('Logout') }}<svg
                                    version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512.001 512.001"
                                    xml:space="preserve">
                                    <path style="fill:#A9A8AE;"
                                        d="M488.001,472.001h-304c-13.254,0-24-10.746-24-24V352c0-13.256,10.746-24,24-24s24,10.744,24,24
                               v72.002h256V88h-256v72c0,13.254-10.746,24-24,24s-24-10.746-24-24V64c0-13.256,10.746-24,24-24h304c13.254,0,24,10.744,24,24
                               v384.002C512.001,461.256,501.256,472.001,488.001,472.001z" />
                                    <path style="fill:#64C37D;"
                                        d="M359.995,232.001H81.939l31.03-31.03c9.373-9.373,9.373-24.568,0-33.941
                               c-9.371-9.373-24.57-9.373-33.941,0l-72,72c-0.005,0.005-0.008,0.01-0.014,0.014c-0.554,0.555-1.078,1.138-1.574,1.744
                               c-0.23,0.282-0.43,0.578-0.648,0.867c-0.251,0.338-0.514,0.667-0.749,1.018c-0.232,0.347-0.432,0.706-0.645,1.062
                               c-0.189,0.317-0.387,0.626-0.562,0.952c-0.195,0.363-0.36,0.736-0.534,1.107c-0.16,0.339-0.33,0.672-0.474,1.019
                               c-0.15,0.363-0.272,0.733-0.403,1.101c-0.133,0.371-0.275,0.736-0.389,1.115c-0.112,0.371-0.197,0.749-0.291,1.125
                               c-0.096,0.384-0.203,0.762-0.282,1.152c-0.088,0.438-0.139,0.88-0.202,1.322c-0.046,0.334-0.11,0.662-0.146,1.002
                               c-0.155,1.578-0.155,3.166,0,4.744c0.034,0.342,0.099,0.675,0.147,1.013c0.062,0.437,0.114,0.875,0.2,1.309
                               c0.078,0.395,0.187,0.778,0.285,1.165c0.094,0.371,0.174,0.744,0.286,1.112c0.117,0.382,0.261,0.754,0.395,1.128
                               c0.13,0.365,0.25,0.73,0.397,1.088c0.147,0.352,0.318,0.69,0.482,1.032c0.171,0.366,0.334,0.736,0.525,1.094
                               c0.178,0.331,0.381,0.646,0.571,0.968c0.21,0.35,0.408,0.704,0.635,1.045c0.242,0.362,0.509,0.701,0.768,1.046
                               c0.211,0.28,0.405,0.566,0.627,0.838c0.504,0.613,1.034,1.205,1.597,1.765l71.995,71.998c4.686,4.686,10.829,7.03,16.97,7.03
                               c6.141-0.002,12.285-2.344,16.971-7.03c9.373-9.373,9.373-24.568,0-33.941l-31.03-31.032h278.056c13.254,0,24-10.746,24-24
                               C383.995,242.745,373.251,232.001,359.995,232.001z" />
                                </svg></a>
                        </li>
                    @else
                        <li class="list-inline-item mr-3 border-right border-left-0 pr-3 pl-0">
                            <a href="{{ route('user.login') }}"
                                class="text-resettt d-inline-block h6 font-weight-boldpy-2">{{ translate('Login') }}
                                <svg class="login-icon" version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                    xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"
                                    xml:space="preserve">
                                    <path style="fill:#64C37D;"
                                        d="M379.955,269.328c0.229-0.341,0.427-0.696,0.635-1.046c0.192-0.322,0.394-0.634,0.57-0.965
                               c0.194-0.362,0.357-0.731,0.53-1.099c0.162-0.342,0.333-0.678,0.477-1.029c0.15-0.36,0.27-0.728,0.4-1.093
                               c0.134-0.373,0.278-0.741,0.394-1.123c0.112-0.37,0.194-0.744,0.288-1.118c0.098-0.386,0.205-0.766,0.283-1.16
                               c0.086-0.437,0.139-0.875,0.202-1.315c0.046-0.336,0.11-0.666,0.144-1.006c0.157-1.578,0.157-3.166,0-4.744
                               c-0.034-0.341-0.098-0.672-0.146-1.01c-0.061-0.437-0.114-0.877-0.2-1.31c-0.078-0.395-0.186-0.776-0.285-1.165
                               c-0.093-0.371-0.174-0.744-0.286-1.112c-0.115-0.384-0.261-0.755-0.395-1.131c-0.13-0.362-0.248-0.726-0.397-1.083
                               c-0.147-0.355-0.32-0.694-0.483-1.04c-0.171-0.363-0.333-0.73-0.523-1.086c-0.179-0.336-0.386-0.656-0.579-0.982
                               c-0.206-0.344-0.402-0.694-0.627-1.03c-0.245-0.368-0.52-0.715-0.786-1.067c-0.205-0.272-0.394-0.552-0.61-0.818
                               c-0.506-0.614-1.037-1.206-1.6-1.768l-71.994-71.995c-9.37-9.373-24.566-9.373-33.941,0c-9.373,9.373-9.373,24.568,0,33.941
                               l31.032,31.032H24c-13.254,0-24,10.744-24,24c0,13.254,10.746,24,24,24h278.054l-31.029,31.029c-9.373,9.373-9.373,24.568,0,33.941
                               c4.686,4.686,10.829,7.03,16.97,7.03c6.142,0,12.285-2.342,16.97-7.03l71.997-71.995c0.56-0.56,1.091-1.15,1.594-1.765
                               c0.226-0.272,0.419-0.56,0.629-0.838C379.445,270.027,379.714,269.688,379.955,269.328z" />
                                    <path style="fill:#A9A8AE;"
                                        d="M488,472H184c-13.254,0-24-10.746-24-24v-96.002c0-13.256,10.746-24,24-24s24,10.744,24,24V424h256
                               V88H208v72c0,13.254-10.746,24-24,24s-24-10.746-24-24V64c0-13.256,10.746-24,24-24h304c13.254,0,24,10.744,24,24v384
                               C512,461.256,501.254,472,488,472z" />
                                </svg></a>
                        </li>
                        <li class="list-inline-item">
                            <a href="{{ route('user.registration') }}"
                                class="text-resett d-inline-block h6 font-weight-boldpy-2">{{ translate('Registration') }}
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 500">
                                    <g fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75"
                                        clip-path="url(#m)" style="display:block">
                                        <path stroke="#121330"
                                            d="M8.677 31.836h-21.21l.066-2.067c0-7.742 6.277-14.019 14.019-14.019h16.031c7.742 0 14.019 6.277 14.019 14.019l-.066 2.067s0 0 0 0H8.677"
                                            style="display:block" transform="matrix(6.97 0 0 6.97 183.238 172.833)" />
                                        <path stroke="#07A889"
                                            d="M15.673-9.749a10.107 10.107 0 0 1 2.373 6.522s0 0 0 0c0 5.604-4.544 10.148-10.148 10.148S-2.25 2.377-2.25-3.227 2.293-13.375 7.898-13.375c3.121 0 5.913 1.409 7.775 3.626"
                                            style="display:block" transform="matrix(6.97 0 0 6.97 194.878 195.484)" />
                                    </g>
                                </svg>
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </div>
</div>
<!-- END Top Bar -->
<header class="@if (get_setting('header_stikcy') == 'on') sticky-top @endif z-1020 bg-white border-bottom shadow-sm">
    <div class="position-relative logo-bar-area z-1">
        <div class="container">
            <div class="d-flex align-items-center">

                <div class="col-auto col-xl-3 pl-0 pr-3 d-flex align-items-center">
                    <a class="d-block py-20px mr-3 ml-0" href="{{ route('home') }}">
                        @php
                            $header_logo = get_setting('header_logo');
                        @endphp
                        @if ($header_logo != null)
                            <img src="{{ uploaded_asset($header_logo) }}" alt="{{ env('APP_NAME') }}"
                                class="mw-100 h-30px h-md-40px" height="40" style="height: 55px;">
                        @else
                            <img src="{{ url('/assets/img/logo.png') }}" alt="{{ env('APP_NAME') }}"
                                class="mw-100 h-30px h-md-40px" height="40" style="height: 55px;">
                        @endif
                    </a>

                    @if (Route::currentRouteName() != 'home')
                        <div class="d-none d-xl-block align-self-stretch category-menu-icon-box ml-auto mr-0">
                            <div class="h-100 d-flex align-items-center" id="category-menu-icon">
                                <div
                                    class="dropdown-toggle navbar-light bg-light h-40px w-50px pl-2 rounded border c-pointer">
                                    <span class="navbar-toggler-icon"></span>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="d-lg-none ml-auto mr-0">
                    <a class="p-2 d-block text-reset" href="javascript:void(0);" data-toggle="class-toggle"
                        data-target=".front-header-search">
                        <i class="las la-search la-flip-horizontal la-2x"></i>
                    </a>
                    <button class="voice-assistant-mobile" onclick="record()"><svg class ="mic-mobile"  fill="#000000" width="24px"
                            height="39px" viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg">
                            <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                            <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                            <g id="SVGRepo_iconCarrier">
                                <path
                                    d="M960.315 96.818c-186.858 0-338.862 152.003-338.862 338.861v484.088c0 186.858 152.004 338.862 338.862 338.862 186.858 0 338.861-152.004 338.861-338.862V435.68c0-186.858-152.003-338.861-338.861-338.861M427.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H669.862v-96.818h242.043l-.004-252.3C587.438 1546.077 331 1274.116 331 943.41V709.983h96.818ZM960.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C524.635 195.475 720.11 0 960.315 0Z"
                                    fill-rule="evenodd"></path>
                            </g>
                        </svg>

                    </button>
                </div>

                <div class="flex-grow-1 front-header-search d-flex align-items-center bg-white">
                    <div class="position-relative flex-grow-1">
                        <form action="{{ route('search') }}" method="GET" class="stop-propagation">
                            <div class="d-flex position-relative align-items-center">
                                <div class="d-lg-none" data-toggle="class-toggle" data-target=".front-header-search">
                                    <button class="btn px-2" type="button"><i
                                            class="la la-2x la-long-arrow-left"></i></button>
                                </div>
                                <div class="input-group">

                                    {{-- Voice assistant  --}}

                                    <input type="text" class="border-0 border-lg form-control" id="speechToText"
                                        placeholder="I am shopping for..." name="keyword"
                                        @isset($query)
                                        value="{{ $query }}"
                                    @endisset
                                        placeholder="{{ translate('I am shopping for...') }}" autocomplete="off">
                                    {{-- <input type="text" class="border-0 border-lg form-control" id="search"
                                        name="keyword"
                                        @isset($query)
                                        value="{{ $query }}"
                                    @endisset
                                        placeholder="{{ translate('I am shopping for...') }}" autocomplete="off"> --}}
                                    <div class="input-group-append d-none d-lg-block">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="la la-search la-flip-horizontal fs-18"></i>
                                            <label for="Speech Recognition">

                                                <button class="voice-assistant" onclick="record()"><svg
                                                        class="mic" fill="#ffffff" width="35px" height="39px"
                                                        viewBox="0 0 1920 1920" xmlns="http://www.w3.org/2000/svg"
                                                        stroke="#ffffff">
                                                        <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                                                        <g id="SVGRepo_tracerCarrier" stroke-linecap="round"
                                                            stroke-linejoin="round"></g>
                                                        <g id="SVGRepo_iconCarrier">
                                                            <path
                                                                d="M960.315 96.818c-186.858 0-338.862 152.003-338.862 338.861v484.088c0 186.858 152.004 338.862 338.862 338.862 186.858 0 338.861-152.004 338.861-338.862V435.68c0-186.858-152.003-338.861-338.861-338.861M427.818 709.983V943.41c0 293.551 238.946 532.497 532.497 532.497 293.55 0 532.496-238.946 532.496-532.497V709.983h96.818V943.41c0 330.707-256.438 602.668-580.9 627.471l-.006 252.301h242.044V1920H669.862v-96.818h242.043l-.004-252.3C587.438 1546.077 331 1274.116 331 943.41V709.983h96.818ZM960.315 0c240.204 0 435.679 195.475 435.679 435.68v484.087c0 240.205-195.475 435.68-435.68 435.68-240.204 0-435.679-195.475-435.679-435.68V435.68C524.635 195.475 720.11 0 960.315 0Z"
                                                                fill-rule="evenodd"></path>
                                                        </g>
                                                    </svg>

                                                </button>


                                            </label>

                                            <!-- Below is the script for voice recognition and conversion to text-->
                                            <script>
                                                function record() {
                                                    var recognition = new webkitSpeechRecognition();
                                                    recognition.lang = "en-GB";

                                                    recognition.onresult = function(event) {
                                                        // console.log(event);
                                                        document.getElementById('speechToText').value = event.results[0][0].transcript;
                                                    }
                                                    recognition.start();

                                                }
                                            </script>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="typed-search-box stop-propagation document-click-d-none d-none bg-white rounded shadow-lg position-absolute left-0 top-100 w-100"
                            style="min-height: 200px">
                            <div class="search-preloader absolute-top-center">
                                <div class="dot-loader">
                                    <div></div>
                                    <div></div>
                                    <div></div>
                                </div>
                            </div>
                            <div class="search-nothing d-none p-3 text-center fs-16">

                            </div>
                            <div id="search-content" class="text-left">

                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-none d-lg-none ml-3 mr-0">
                    <div class="nav-search-box">
                        <a href="#" class="nav-box-link">
                            <i class="la la-search la-flip-horizontal d-inline-block nav-box-icon"></i>
                        </a>
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="compare">
                        @include('frontend.partials.compare')
                    </div>
                </div>

                <div class="d-none d-lg-block ml-3 mr-0">
                    <div class="" id="wishlist">
                        @include('frontend.partials.wishlist')
                    </div>
                </div>

                <div class="d-none d-lg-block  align-self-stretch ml-3 mr-0" data-hover="dropdown">
                    <div class="nav-cart-box dropdown h-100" id="cart_items">
                        @include('frontend.partials.cart')
                    </div>
                </div>

            </div>
        </div>
        @if (Route::currentRouteName() != 'home')
            <div class="hover-category-menu position-absolute w-100 top-100 left-0 right-0 d-none z-3"
                id="hover-category-menu">
                <div class="container">
                    <div class="row gutters-10 position-relative">
                        <div class="col-lg-3 position-static">
                            @include('frontend.partials.category_menu')
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    @if (get_setting('header_menu_labels') != null)
        <div class="bg-white border-top border-gray-200 py-1">
            <div class="container">
                <ul class="list-inline mb-0 pl-0 mobile-hor-swipe text-center">
                  {{-- @foreach (json_decode(get_setting('header_menu_labels'), true) as $key => $value)
                        <li class="list-inline-item mr-0">
                            <a href="{{ json_decode(get_setting('header_menu_links'), true)[$key] }}"
                                class="opacity-60 fs-14 px-3 py-2 d-inline-block fw-600 hov-opacity-100 text-reset">
                                {{ translate($value) }}
                            </a>
                        </li>
                    @endforeach --}}
                    @foreach (\App\Models\Category::where('level', 0)->orderBy('order_level', 'desc')->get()->take(11) as $key => $category)
                        <li class="category-nav-element list-inline-item mr-0 dropdown-cat"
                            data-id="{{ $category->id }}">
                            <a href="{{ route('products.category', $category->slug) }}"
                                class="text-truncate text-reset py-2 px-3 d-block">
                                <img class="cat-image lazyload mr-2 opacity-60"
                                    src="{{ url('/assets/img/placeholder.jpg') }}"
                                    data-src="{{ uploaded_asset($category->icon) }}" width="16"
                                    alt="{{ $category->getTranslation('name') }}"
                                    onerror="this.onerror=null;this.src='{{ url('/assets/img/placeholder.jpg') }}';">
                                <span class="cat-name">{{ $category->getTranslation('name') }}</span>
                            </a>




                            @if (count(\App\Utility\CategoryUtility::get_immediate_children_ids($category->id)) > 0)
                                <div class="sub-cat-menu c-scrollbar-light rounded shadow-lg p-4 nav-bar-cat">
                                    <div class="c-preloader text-center absolute-center nav-bar-cat-menu">
                                        <i class="las la-spinner la-spin la-3x opacity-70"></i>
                                    </div>
                                </div>
                            @endif
                        </li>
                    @endforeach
                    <li class="category-nav-element list-inline-item mr-0 my-1">
                        <a href="{{ route('categories.all') }}" class="text-truncate text-reset py-2 px-3 d-block">
                            <span class="d-none d-lg-inline-block">{{ translate('See All') }} ></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    @endif
</header>
