<?php

namespace App\Http\Controllers;
use App\Models\User;
use App\Models\Customers;
use App\Models\Estimates;
use App\Models\Invoices;
use App\Models\Items;
use App\Models\Payments;
use App\Models\RecurringInvoices;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if(auth()->user()->hasRole('admin')){
            return redirect()->route('customers.index');
        }
        $startoftheMonth = Carbon::today()->startOfMonth();
        $endoftheMonth = Carbon::today()->endOfMonth();
        $laststartoftheMonth = Carbon::today()->subMonth(1)->startOfMonth();
        $lastendoftheMonth = Carbon::today()->subMonth(1)->endOfMonth();
        $totalUser = User::all()->count();
        $currentMonthUser = User::where('created_at','>',$startoftheMonth)->where('created_at','<',$endoftheMonth)->count();
        $lastMonthUser = User::where('created_at','>',$laststartoftheMonth)->where('created_at','<',$lastendoftheMonth)->count();
        // Calculate the percentage change in user count
        $userpercentageChange = 0;
        if ($lastMonthUser > 0) {
            $userpercentageChange = (($currentMonthUser - $lastMonthUser) / $lastMonthUser) * 100;
            $userpercentageChange = round($userpercentageChange, 2); // Round to 2 decimal points
        }
        // Check if the count percentage is greater or lower
        $usercomparison = '';
        if ($userpercentageChange > 0) {
            $usercomparison = 'higher';
        } elseif ($userpercentageChange < 0) {
            $usercomparison = 'lower';
        }
        return view('dashboard',compact('totalUser','currentMonthUser','lastMonthUser','userpercentageChange','usercomparison'));
    }
}
