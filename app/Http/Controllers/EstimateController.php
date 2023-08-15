<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Estimates;
use App\Models\Customers;
use App\Models\Invoices;
use Illuminate\Support\Facades\DB;

class EstimateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all Estimates
        $estimates = Estimates::sortable()->OrderBy('created_at','DESC')->paginate(10);
        $parameters = count($_GET) != 0;
        // Pass the Estimates to the view
        return view('estimates.index', compact('estimates','parameters'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getEstimate($id)
    {
        $estimate = Estimates::with('customer','billingaddress','shippingaddress')->FindOrFail($id);
        if($estimate){
            return response()->json([
                'status' => true,
                'data'=> $estimate
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Estimate not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Estimates $estimate)
    {
        $estimate->delete();
        return redirect()->back()->withStatus('Estimate Deleted Successfully');
    }

    public function addEstimate(Request $request)
    {
        try {            
            $validatedData = $request->validate([
                'estimate_number' => 'unique:estimates,number',
                'estimate_order' => 'unique:estimates,reference_number'                
            ]);
            DB::beginTransaction(); // Start a database transaction

            $estimate = new Estimates();
            if($request->has('estimate_customer')){
                $estimate->customer_id = (int)$request->estimate_customer;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->estimate_customer);
                if($customer){
                    $estimate->billing_address = $customer->billing_address;
                    $estimate->shipping_address = $customer->shipping_address;
                    $estimate->currency_id = $customer->currency_id;
                }                
            }
            if($request->has('estimate_order')){
                $estimate->reference_number = strip_tags($request->estimate_order);
            }
            if($request->has('estimate_date')){
                $estimate->date = strip_tags($request->estimate_date);
            }
            if($request->has('estimate_gst_no')){
                $estimate->gst_no = strip_tags($request->estimate_gst_no);
            }
            if($request->has('estimate_expiry_date')){
                $estimate->expiry_date = strip_tags($request->estimate_expiry_date);
            }
            if($request->has('estimate_discount')){
                $estimate->discount = strip_tags($request->estimate_discount);
            }
            if($request->has('estimate_discounted_amount')){
                $estimate->discounted_amount = strip_tags($request->estimate_discounted_amount);
            }
            if($request->has('estimate_discount_on')){
                $estimate->is_discount_before_tax = $request->estimate_discount_on;
            }
            if($request->has('estimate_discount_type')){
                $estimate->discount_type = strip_tags($request->estimate_discount_type);
            }
            if($request->has('estimate_item_details')){
                $estimate->line_items = json_encode($request->estimate_item_details);
            }
            if($request->has('estimate_shipping_charge')){
                $estimate->shipping_charge = strip_tags($request->estimate_shipping_charge);
            }
            if($request->has('estimate_adjustment_amount')){
                $estimate->adjustment = strip_tags($request->estimate_adjustment_amount);
            }
            if($request->has('estimate_adjustment_descp')){
                $estimate->adjustment_description = strip_tags($request->estimate_adjustment_descp);
            }
            if($request->has('estimate_net')){
                $estimate->net = strip_tags($request->estimate_net);
            }
            if($request->has('estimate_subtotal')){
                $estimate->sub_total = strip_tags($request->estimate_subtotal);
            }
            if($request->has('estimate_gst')){
                $estimate->tax_total = strip_tags($request->estimate_gst);
            }
            if($request->has('estimate_total')){
                $estimate->total = strip_tags($request->estimate_total);
            }
            if($request->has('estimate_payment_remarks')){
                $estimate->notes = strip_tags($request->estimate_payment_remarks);
            }
            if($request->has('estimate_terms')){
                $estimate->terms = strip_tags($request->estimate_terms);
            }
            $estimate->status = 'draft';
            $estimate->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Estimate Created Successfully'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            $message = $e->getMessage();
            return response()->json([
                'status' => false,
                'msg'=> $message
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
    public function updateEstimate($id, Request $request)
    {
        try {
            $estimate = Estimates::findOrFail($id);
            $validatedData = $request->validate([
                'estimate_number' => 'unique:estimates,number,'.$estimate->id.',id',
                'estimate_order' => 'unique:estimates,reference_number,'.$estimate->id.',id',
            ]);
            
            DB::beginTransaction(); // Start a database transaction

            if($estimate){
                if($request->has('estimate_customer')){
                $estimate->customer_id = (int)$request->estimate_customer;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->estimate_customer);
                    if($customer){
                        $estimate->billing_address = $customer->billing_address;
                        $estimate->shipping_address = $customer->shipping_address;
                        $estimate->currency_id = $customer->currency_id;
                    }                
                }
                if($request->has('estimate_order')){
                    $estimate->reference_number = strip_tags($request->estimate_order);
                }
                if($request->has('estimate_date')){
                    $estimate->date = strip_tags($request->estimate_date);
                }
                if($request->has('estimate_gst_no')){
                    $estimate->gst_no = strip_tags($request->estimate_gst_no);
                }
                if($request->has('estimate_expiry_date')){
                    $estimate->due_date = strip_tags($request->estimate_expiry_date);
                }
                if($request->has('estimate_discount')){
                    $estimate->discount = strip_tags($request->estimate_discount);
                }
                if($request->has('estimate_discounted_amount')){
                    $estimate->discounted_amount = strip_tags($request->estimate_discounted_amount);
                }
                if($request->has('estimate_discount_on')){
                    $estimate->is_discount_before_tax = $request->estimate_discount_on;
                }
                if($request->has('estimate_discount_type')){
                    $estimate->discount_type = strip_tags($request->estimate_discount_type);
                }
                if($request->has('estimate_item_details')){
                    $estimate->line_items = json_encode($request->estimate_item_details);
                }
                if($request->has('estimate_shipping_charge')){
                    $estimate->shipping_charge = strip_tags($request->estimate_shipping_charge);
                }
                if($request->has('estimate_adjustment_amount')){
                    $estimate->adjustment = strip_tags($request->estimate_adjustment_amount);
                }
                if($request->has('estimate_adjustment_descp')){
                    $estimate->adjustment_description = strip_tags($request->estimate_adjustment_descp);
                }
                if($request->has('estimate_net')){
                    $estimate->net = strip_tags($request->estimate_net);
                }
                if($request->has('estimate_subtotal')){
                    $estimate->sub_total = strip_tags($request->estimate_subtotal);
                }
                if($request->has('estimate_gst')){
                    $estimate->tax_total = strip_tags($request->estimate_gst);
                }
                if($request->has('estimate_total')){
                    $estimate->total = strip_tags($request->estimate_total);
                }
                if($request->has('estimate_payment_remarks')){
                    $estimate->notes = strip_tags($request->estimate_payment_remarks);
                }
                if($request->has('estimate_terms')){
                    $estimate->terms = strip_tags($request->estimate_terms);
                }
                $estimate->update();
                
                DB::commit(); // Commit the transaction
                
                return response()->json([
                    'status' => true,
                    'msg'=> 'Estimate Updated Successfully'
                ]);    
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            $message = $e->getMessage();
            return response()->json([
                'status' => false,
                'msg'=> $message
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
    public function convertToInvoice($id)
    {
        try {
            $estimate = Estimates::findOrFail($id);
            
            DB::beginTransaction(); // Start a database transaction

            if($estimate){
                $invoice = new Invoices();
                $invoice->customer_id = $estimate->customer_id;
                $invoice->billing_address = $estimate->billing_address;
                $invoice->shipping_address = $estimate->shipping_address;
                $invoice->currency_id = $estimate->currency_id;
                $invoice->reference_number = $estimate->reference_number;
                $invoice->date = $estimate->date;
                $invoice->gst_no = $estimate->gst_no;
                $invoice->due_date = $estimate->date;
                $invoice->discount = $estimate->discount;
                $invoice->discounted_amount = $estimate->discounted_amount;
                $invoice->is_discount_before_tax = $estimate->is_discount_before_tax;
                $invoice->discount_type = $estimate->discount_type;
                $invoice->line_items = $estimate->line_items;
                $invoice->shipping_charge = $estimate->shipping_charge;
                $invoice->adjustment = $estimate->adjustment;
                $invoice->adjustment_description = $estimate->adjustment_description;
                $invoice->net = $estimate->net;
                $invoice->sub_total = $estimate->sub_total;
                $invoice->tax_total = $estimate->tax_total;
                $invoice->total = $estimate->total;
                $invoice->balance = $estimate->total;
                $invoice->notes = $estimate->notes;
                $invoice->terms = $estimate->terms;
                $invoice->status = 'unpaid';
                $invoice->payment_made = 0;
                $invoice->save();

                $estimate->is_invoice = 1;
                $estimate->status = 'invoiced';
                $estimate->update();
                
                DB::commit(); // Commit the transaction
                
                return redirect()->back()->withStatus('Invoice Created Successfully');  
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            $message = $e->getMessage();
            return redirect()->back()->withError($message);
        } catch (\Exception $e) {
            DB::rollback(); // Rollback the transaction in case of exception
            $message = $e->getMessage();
            return redirect()->back()->withError($message);
            exit;
        } catch (\QueryException $e) {
            DB::rollback(); // Rollback the transaction in case of exception
            return redirect()->back()->withError('Error in Database');
        }
    }
}
