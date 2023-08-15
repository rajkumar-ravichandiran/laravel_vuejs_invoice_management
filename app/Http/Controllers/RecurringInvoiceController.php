<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RecurringInvoices;
use Illuminate\Support\Facades\DB;
use App\Models\Customers;

class RecurringInvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all RecurringInvoices
        $recurringinvoices = RecurringInvoices::OrderBy('created_at','DESC')->paginate(10);
        $parameters = count($_GET) != 0;
        // Pass the RecurringInvoices to the view
        return view('recurringinvoices.index', compact('recurringinvoices','parameters'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getRecurringInvoice($id)
    {
        $recurringinvoice = RecurringInvoices::with('customer','billingaddress','shippingaddress')->FindOrFail($id);
        if($recurringinvoice){
            return response()->json([
                'status' => true,
                'data'=> $recurringinvoice
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Recurring Invoice not found'
            ]);
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(RecurringInvoices $recurringinvoice)
    {
        $recurringinvoice->delete();
        return redirect()->back()->withStatus('Recurring Invoice Deleted Successfully');
    }

    public function addRecurringInvoice(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'invoice_number' => 'unique:recurring_invoices,number',
                'invoice_order' => 'unique:recurring_invoices,reference_number'                
            ]);            
            DB::beginTransaction(); // Start a database transaction
            
            $invoice = new RecurringInvoices();
            if($request->has('recurring_customer_id')){
                $invoice->customer_id = (int)$request->recurring_customer_id;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->recurring_customer_id);
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
            if($request->has('recurring_start_date')){
                $invoice->start_date = strip_tags($request->recurring_start_date);
                $invoice->last_sent_date = strip_tags($request->recurring_start_date);
            }
            if($request->has('recurring_end_date')){
                $invoice->end_date = strip_tags($request->recurring_end_date);
            }
            if($request->has('recurring_cycle')){
                $invoice->billing_cycle = (int)$request->recurring_cycle;
            }
            if($request->has('recurring_cycle')){
                $givenDate = new \DateTime($request->recurring_start_date);
                if($request->recurring_cycle == '1'){
                    // Add one year
                    $oneYearLater = clone $givenDate;
                    $oneYearLater->modify('+1 year');
                    $invoice->next_invoice_date= $oneYearLater->format('Y-m-d');
                }else{
                    $oneMonthLater = clone $givenDate;
                    $oneMonthLater->modify('+1 month');
                    $invoice->next_invoice_date= $oneMonthLater->format('Y-m-d');
                }
                    
            }
            $invoice->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Recurring Invoice Created Successfully'
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
    public function updateRecurringInvoice($id, Request $request)
    {
        try {
            $invoice = RecurringInvoices::findOrFail($id);
            
            DB::beginTransaction(); // Start a database transaction

            if($invoice){
                if($request->has('recurring_customer_id')){
                $invoice->customer_id = (int)$request->recurring_customer_id;
                $customer = Customers::with('billingaddress','shippingaddress')->FindOrFail($request->recurring_customer_id);
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
            if($request->has('recurring_start_date')){
                $invoice->start_date = strip_tags($request->recurring_start_date);
                $invoice->last_sent_date = strip_tags($request->recurring_start_date);
            }
            if($request->has('recurring_end_date')){
                $invoice->end_date = strip_tags($request->recurring_end_date);
            }
            if($request->has('recurring_cycle')){
                $invoice->billing_cycle = (int)$request->recurring_cycle;
            }
            if($request->has('recurring_cycle')){
                $givenDate = new \DateTime($request->recurring_start_date);
                if($request->recurring_cycle == '1'){
                    // Add one year
                    $oneYearLater = clone $givenDate;
                    $oneYearLater->modify('+1 year');
                    $invoice->next_invoice_date= $oneYearLater->format('Y-m-d');
                }else{
                    $oneMonthLater = clone $givenDate;
                    $oneMonthLater->modify('+1 month');
                    $invoice->next_invoice_date= $oneMonthLater->format('Y-m-d');
                }
                    
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
}
