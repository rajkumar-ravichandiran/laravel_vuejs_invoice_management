<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Items;
use Illuminate\Support\Facades\DB;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // Retrieve all Items
        $items = Items::sortable()->OrderBy('created_at','DESC')->paginate(10);
        $parameters = count($_GET) != 0;
        // Pass the items to the view
        return view('items.index', compact('items','parameters'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getItem($id)
    {
        $item = Items::FindOrFail($id);
        if($item){
            return response()->json([
                'status' => true,
                'data'=> $item
            ]);
        }else{
            return response()->json([
                'status' => false,
                'msg'=> 'Item not found'
            ]);
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Items $item)
    {
        $item->delete();
        return redirect()->back()->withStatus('Item Deleted Successfully');
    }

    public function addItem(Request $request)
    {
        try {            
            $validatedData = $request->validate([
                'item_name' => 'unique:items,name','item_sku' => 'unique:items,sku'                
            ]);
            DB::beginTransaction(); // Start a database transaction

            $item = new Items();
            if($request->has('item_name')){
                $item->name = strip_tags($request->item_name);
            }
            if($request->has('item_rate')){
                $item->rate = strip_tags($request->item_rate);
            }
            if($request->has('item_description')){
                $item->description = strip_tags($request->item_description);
            }
            if($request->has('item_tax_id')){
                $item->tax_id = strip_tags($request->item_tax_id);
            }
            if($request->has('item_sku')){
                $item->sku = strip_tags($request->item_sku);
            }
            if($request->has('item_type')){
                $item->type = (int)$request->item_type;
            }
            if($request->has('item_is_taxable')){
                $item->is_taxable = (int)$request->item_is_taxable;
            }
            if($request->has('item_hsn')){
                $item->hsn_or_sac = strip_tags($request->item_hsn);
            }
            $item->save();

            DB::commit(); // Commit the transaction

            return response()->json([
                'status' => true,
                'msg'=> 'Item Created Successfully'
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
    public function updateItem($id, Request $request)
    {
        try {
            $item = Items::findOrFail($id);
            $validatedData = $request->validate([
                'item_name' => 'unique:items,name,'.$item->id.',id',
                'item_sku' => 'unique:items,sku,'.$item->id.',id',
            ]);
            
            DB::beginTransaction(); // Start a database transaction

            if($item){
                if($request->has('item_name')){
                $item->name = strip_tags($request->item_name);
                }
                if($request->has('item_rate')){
                    $item->rate = strip_tags($request->item_rate);
                }
                if($request->has('item_description')){
                    $item->description = strip_tags($request->item_description);
                }
                if($request->has('item_tax_id')){
                    $item->tax_id = strip_tags($request->item_tax_id);
                }
                if($request->has('item_sku')){
                    $item->sku = strip_tags($request->item_sku);
                }
                if($request->has('item_type')){
                    $item->type = (int)$request->item_type;
                }
                if($request->has('item_is_taxable')){
                    $item->is_taxable = (int)$request->item_is_taxable;
                }
                if($request->has('item_hsn')){
                    $item->hsn_or_sac = strip_tags($request->item_hsn);
                }
                $item->update();
                
                DB::commit(); // Commit the transaction
                
                return response()->json([
                    'status' => true,
                    'msg'=> 'Item Updated Successfully'
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
    public function getItemsList(Request $request)
    {
        try {            
           $items = Items::where('active','1')->orderBy('name', 'ASC');

            if ($request->has('q')) {
                $items = $items->where('name', 'like', '%' . $request->q . '%');
            }

            $items = $items->get();
            $itemsArray = $items->toArray(); // Convert to key-value array

            return response()->json([
                'status' => true,
                'data' => $itemsArray,
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
