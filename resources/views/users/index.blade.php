@extends('layouts.app')
@section('content')
<div class="header bg-gradient-primary pb-8 pt-5 pt-md-8">
<div class="container-fluid">
</div>
</div>
<div class="container-fluid mt--7">
    <div class="row">
        <div class="col">
            <div class="card shadow">
                <div class="card-header border-0">
                    <div class="row align-items-center">
                        <div class="col-6">
                            <h3 class="mb-0">Users</h3>
                        </div>
                        <div class="col-6 text-right">
                            <a href="{{route('user.create')}}" class="btn btn-sm btn-primary">Add user</a>
                            <button id="show-hide-filters" class="btn btn-icon btn-1 btn-sm btn-outline-secondary" type="button"><span class="btn-inner--icon"><i id="button-filters" class="ni ni-bold-down"></i></span></button>
                        </div>
                    </div>
                    <div class="tab-content show-filters" style="display:{{$parameters ? 'block' : 'none'}};">
                       <form method="GET">
                          <div class="row mt-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="role_id">{{ __('Filter by Role') }}</label>
                                    <select class="form-control" name="role_id">
                                        <option disabled selected value> -- {{ __('Select an option') }} -- </option>
                                        @foreach ($roles as $key=>$role)
                                            <option <?php if(isset($_GET['role_id'])&&$_GET['role_id'].""==$key.""){echo "selected";} ?> value="{{ $key }}">{{$role}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-control-label" for="status">{{ __('Filter by Status') }}</label>
                                    <select class="form-control" name="status">
                                        <option disabled selected value> -- {{ __('Select an option') }} -- </option>
                                            <option <?php if(isset($_GET['status'])&&$_GET['status'].""=="0"){echo "selected";} ?> value="0">{{__('InActive')}}</option>
                                            <option <?php if(isset($_GET['status'])&&$_GET['status'].""=="1"){echo "selected";} ?> value="1">{{__('Active')}}</option>
                                    </select>
                                </div>
                            </div>
                             <div class="col-md-6">
                                <div class="row justify-content-end">
                                   @if ($parameters)
                                   <div class="col-md-4">
                                      <a href="{{ Request::url() }}" class="btn btn-md btn-block">{{ __('Clear Filters') }}</a>
                                   </div>
                                   @else
                                   <div class="col-md-8"></div>
                                   @endif
                                   <div class="col-md-4">
                                      <button type="submit" class="btn btn-primary btn-md btn-block">{{ __('Filter') }}</button>
                                   </div>
                                </div>
                             </div>
                          </div>
                       </form>
                    </div>
                </div>
                <div class="col-12">
                    @include('layouts.flash')
                </div>

                <div class="table-responsive">
                    <table class="table align-items-center table-flush">
                        <thead class="thead-light">
                            <tr>
                                <th scope="col">#</th>
                                <th scope="col">@sortablelink('name')</th>
                                <th scope="col">@sortablelink('email')</th>
                                <th scope="col">Role</th>
                                <th scope="col">Status</th>
                                <th scope="col">@sortablelink('created_at')</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($users as $key=>$user)
                                <tr>
                                    <td>{{ $key+1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td><a href="mailto:{{ $user->email }}">{{ $user->email }}</a></td>
                                    <td>{{ $user->roles()->pluck('name')->first() }}</td>
                                    <td>{{ $user->active == 1? 'Active' : 'InActive' }}</td>
                                    <td>{{ $user->created_at }}</td>
                                    <td class="text-right">
                                        <div class="dropdown">
                                            <a class="btn btn-sm btn-icon-only text-light" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                                            <a class="dropdown-item" href="{{route('user.edit',$user)}}">Edit</a>
                                            @if($user->active == 0)
                                            <a class="dropdown-item" href="{{route('user.approval',[$user,1])}}">Accept</a>
                                            @endif
                                            <a class="dropdown-item" href="{{route('user.approval',[$user,0])}}">Reject</a>
                                            <form action="{{ route('user.destroy', $user) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item">Delete</button>
                                            </form>                                            
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                        </tbody>
                    </table>
                </div>
            <!-- Card footer -->
            <div class="card-footer py-4">
              {{ $users->links() }}
            </div>
            </div>
        </div>
    </div>
@include('layouts.footers.auth')
</div>
@endsection
