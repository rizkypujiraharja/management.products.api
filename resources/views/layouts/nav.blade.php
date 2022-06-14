@auth
{{--    <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm mb-0">--}}
    <nav class="navbar navbar-expand-md navbar-light mb-2 p-0">
        <div class="container text-white">
            <a class="navbar-brand d-none d-md-block" href="{{ url('/') }}">
                PM
                @yield('title')
            </a>

            <div class="d-block d-md-none mb-0 navbar-dark w-100 bg-primary">
                <div class="btn-group" role="group" aria-label="Basic example">
                    <a type="button" class="btn btn-primary" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    <a type="button" class="btn btn-primary " href="{{ route('products') }}">{{ __('Products') }}</a>
                    <a type="button" class="btn btn-primary" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                </div>
                <button class="navbar-toggler navbar-light float-right border-0" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon text-white"></span>
                </button>
            </div>

            <style>
                .collapsing {
                    -webkit-transition: none;
                    transition: none;
                    display: none;
                }
            </style>
            <div class="collapse navbar-collapse bg-white" id="navbarSupportedContent" style="transition: none !important;">
                <!-- Left Side Of Navbar -->
                <ul class="navbar-nav mr-auto text-center text-md-left">
                    <li class="nav-item d-none d-md-inline d-md-block">
                        <a class="nav-link" href="{{ route('dashboard') }}">|</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('products') }}">{{ __('Products') }}</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a class="nav-link" href="{{ route('orders') }}">{{ __('Orders') }}</a>
                    </li>

                    <!-- Picklist Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Picklist') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                           @foreach ($navigationMenuPicklist as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- Packlist Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Packlist') }}<span class="caret"></span>
                        </a>

                        <!-- Packlist Menu -->
                        <div class="dropdown-menu dropdown-menu-left" aria-labelledby="navbarDropdown">
                            @foreach ($navigationMenuPacklist as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach
                        </div>
                    </li>

                    <!-- Reports Dropdown -->
                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown3" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ __('Reports') }}<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                            <a class="dropdown-item" href="{{ route('performance.dashboard') .'?between_dates=-7days,now' }}">{{ __('Performance Dashboard') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.picks') }}">{{ __('Picks') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.shipments') }}">{{ __('Shipments') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.inventory') }}">{{ __('Inventory') }}</a>
                            <a class="dropdown-item" href="{{ route('reports.restocking') }}">{{ __('Restocking') }}</a>
                            @foreach ($navigationMenuReports as $menu)
                                <a class="dropdown-item" href="{{ $menu->url }}">
                                    {{ $menu->name }}
                                </a>
                            @endforeach

                        </div>
                    </li>
                </ul>


                <!-- Right Side Of Navbar -->
                <ul class="navbar-nav ml-auto text-center text-md-left">

                    <li class="nav-item dropdown">
                        <!-- User dropdown menu -->
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} (id: {{ Auth::user()->id }})<span class="caret"></span>
                        </a>

                        <!-- Menu Items END -->
                        <div class="dropdown-menu dropdown-menu-right text-center text-md-left" aria-labelledby="navbarDropdown">
                            {{-- Profile --}}
                            <a class="dropdown-item" href="{{ route('setting-profile') }}">
                                {{ __('Profile') }}
                            </a>

                            <!-- Settings -->
                            @hasrole('admin')
                                <a class="dropdown-item" href="{{ route('settings') }}">
                                    {{ __('Settings') }}
                                </a>
                            @endhasrole

                            <a class="dropdown-item" href="https://www.youtube.com/channel/UCl04S5dRXop1ZdZsOqY3OnA" target="_blank">
                                {{ __('YouTube') }}
                            </a>

                            <a class="dropdown-item" href="{{ route('stocktaking') }}">
                                {{ __('Stocktaking') }}
                            </a>

                            <!-- Logout -->
                            <a class="dropdown-item" href="{{ route('logout') }}"
                               onclick="event.preventDefault();
                               document.getElementById('logout-form').submit();">
                                 {{ __('Logout') }}
                            </a>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>

                        </div>
                    </li>

                </ul>
            </div>

        </div>
    </nav>
@endauth
