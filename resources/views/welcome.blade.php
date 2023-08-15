@extends('layouts.front', ['class' => 'bg-gradient-primary'])
@section('css')
<link type="text/css" href="{{ asset('assets') }}/css/custom.css" rel="stylesheet">
@endsection
@section('content')
    <div class="header py-7 py-lg-8">
        <div class="container">
            <div class="header-body text-center mt-7 mb-7">
                <div class="row justify-content-center">
                    <div class="col-lg-5 col-md-6">
                        <h1 class="text-white">{{ __('Welcome to InvoiceApp.') }}</h1>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
