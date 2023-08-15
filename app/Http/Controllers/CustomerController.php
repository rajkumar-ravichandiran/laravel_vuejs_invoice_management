<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customers;
use App\Models\Address;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all customers
        $customers = Customers::sortable()->OrderBy('created_at','DESC')->paginate(10);
        $parameters = count($_GET) != 0;
        // Pass the customers to the view
        return view('customers.index', compact('customers','parameters'));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getCustomersList(Request $request)
    {
        try {            
           $customers = Customers::orderBy('name', 'ASC');

            if ($request->has('q')) {
                $customers = $customers->where('name', 'like', '%' . $request->q . '%');
            }
            $customerArray = $customers->get()->toArray(); // Convert to key-value array

            return response()->json([
                'status' => true,
                'data' => $customerArray,
            ]);

        } catch (\Exception $e) {

            $message = $e->getMessage();
            return response()->json([
                'status' => false,
                'msg'=> $message
            ]);
            exit;
        } catch (\QueryException $e) {

            return response()->json([
                'status' => false,
                'msg'=> 'Error in Database'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getCustomer($id)
    {
        $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($id);
        if($customer){
            return response()->json([
                'status' => true,
                'data'=> $customer
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Customer not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Customers $customer)
    {
        $customer->delete();
        return redirect()->back()->withStatus('Customer Deleted Successfully');
    }

    public function addCustomer(Request $request)
    {
        try {            
            
            DB::beginTransaction(); // Start a database transaction

            $customer = new Customers();
            if($request->has('customer_name')){
                $customer->name = strip_tags($request->customer_name);
            }
            if($request->has('customer_email')){
                $customer->email = strip_tags($request->customer_email);
            }
            if($request->has('customer_phone')){
                $customer->phone = strip_tags($request->customer_phone);
            }
            if($request->has('customer_type')){
                $customer->type = strip_tags($request->customer_type);
            }
            if($request->has('company_name')){
                $customer->company_name = strip_tags($request->company_name);
            }
            if($request->has('payment_terms')){
                $customer->payment_terms = strip_tags($request->payment_terms);
            }
            if($request->has('currency_id')){
                $customer->currency_id = strip_tags($request->currency_id);
            }
            if($request->has('customer_website')){
                $customer->website = strip_tags($request->customer_website);
            }
            if($request->has('custom_fields')){
                $customFields = $request->custom_fields;
                foreach ($customFields as &$customField) {
                    foreach ($customField as &$value) {
                        $value = is_string($value) ? strip_tags($value) : $value;
                    }
                }
                $customer->custom_fields = json_encode($customFields,true);
            }
            if($request->has('customer_notes')){
                $customer->notes = strip_tags($request->customer_notes);
            }
            if($request->has('contact_persons')){
                $contactPersons = $request->contact_persons;
                    foreach ($contactPersons as &$contactPerson) {
                        foreach ($contactPerson as &$value) {
                            $value = is_string($value) ? strip_tags($value) : $value;
                        }
                    }
                $customer->contact_persons = json_encode($contactPersons,true);
            }
            if($request->has('customer_gst_no')){
                $customer->gst_no = strip_tags($request->customer_gst_no);
            }
            if($request->has('customer_facebook')){
                $customer->facebook = strip_tags($request->customer_facebook);
            }
            if($request->has('customer_twitter')){
                $customer->twitter = strip_tags($request->customer_twitter);
            }
            if($request->has('billing_attention') && strlen($request->billing_attention) > 0){
                $address = new Address();
                $address->attention = strip_tags($request->billing_attention);
                if($request->has('billing_country')){
                    $address->country = strip_tags($request->billing_country);
                }
                if($request->has('billing_street_1')){
                    $address->address = strip_tags($request->billing_street_1);
                }
                if($request->has('billing_street_2')){
                    $address->street2 = strip_tags($request->billing_street_2);
                }
                if($request->has('billing_city')){
                    $address->city = strip_tags($request->billing_city);
                }
                if($request->has('billing_state')){
                    $address->state = strip_tags($request->billing_state);
                }
                if($request->has('billing_zip_code')){
                    $address->zipcode = strip_tags($request->billing_zip_code);
                }
                if($request->has('billing_fax')){
                    $address->fax = strip_tags($request->billing_fax);
                }
                if($request->has('billing_phone')){
                    $address->phone = strip_tags($request->billing_phone);
                }
                $address->save();
                if($address){
                    $customer->billing_address = $address->id;    
                }                    
            }
            if($request->has('shipping_attention') && strlen($request->shipping_attention) > 0){
                $address = new Address();
                $address->attention = strip_tags($request->shipping_attention);
                if($request->has('shipping_country')){
                    $address->country = strip_tags($request->shipping_country);
                }
                if($request->has('shipping_street_1')){
                    $address->address = strip_tags($request->shipping_street_1);
                }
                if($request->has('shipping_street_2')){
                    $address->street2 = strip_tags($request->shipping_street_2);
                }
                if($request->has('shipping_city')){
                    $address->city = strip_tags($request->shipping_city);
                }
                if($request->has('shipping_state')){
                    $address->state = strip_tags($request->shipping_state);
                }
                if($request->has('shipping_zip_code')){
                    $address->zipcode = strip_tags($request->shipping_zip_code);
                }
                if($request->has('shipping_fax')){
                    $address->fax = strip_tags($request->shipping_fax);
                }
                if($request->has('shipping_phone')){
                    $address->phone = strip_tags($request->shipping_phone);
                }
                $address->save();
                if($address){
                    $customer->shipping_address = $address->id;    
                }                    
            }
            $customer->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Customer Created Successfully'
            ]);
        } catch (\Exception $e) {

            DB::rollback(); // Rollback the transaction in case of exception

            $message = $e->getMessage();
            return response()->json([
                'status' => false,
                'msg'=> $message
            ]);
            exit;
        } catch (\QueryException $e) {

            DB::rollback(); // Rollback the transaction in case of exception

            return response()->json([
                'status' => false,
                'msg'=> 'Error in Database'
            ]);
        }
    }
    public function updateCustomer($customer, Request $request)
    {
        try {
            DB::beginTransaction(); // Start a database transaction

            $customer = Customers::FindOrFail($customer);
            if($customer){
                if($request->has('customer_name')){
                    $customer->name = strip_tags($request->customer_name);
                }
                if($request->has('customer_email')){
                    $customer->email = strip_tags($request->customer_email);
                }
                if($request->has('customer_phone')){
                    $customer->phone = strip_tags($request->customer_phone);
                }
                if($request->has('customer_type')){
                    $customer->type = strip_tags($request->customer_type);
                }
                if($request->has('company_name')){
                    $customer->company_name = strip_tags($request->company_name);
                }
                if($request->has('payment_terms')){
                    $customer->payment_terms = strip_tags($request->payment_terms);
                }
                if($request->has('currency_id')){
                    $customer->currency_id = strip_tags($request->currency_id);
                }
                if($request->has('customer_website')){
                    $customer->website = strip_tags($request->customer_website);
                }
                if($request->has('custom_fields')){
                    $customFields = $request->custom_fields;
                    foreach ($customFields as &$customField) {
                        foreach ($customField as &$value) {
                            $value = is_string($value) ? strip_tags($value) : $value;
                        }
                    }
                    $customer->custom_fields = json_encode($customFields,true);
                }
                if($request->has('customer_notes')){
                    $customer->notes = strip_tags($request->customer_notes);
                }
                if($request->has('contact_persons')){
                    $contactPersons = $request->contact_persons;
                    foreach ($contactPersons as &$contactPerson) {
                        foreach ($contactPerson as &$value) {
                            $value = is_string($value) ? strip_tags($value) : $value;
                        }
                    }
                    $customer->contact_persons = json_encode($contactPersons,true);
                }
                if($request->has('customer_gst_no')){
                    $customer->gst_no = strip_tags($request->customer_gst_no);
                }
                if($request->has('customer_facebook')){
                    $customer->facebook = strip_tags($request->customer_facebook);
                }
                if($request->has('customer_twitter')){
                    $customer->twitter = strip_tags($request->customer_twitter);
                }
                if($request->has('billing_attention') && strlen($request->billing_attention) > 0){
                    if($customer->billing_address && $customer->billingaddress){
                        $address = Address::FindOrFail($customer->billing_address);
                    }else{
                        $address = new Address();
                    }
                    $address->attention = strip_tags($request->billing_attention);
                    if($request->has('billing_country')){
                        $address->country = strip_tags($request->billing_country);
                    }
                    if($request->has('billing_street_1')){
                        $address->address = strip_tags($request->billing_street_1);
                    }
                    if($request->has('billing_street_2')){
                        $address->street2 = strip_tags($request->billing_street_2);
                    }
                    if($request->has('billing_city')){
                        $address->city = strip_tags($request->billing_city);
                    }
                    if($request->has('billing_state')){
                        $address->state = strip_tags($request->billing_state);
                    }
                    if($request->has('billing_zip_code')){
                        $address->zipcode = strip_tags($request->billing_zip_code);
                    }
                    if($request->has('billing_fax')){
                        $address->fax = strip_tags($request->billing_fax);
                    }
                    if($request->has('billing_phone')){
                        $address->phone = strip_tags($request->billing_phone);
                    }
                    $address->save();
                    if($address){
                        $customer->billing_address = $address->id;    
                    }                    
                }
                if($request->has('shipping_attention') && strlen($request->shipping_attention) > 0){
                    if($customer->shipping_address && $customer->shippingaddress){
                        $address = Address::FindOrFail($customer->shipping_address);
                    }else{
                        $address = new Address();
                    }
                    $address->attention = strip_tags($request->shipping_attention);
                    if($request->has('shipping_country')){
                        $address->country = strip_tags($request->shipping_country);
                    }
                    if($request->has('shipping_street_1')){
                        $address->address = strip_tags($request->shipping_street_1);
                    }
                    if($request->has('shipping_street_2')){
                        $address->street2 = strip_tags($request->shipping_street_2);
                    }
                    if($request->has('shipping_city')){
                        $address->city = strip_tags($request->shipping_city);
                    }
                    if($request->has('shipping_state')){
                        $address->state = strip_tags($request->shipping_state);
                    }
                    if($request->has('shipping_zip_code')){
                        $address->zipcode = strip_tags($request->shipping_zip_code);
                    }
                    if($request->has('shipping_fax')){
                        $address->fax = strip_tags($request->shipping_fax);
                    }
                    if($request->has('shipping_phone')){
                        $address->phone = strip_tags($request->shipping_phone);
                    }
                    $address->save();
                    if($address){
                        $customer->shipping_address = $address->id;    
                    }                    
                }
                $customer->update();
                
                DB::commit(); // Commit the transaction
                
                return response()->json([
                    'status' => true,
                    'msg'=> 'Customer Updated Successfully'
                ]);    
            }
            
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction in case of exception
            $message = $e->getMessage();
            return response()->json([
                'status' => false,
                'msg'=> $message
            ]);
            exit;
        } catch (\QueryException $e) {
            DB::rollback(); // Rollback the transaction in case of exception
            return response()->json([
                'status' => false,
                'msg'=> 'Error in Database'
            ]);
        }
    }
}
