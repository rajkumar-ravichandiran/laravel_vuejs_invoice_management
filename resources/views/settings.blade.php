@extends('layouts.app', ['title' => __('User Profile')])

@section('content')
    @include('users.partials.header', [
        'title' =>'',
    ])   

    <div class="container-fluid mt--7">
        <div class="row">
            
            <div class="col-xl-12">
                <div class="card bg-secondary shadow">
                    <div class="card-header bg-white border-0">
                        <div class="row align-items-center">
                            <h3 class="mb-0">{{ __('Settings') }}</h3>
                        </div>
                    </div>
                    <div class="card-body">
                        <form id="settings" method="post" action="{{ route('settings.update') }}" autocomplete="off" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            @if (session('status'))
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    {{ session('status') }}
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            @endif
                            <div class="nav-wrapper">
                                <ul class="nav nav-pills nav-fill flex-column flex-md-row" id="tabs-icons-text" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link mb-sm-3 mb-md-0 active" id="tabs-icons-text-1-tab" data-toggle="tab" href="#tabs-icons-text-1" role="tab" aria-controls="tabs-icons-text-1" aria-selected="true"><i class="ni ni-bullet-list-67 mr-2"></i>{{ __ ('Site Info') }}</a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link mb-sm-3 mb-md-0" id="tabs-icons-text-2-tab" data-toggle="tab" href="#tabs-icons-text-2" role="tab" aria-controls="tabs-icons-text-2" aria-selected="false"><i class="ni ni-image mr-2"></i>{{ __ ('SMTP') }}</a>
                                    </li>
                                </ul>
                            </div>
                            <br/>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="tabs-icons-text-1" role="tabpanel" aria-labelledby="tabs-icons-text-1-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('app_name') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="app_name">{{ __('App Name') }}</label>
                                            <input type="text" name="app_name" id="app_name" class="form-control form-control-alternative{{ $errors->has('app_name') ? ' is-invalid' : '' }}" placeholder="{{ __('App Name') }}" value="{{ old('app_name',config('app.name')) }}"  >
                                            @if ($errors->has('app_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('app_name') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('app_env') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="app_env">{{ __('App Environment') }}</label>                         
                                            <select name="app_env" class="form-control{{ $errors->has('app_env') ? ' is-invalid' : '' }}" id="app_env"  >
                                                <option {{ old('app_env',config('app.env')) == 'local' ? 'selected' : '' }} value="local">{{__('Local')}}</option>
                                                <option {{ old('app_env',config('app.env')) == 'production' ? 'selected' : ''  }}  value="production">{{__('Production')}}</option>
                                            </select>
                                                @if ($errors->has('app_env'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('app_env') }}</strong>
                                                    </span>
                                                @endif
                                          </div>
                                      </div>
                                        <div class="col-md-6">
                                          <div class="form-group{{ $errors->has('app_debug') ? ' has-danger' : '' }}">
                                                <label class="form-control-label" for="app_debug">{{ __('App Debugging') }}</label>
                                                <label class="custom-toggle" style="float: right">
                                                    <input type='hidden' value='false' name="app_debug" id="app_debughid">
                                                    <input value="true" {{ old('app_debug',config('app.debug')) == 'true' ? 'checked' : '' }}  type="checkbox"  name="app_debug" id="app_debug">
                                                    <span class="custom-toggle-slider rounded-circle"></span>
                                                </label>
                                                @if ($errors->has('app_debug'))
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $errors->first('app_debug') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="tabs-icons-text-2" role="tabpanel" aria-labelledby="tabs-icons-text-2-tab">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_driver') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_driver">{{ __('Mail driver') }}</label>                         
                                            <select name="mail_driver" class="form-control{{ $errors->has('mail_driver') ? ' is-invalid' : '' }}" id="mail_driver"  >
                                                <option {{ old('mail_driver',config('mail.default')) == 'smtp' ? 'selected' : '' }} value="smtp">{{__('SMTP')}}</option>
                                                <option {{ old('mail_driver',config('mail.default')) == 'sendmail' ? 'selected' : ''  }}  value="sendmail">{{__('PHP Sendmail - best of port 465')}}</option>
                                            </select>
                                                @if ($errors->has('mail_driver'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('mail_driver') }}</strong>
                                                    </span>
                                                @endif
                                          </div>
                                      </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_host') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_host">{{ __('Host') }}</label>
                                            <input type="text" name="mail_host" id="mail_host" class="form-control form-control-alternative{{ $errors->has('mail_host') ? ' is-invalid' : '' }}" placeholder="{{ __('Host') }}" value="{{ old('mail_host',config('mail.mailers.smtp.host')) }}"  >
                                            @if ($errors->has('mail_host'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_host') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_port') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_port">{{ __('Port') }}</label>
                                            <input type="text" name="mail_port" id="mail_port" class="form-control form-control-alternative{{ $errors->has('mail_port') ? ' is-invalid' : '' }}" placeholder="{{ __('Port') }}" value="{{ old('mail_port',config('mail.mailers.smtp.port')) }}"  >
                                            @if ($errors->has('mail_port'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_port') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_encryption') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_encryption">{{ __('Encryption') }}</label>                         
                                            <select name="mail_encryption" class="form-control{{ $errors->has('mail_encryption') ? ' is-invalid' : '' }}" id="mail_encryption"  >
                                                <option {{ old('mail_encryption',config('mail.mailers.smtp.encryption')) == 'null' ? 'selected' : '' }} value="null">{{__('Null - best for port 26')}}</option>
                                                <option {{ old('mail_encryption',config('mail.mailers.smtp.encryption')) == '' ? 'selected' : '' }} value="">{{__('None - best for port 587')}}</option>
                                                <option {{ old('mail_encryption',config('mail.mailers.smtp.encryption')) == 'ssl' ? 'selected' : '' }} value="ssl">{{__('SSL - best for port 465')}}</option>
                                                <option {{ old('mail_encryption',config('mail.mailers.smtp.encryption')) == 'tls' ? 'selected' : '' }} value="tls">{{__('TLS')}}</option>
                                                <option {{ old('mail_encryption',config('mail.mailers.smtp.encryption')) == 'starttls' ? 'selected' : '' }} value="starttls">{{__('STARTTLS')}}</option>
                                            </select>
                                                @if ($errors->has('mail_encryption'))
                                                    <span class="invalid-feedback" style="display: block;" role="alert">
                                                        <strong>{{ $errors->first('mail_encryption') }}</strong>
                                                    </span>
                                                @endif
                                          </div>
                                      </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_username') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_username">{{ __('Username') }}</label>
                                            <input type="text" name="mail_username" id="mail_username" class="form-control form-control-alternative{{ $errors->has('mail_username') ? ' is-invalid' : '' }}" placeholder="{{ __('Username') }}" value="{{ old('mail_username',config('mail.mailers.smtp.username')) }}"  >
                                            @if ($errors->has('mail_username'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_username') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_password') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_password">{{ __('Password') }}</label>
                                            <input type="text" name="mail_password" id="mail_password" class="form-control form-control-alternative{{ $errors->has('mail_password') ? ' is-invalid' : '' }}" placeholder="{{ __('Password') }}" value="{{ old('mail_password',config('mail.mailers.smtp.password')) }}"  >
                                            @if ($errors->has('mail_password'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_password') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_from_address') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_from_address">{{ __('From address') }}</label>
                                            <input type="text" name="mail_from_address" id="mail_from_address" class="form-control form-control-alternative{{ $errors->has('mail_from_address') ? ' is-invalid' : '' }}" placeholder="{{ __('From address') }}" value="{{ old('mail_from_address',config('mail.from.address')) }}"  >
                                            @if ($errors->has('mail_from_address'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_from_address') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group{{ $errors->has('mail_from_name') ? ' has-danger' : '' }}">
                                            <label class="form-control-label" for="mail_from_name">{{ __('From Name') }}</label>
                                            <input type="text" name="mail_from_name" id="mail_from_name" class="form-control form-control-alternative{{ $errors->has('mail_from_name') ? ' is-invalid' : '' }}" placeholder="{{ __('From Name') }}" value="{{ old('mail_from_name',config('mail.from.name')) }}"  >
                                            @if ($errors->has('mail_from_name'))
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $errors->first('mail_from_name') }}</strong>
                                                </span>
                                            @endif
                                            </div>                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="col-12">
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-success mt-4">{{ __('Save') }}</button>
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
