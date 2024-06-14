<?php

namespace App\Http\Controllers;

use App\Helpers\ApiFormatter;
use Illuminate\Http\Request;
use App\models\lending;
use App\models\Stuff;
use App\Models\Stuffstock;
use App\models\user;
use Illuminate\Support\Facades\Validator;

class lendingController extends Controller
{
    public function __construct()
{
    $this->middleware('auth:api');
}
    public function index(){
        $lending = lending::all();

        return response()->json([
            'success' => true,
            'message' => 'Lihat semua barang',
            'data' => $lending
        ],200);
    }

    public function store(Request $request){
        {
            try {
                $getLending = Lending::with('stuff', 'user')->get();
    
                return ApiFormatter::sendResponse(200, 'Succesfully Get All Lending Data', $getLending);
            }catch (\Exception $err) {
                return ApiFormatter::sendResponse(400, $err->getMessage());
            }
        }
    }
        public function show($id){
       try{
        $getLending = Lending::where('id', $id)->with('stuff', 'user')->first();

        if(!$getLending){
            return ApiFormatter::sendResponse(404, false, 'Date Lending not found');
        }else {
            return ApiFormatter::sendResponse(200, true, 'success', $getLending);
        }
       }catch(\Exception $e) {
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
       }
}

    public function destroy($id){
    try{
        $lending = lending::findOrFail($id);

        $lending->delete();

        return response()->json([
         'success' => true,
         'message' => 'Barang Hapus Data dengan id $id',
            'data' => $lending
        ],200);
    } catch(\Throwable $th){
        return response()->json([
        'success' => false,
        'message' => 'Proses gagal! data dengan id $id tidak ditemukan',
        ],400);
    }
}

public function update(Request $request, $id){
    try{
        $getLending = Lending::find($id);

        if ($getLending) {
            $this->validate($request, [
                'stuff_id' => 'required',
                'date_time'=> 'required',
                'name'=> 'required', 
                'user_id'=> 'required',
                'notes' => 'required',
                'total' => 'required'
            ]);

            $getStuffStock = Stuffstock::where('stuff_id', $request->stuff_id)
            ->first();
            $getCurrentStock = Stuffstock::where('stuff_id', $getLending['stuff_id'])
            ->first();

            if ($request->stuff_id == $getCurrentStock['stuff_id']){
                $updateStock = $getCurrentStock->update([
                    'total_available' => $getCurrentStock['total_available'] + 
                    $getLending('total_stuff') - $request->total_stuff,
                ]);
            }else{
                $updateStock = $getCurrentStock->update([
                    'total_available' => $getCurrentStock['total_available'] + 
                    $getLending['total_stuff']
                ]);
                $updateStock = $getCurrentStock->update([
                    'total_available' => $getStuffStock['total_available'] - 
                    $request['total_stuff']
                ]);
            }

            $updateLending = $getLending->update([
                'stuff_id' => $request->stuff_id,
                'date_time'=> $request->date_time,
                'name'=> $request->name,
                'user_id'=>$request->user_id,
                'notes'=>$request->notes,
                'total_stuff'=> $request->total_stuff
            ]);

            $getUpdateLending = Lending::where('id', $id)->with('stuff', 'user',
            'restoration')->first();

            return ApiFormatter::sendResponse(200, 'success', $getUpdateLending);
        }
    }catch(\Exception $e){
        return ApiFormatter::sendResponse(400, $e->getMessage());
    }
}
public function recycleBin()
{
    try {

        $lendingDeleted = Lending::onlyTrashed()->get();

        if (!$lendingDeleted) {
            return ApiFormatter::sendResponse(404, false, 'Deletd Data Lending Doesnt Exists');
        } else {
            return ApiFormatter::sendResponse(200, true, 'Successfully Get Delete All Lending Data', $lendingDeleted);
        }
    } catch (\Exception $e) {
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
    }
}

public function restore($id)
{
    try {

        $getLending = Lending::onlyTrashed()->where('id', $id);

        if (!$getLending) {
            return ApiFormatter::sendResponse(404, false, 'Restored Data Lending Doesnt Exists');
        } else {
            $restoreLending = $getLending->restore();

            if ($restoreLending) {
                $getRestore = Lending::find($id);
                $addStock = StuffStock::where('stuff_id', $getRestore['stuff_id'])->first();
                $updateStock = $addStock->update([
                    'total_available' => $addStock['total_available'] - $getRestore['total_stuff'],
                ]);

                return ApiFormatter::sendResponse(200, true, 'Successfully Restore A Deleted Lending Data', $getRestore);
            }
        }
    } catch (\Exception $e) {
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
    }
}

public function forceDestroy($id)
{
    try {

        $getLending = Lending::onlyTrashed()->where('id', $id);

        if (!$getLending) {
            return ApiFormatter::sendResponse(404, false, 'Data Lending for Permanent Delete Doesnt Exists');
        } else {
            $forceStuff = $getLending->forceDelete();

            if ($forceStuff) {
                return ApiFormatter::sendResponse(200, true, 'Successfully Permanent Delete A Lending Data');
            }
        }
    } catch (\Exception $e) {
        return ApiFormatter::sendResponse(400, false, $e->getMessage());
    }
}
}
