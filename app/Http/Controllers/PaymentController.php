<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Payments;
use App\Models\Invoices;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all Payments
        $payments = Payments::OrderBy('created_at','DESC')->paginate(10);
        $parameters = count($_GET) != 0;
        // Pass the Payments to the view
        return view('payments.index', compact('payments','parameters'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getPayment($id)
    {
        $payment = Payments::with('customer','invoice')->FindOrFail($id);
        if($payment){
            return response()->json([
                'status' => true,
                'data'=> $payment
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Payment not found'
            ]);
        }
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Payments $payment)
    {
        $payment->delete();
        return redirect()->back()->withStatus('Payment Deleted Successfully');
    }

    public function addPayment(Request $request)
    {
        try {            
            DB::beginTransaction(); // Start a database transaction

            $payment= new Payments();
            if($request->has('payment_customer_id')){
                $payment->customer_id = (int)$request->payment_customer_id;
            }
            if($request->has('payment_invoice_id')){
                $payment->invoice_id = (int)$request->payment_invoice_id;
                $invoice = Invoices::findOrFail($request->payment_invoice_id);
                $amountPaid = floatval($invoice->payment_made)+floatval($request->payment_amount);
                if($invoice){
                    if($amountPaid >= $invoice->total){
                        $status = 'paid';
                    }else{
                        $status = 'partially paid';
                    }
                    $invoice->status = $status;
                    $invoice->payment_made = $amountPaid;
                    $invoice->balance = $invoice->total - $amountPaid;
                    $invoice->update();                    
                }
            }
            if($request->has('payment_reference')){
                $payment->reference_number = strip_tags($request->payment_reference);
            }
            if($request->has('payment_mode')){
                $payment->payment_mode = strip_tags($request->payment_mode);
            }
            if($request->has('payment_amount')){
                $payment->amount = strip_tags($request->payment_amount);
            }
            if($request->has('bank_charges')){
                $payment->bank_charges = strip_tags($request->bank_charges);
            }
            if($request->has('payment_date')){
                $payment->date = strip_tags($request->payment_date);
            }
            if($request->has('payment_description')){
                $payment->description = strip_tags($request->payment_description);
            }
            $payment->status = 'paid';
            $payment->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Payment Created Successfully'
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
    public function updatePayment($id, Request $request)
    {
        try {
            $payment = Payments::findOrFail($id);
            
            DB::beginTransaction(); // Start a database transaction

            if($payment){
                if($request->has('payment_customer_id')){
                $payment->customer_id = (int)$request->payment_customer_id;
                }
                if($request->has('payment_invoice_id')){
                    $payment->invoice_id = (int)$request->payment_invoice_id;
                    $invoice = Invoices::findOrFail($request->payment_invoice_id);
                    if($invoice){
                        if($request->payment_amount > $invoice->total){
                            $status = 'paid';
                        }else{
                            $status = 'partially paid';
                        }
                        $invoice->status = $status;
                        $invoice->payment_made = $request->payment_amount;
                        $invoice->balance = $invoice->total - $request->payment_amount;
                        $invoice->update();                    
                    }
                }
                if($request->has('payment_reference')){
                    $payment->reference_number = strip_tags($request->payment_reference);
                }
                if($request->has('payment_mode')){
                    $payment->payment_mode = strip_tags($request->payment_mode);
                }
                if($request->has('payment_amount')){
                    $payment->amount = strip_tags($request->payment_amount);
                }
                if($request->has('bank_charges')){
                    $payment->bank_charges = strip_tags($request->bank_charges);
                }
                if($request->has('payment_date')){
                    $payment->date = strip_tags($request->payment_date);
                }
                if($request->has('payment_description')){
                    $payment->description = strip_tags($request->payment_description);
                }
                $payment->status = 'paid';
                $payment->update();
                
                DB::commit(); // Commit the transaction
                
                return response()->json([
                    'status' => true,
                    'msg'=> 'Payment Updated Successfully'
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
