@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (session('status'))
                <div class="alert alert-success" role="alert">
                    {{ session('status') }}
                </div>
            @endif

            <div class="list-group">
                <a href="{{ route('settings.general') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="cog" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">General</div>
                        <div class="setting-desc">View and setting-desc application settings</div>
                    </div>
                </a>

                <a href="{{ route('setting-profile') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="user-edit" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Profile</div>
                        <div class="setting-desc">Setting profile</div>
                    </div>
                </a>

                <a href="{{ route('settings.users') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="users" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Users</div>
                        <div class="setting-desc">Manage users</div>
                    </div>
                </a>

                <a href="{{ route('settings.order_statuses') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="box-open" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Order Statuses</div>
                        <div class="setting-desc">Manage order statuses</div>
                    </div>
                </a>

                <a href="{{ route('settings.mail_templates') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="envelope-open-text" class="fas-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Mail Templates</div>
                        <div class="setting-desc">View and update templates</div>
                    </div>
                </a>

                <a href="{{ route('settings.navigation_menu') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="list-ul" class="fas-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Navigation Menu</div>
                        <div class="setting-desc">Manage navigation menu</div>
                    </div>
                </a>

                <a href="{{ route('settings.rmsapi') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="code" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Microsoft Dynamic RMS 2.0 API</div>
                        <div class="setting-desc">View and update RMS API integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.dpd-ireland') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="truck" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">DPD Ireland API</div>
                        <div class="setting-desc">View and update DPD Ireland integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.modules') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="puzzle-piece" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Modules</div>
                        <div class="setting-desc">Manage Modules</div>
                    </div>
                </a>

                <a href="{{ route('settings.api2cart') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="shopping-cart" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Api2cart</div>
                        <div class="setting-desc">View and update Api2cart integration settings</div>
                    </div>
                </a>

                <a href="{{ route('settings.api') }}" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="key" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">API</div>
                        <div class="setting-desc">View and update application API settings and tokens</div>
                    </div>
                </a>

                <a href="{{ route('queue-monitor::index') }}" target="_blank" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="desktop" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Queue Monitor</div>
                        <div class="setting-desc">Open jobs monitor</div>
                    </div>
                </a>

                <a href="{{ route('tools.log-viewer') }}" target="_blank" class="setting-list">
                    <div class="setting-icon">
                        <font-awesome-icon icon="clipboard-list" class="fa-sm"></font-awesome-icon>
                    </div>
                    <div class="setting-body">
                        <div class="setting-title">Log Viewer</div>
                        <div class="setting-desc">View application logs</div>
                    </div>
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('css')
    <style>
        .setting-list{
            border-radius: 1px;
            padding: 0.5rem 1rem;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, 0.125);
            width: 100%;
            color: #495057;
            display: flex;
            align-items: flex-start;
            margin-bottom: 5px;
        }

        .setting-list:hover, .setting-list:focus {
            color: #495057;
            text-decoration: none;
            background-color: #f8f9fa;
        }

        .setting-icon{
            padding: 1rem;
            margin-right: 1rem;
            background-color: #f8f9fa;
            border-radius: 0.25rem;
        }

        .setting-icon:hover{
            background-color: unset;
        }

        .setting-title{
            color: #3490dc;
            font-weight: bolder;
            /*font-size: 1rem;*/
            /*line-height: 1.2;*/
            margin-bottom: 2px;
        }

        .setting-desc{
            color: #6c757d;
            font-size: 10pt;
        }
    </style>
@endsection
