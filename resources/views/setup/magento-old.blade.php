@extends('layouts.app')

@section('content')
<div class="container h-100">
    <div class="row justify-content-center">
        <div class="col-md-4 m-5 text-center">
            <div>
                <img src="{{ asset('img/logos/magento_shopify.png') }}" alt="">
            </div>

            <div class="card mt-2">
{{--                <div class="card-header">{{ __('Register') }}</div>--}}

                <div class="card-body">
                    <div class="row">
{{--                        <div class="col-6">--}}
{{--                            <img src="https://picsum.photos/id/1/340/425" alt="">--}}
{{--                        </div>--}}
                        <div class="col-12">
                            <form method="POST" action="{{ route('register') }}">
                                @csrf
                                <div class="form-group row col">
                                    <div class="mb-1">What eCommerce platform you using ?</div>
                                    <select id="store_type" class="form-control">
                                        <option value=""></option>
                                        <option value="magento2api">Magento 2</option>
                                        <option value="shopify">Shopify</option>
                                    </select>

                                    @error('store_type')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

{{--                                <div class="form-group row">--}}
{{--                                    <div class="col-12 mb-1">Which couriers you are using ?</div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="dpd_ireland" name="dpd_ireland" >--}}
{{--                                        <label for="dpd_ireland">DPD Ireland</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="anpost_ireland" name="anpost_ireland" >--}}
{{--                                        <label for="anpost_ireland">AnPost Ireland</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="fastway_couriers" name="fastway_couriers" >--}}
{{--                                        <label for="fastway_couriers">Fastway Couriers</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="dhl" name="dhl">--}}
{{--                                        <label for="dhl">DHL</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="ups" name="ups">--}}
{{--                                        <label for="ups">UPS</label>--}}
{{--                                    </div>--}}
{{--                                    <div class="col-12">--}}
{{--                                        <input type="checkbox" id="other" name="other"    >--}}
{{--                                        <label for="other">Other</label>--}}
{{--                                    </div>--}}

{{--                                    @error('store_type')--}}
{{--                                    <span class="invalid-feedback" role="alert">--}}
{{--                                            <strong>{{ $message }}</strong>--}}
{{--                                        </span>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

                                <div class="form-group row col">
                                    <input placeholder="Domain name" id="url" type="url" class="form-control @error('url') is-invalid @enderror" name="url" value="{{ old('url') }}" required autocomplete="url" autofocus>

                                    @error('url')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group row col">
                                    <input placeholder="Email address" id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="form-group row col">
                                    <button type="submit" class="btn btn-primary w-100">
                                        {{ __('CONNECT') }}
                                    </button>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
