@extends('layouts.app')

@section('title', __('Settings'))

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-12">
            <inventory-reservations-configuration-page></inventory-reservations-configuration-page>
        </div>
    </div>
</div>
@endsection
