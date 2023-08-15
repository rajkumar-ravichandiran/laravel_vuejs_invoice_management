<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use App\Notifications\ManualApprovalNotification;

class UserController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\Models\User  $model
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        if(auth()->user()->hasRole(['admin'])){
        $users = User::query();
        }else{
            // Get the admin users
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'admin');
        })->first();
        $users = User::whereNotIn('id',[auth()->user()->id,$adminUsers->id]);
        }
        if (isset($_GET['role_id'])&&strlen($_GET['role_id'])>0) {
        $users = $users->whereHas('roles', function ($query) {
            $query->where('id', $_GET['role_id']);
        });    
        }
        if (isset($_GET['status'])&&strlen($_GET['status'])>0) {
        $users = $users->where('active', $_GET['status']);    
        }
        $users = $users->OrderBy('created_at','DESC')->paginate(10);
        $roles = Role::pluck('name','id')->toArray();
        $parameters = count($_GET) != 0;
        return view('users.index',compact('users','parameters','roles'));
    }

    public function create()
    {
        $roles = Role::pluck('name')->toArray();
        return view('users.create',compact('roles'));
    }

    public function store(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'active' => (int)$request->status,
        ]);

        $role = Role::findByName($request->user_type);
        if($role){
            $user->assignRole($role->name);            
        }else{
            $user->assignRole('visitor');            
        }
        return redirect()->back()->withStatus('User Created Successfully');
    }

    public function edit(User $user)
    {
        $roles = Role::pluck('name')->toArray();
        return view('users.edit',compact('roles','user'));
    }

    public function update(User $user,Request $request)
    {
        if($request->has('name')){
        $user->name = $request->name;
        }

        if($request->has('email')){
        $user->email = $request->email;
        }

        if($request->has('password')){
        $user->password = Hash::make($request->password);
        }

        if($request->has('status')){
        $user->active = (int)$request->status;
        }

        if($request->has('user_type')){
        $user->removeRole($user->roles()->pluck('name')->first());
        $role = Role::findByName($request->user_type);
            if($role){
                $user->assignRole($role->name);            
            }else{
                $user->assignRole('visitor');            
            }
        }

        $user->update();
        return redirect()->back()->withStatus('User Updated Successfully');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->back()->withStatus('User Deleted Successfully');
    }

    public function manualApproval($id,$status)
    {
        $user = User::FindOrFail($id);
        if($user){
        $user->active = (int)$status;
        $user->update();
            if($user->active == 1){
                // User is active, send approval notification
                $user->notify(new ManualApprovalNotification(1));
                return redirect()->back()->withStatus('User Approved Successfully');
            }else{
                // User is inactive, send rejection notification
                $user->notify(new ManualApprovalNotification(0));
                return redirect()->back()->withStatus('User Rejected Successfully');
            }
        }else{
         return redirect()->back()->withError('User Not found');
        }
        
    }
}
