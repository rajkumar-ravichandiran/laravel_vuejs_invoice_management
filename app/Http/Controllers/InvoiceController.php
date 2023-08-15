<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoices;
use App\Models\Customers;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all Invoices
        $invoices = Invoices::with('payments')->sortable()->OrderBy('created_at','DESC')->get();
        $parameters = count($_GET) != 0;
        // Pass the Invoices to the view
        return view('invoices.index', compact('invoices','parameters'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getInvoice($id)
    {
        $invoice = Invoices::with('customer','billingaddress','shippingaddress')->FindOrFail($id);
        if($invoice){
            return response()->json([
                'status' => true,
                'data'=> $invoice
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Invoice not found'
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Invoices $invoice)
    {
        $invoice->delete();
        return redirect()->back()->withStatus('Invoice Deleted Successfully');
    }

    public function addInvoice(Request $request)
    {
        try {            
            $validatedData = $request->validate([
                'invoice_number' => 'unique:invoices,number',
                'invoice_order' => 'unique:invoices,reference_number'                
            ]);
            DB::beginTransaction(); // Start a database transaction

            $invoice = new Invoices();
            if($request->has('invoice_customer')){
                $invoice->customer_id = (int)$request->invoice_customer;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->invoice_customer);
                if($customer){
                    $invoice->billing_address = $customer->billing_address;
                    $invoice->shipping_address = $customer->shipping_address;
                    $invoice->currency_id = $customer->currency_id;
                }                
            }
            if($request->has('invoice_order')){
                $invoice->reference_number = strip_tags($request->invoice_order);
            }
            if($request->has('invoice_date')){
                $invoice->date = strip_tags($request->invoice_date);
            }
            if($request->has('invoice_gst_no')){
                $invoice->gst_no = strip_tags($request->invoice_gst_no);
            }
            if($request->has('invoice_due_date')){
                $invoice->due_date = strip_tags($request->invoice_due_date);
            }
            if($request->has('invoice_discount')){
                $invoice->discount = strip_tags($request->invoice_discount);
            }
            if($request->has('invoice_discounted_amount')){
                $invoice->discounted_amount = strip_tags($request->invoice_discounted_amount);
            }
            if($request->has('invoice_discount_on')){
                $invoice->is_discount_before_tax = $request->invoice_discount_on;
            }
            if($request->has('invoice_discount_type')){
                $invoice->discount_type = strip_tags($request->invoice_discount_type);
            }
            if($request->has('invoice_item_details')){
                $invoice->line_items = json_encode($request->invoice_item_details);
            }
            if($request->has('invoice_shipping_charge')){
                $invoice->shipping_charge = strip_tags($request->invoice_shipping_charge);
            }
            if($request->has('invoice_adjustment_amount')){
                $invoice->adjustment = strip_tags($request->invoice_adjustment_amount);
            }
            if($request->has('invoice_adjustment_descp')){
                $invoice->adjustment_description = strip_tags($request->invoice_adjustment_descp);
            }
            if($request->has('invoice_net')){
                $invoice->net = strip_tags($request->invoice_net);
            }
            if($request->has('invoice_subtotal')){
                $invoice->sub_total = strip_tags($request->invoice_subtotal);
            }
            if($request->has('invoice_gst')){
                $invoice->tax_total = strip_tags($request->invoice_gst);
            }
            if($request->has('invoice_total')){
                $invoice->total = strip_tags($request->invoice_total);
                $invoice->balance = strip_tags($request->invoice_total);
            }
            if($request->has('invoice_payment_remarks')){
                $invoice->notes = strip_tags($request->invoice_payment_remarks);
            }
            if($request->has('invoice_terms')){
                $invoice->terms = strip_tags($request->invoice_terms);
            }
            $invoice->status = 'unpaid';
            $invoice->payment_made = 0;
            $invoice->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Invoice Created Successfully'
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
    public function updateInvoice($id, Request $request)
    {
        try {
            $invoice = Invoices::findOrFail($id);
            $validatedData = $request->validate([
                'invoice_number' => 'unique:invoices,number,'.$invoice->id.',id',
                'invoice_order' => 'unique:invoices,reference_number,'.$invoice->id.',id',
            ]);
            
            DB::beginTransaction(); // Start a database transaction

            if($invoice){
                if($request->has('invoice_customer')){
                $invoice->customer_id = (int)$request->invoice_customer;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->invoice_customer);
                    if($customer){
                        $invoice->billing_address = $customer->billing_address;
                        $invoice->shipping_address = $customer->shipping_address;
                        $invoice->currency_id = $customer->currency_id;
                    }                
                }
                if($request->has('invoice_order')){
                    $invoice->reference_number = strip_tags($request->invoice_order);
                }
                if($request->has('invoice_date')){
                    $invoice->date = strip_tags($request->invoice_date);
                }
                if($request->has('invoice_gst_no')){
                    $invoice->gst_no = strip_tags($request->invoice_gst_no);
                }
                if($request->has('invoice_due_date')){
                    $invoice->due_date = strip_tags($request->invoice_due_date);
                }
                if($request->has('invoice_discount')){
                    $invoice->discount = strip_tags($request->invoice_discount);
                }
                if($request->has('invoice_discounted_amount')){
                    $invoice->discounted_amount = strip_tags($request->invoice_discounted_amount);
                }
                if($request->has('invoice_discount_on')){
                    $invoice->is_discount_before_tax = $request->invoice_discount_on;
                }
                if($request->has('invoice_discount_type')){
                    $invoice->discount_type = strip_tags($request->invoice_discount_type);
                }
                if($request->has('invoice_item_details')){
                    $invoice->line_items = json_encode($request->invoice_item_details);
                }
                if($request->has('invoice_shipping_charge')){
                    $invoice->shipping_charge = strip_tags($request->invoice_shipping_charge);
                }
                if($request->has('invoice_adjustment_amount')){
                    $invoice->adjustment = strip_tags($request->invoice_adjustment_amount);
                }
                if($request->has('invoice_adjustment_descp')){
                    $invoice->adjustment_description = strip_tags($request->invoice_adjustment_descp);
                }
                if($request->has('invoice_net')){
                    $invoice->net = strip_tags($request->invoice_net);
                }
                if($request->has('invoice_subtotal')){
                    $invoice->sub_total = strip_tags($request->invoice_subtotal);
                }
                if($request->has('invoice_gst')){
                    $invoice->tax_total = strip_tags($request->invoice_gst);
                }
                if($request->has('invoice_total')){
                    $invoice->total = strip_tags($request->invoice_total);
                }
                if($request->has('invoice_payment_remarks')){
                    $invoice->notes = strip_tags($request->invoice_payment_remarks);
                }
                if($request->has('invoice_terms')){
                    $invoice->terms = strip_tags($request->invoice_terms);
                }
                $invoice->update();
                
                DB::commit(); // Commit the transaction
                
                return response()->json([
                    'status' => true,
                    'msg'=> 'Invoice Updated Successfully'
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
    public function getInvoicesList(Request $request)
    {
        try {            
           $invoices = Invoices::where('status','!=','paid')->orderBy('created_at', 'ASC');

            if ($request->has('x')) {
                $invoices = $invoices->where('customer_id',$request->x);
            }

            $invoices = $invoices->get();
            $invoicesArray = $invoices->toArray(); // Convert to key-value array

            return response()->json([
                'status' => true,
                'data' => $invoicesArray,
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
}
