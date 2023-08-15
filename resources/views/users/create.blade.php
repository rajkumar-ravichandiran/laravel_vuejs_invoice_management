@extends('layouts.app')
@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
<div class="container-fluid">
</div>
</div>
    <div class="container-fluid mt--7">
        <div class="row">
            
            <div class="col-xl-12">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <h3 class="mb-0">{{ __('Create User') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form method="post" action="{{ route('user.store') }}" autocomplete="off">
                            @csrf
                            @method('post')

                            <h6 class="heading-small text-muted mb-4">{{ __('User information') }}</h6>
                            
                            <div class="col-12">
                                @include('layouts.flash')
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('name') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-name">{{ __('Name') }}</label>
                                    <input type="text" name="name" id="input-name" class="form-control form-control-alternative{{ $errors->has('name') ? ' is-invalid' : '' }}" placeholder="{{ __('Name') }}" value="{{ old('name') }}" required autofocus>

                                    @if ($errors->has('name'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('user_type') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="user_type">{{ __('User Type') }}</label>                         
                                    <select name="user_type" class="form-control{{ $errors->has('user_type') ? ' is-invalid' : '' }}" id="user_type"  required>
                                        <option value="" selected disabled>{{__('Select User Type')}}</option>
                                        @foreach($roles as $role)
                                        <option value="{{$role}}">{{$role}}</option>
                                        @endforeach
                                    </select>
                                        @if ($errors->has('user_type'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('user_type') }}</strong>
                                            </span>
                                        @endif
                                  </div>
                              </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('email') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-email">{{ __('Email') }}</label>
                                    <input type="email" name="email" id="input-email" class="form-control form-control-alternative{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="{{ __('Email') }}" value="{{ old('email') }}" required>

                                    @if ($errors->has('email'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('email') }}</strong>
                                        </span>
                                    @endif
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('status') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="status">{{ __('Status') }}</label>                         
                                    <select name="status" class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}" id="status"  required>
                                        <option value="" selected disabled>{{__('Select Status')}}</option>
                                        <option value="1">{{__('Approved')}}</option>
                                        <option value="0">{{__('Rejected')}}</option>
                                    </select>
                                        @if ($errors->has('status'))
                                            <span class="invalid-feedback" style="display: block;" role="alert">
                                                <strong>{{ $errors->first('status') }}</strong>
                                            </span>
                                        @endif
                                  </div>
                              </div>
                                <div class="col-md-6">
                                    <div class="form-group{{ $errors->has('password') ? ' has-danger' : '' }}">
                                    <label class="form-control-label" for="input-password">{{ __('Password') }}</label>
                                    <input type="password" name="password" id="input-password" class="form-control form-control-alternative{{ $errors->has('password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" value="" required>
                                    
                                    @if ($errors->has('password'))
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $errors->first('password') }}</strong>
                                        </span>
                                    @endif
                                </div>
                                </div>
                                <div class="col-12">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>                        
                    </div>
                </div>
            </div>
        </div>
        
        @include('layouts.footers.auth')
    </div>
@endsection
