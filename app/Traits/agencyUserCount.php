<?php

namespace App\Traits;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Hash;
use DB;

trait agencyUserCount{
    public function agencyUserCount($id)
    {
        try 
        {
            DB::beginTransaction();
            $usercount = User::where('agency_id', $id)->count();
            DB::commit();
            return $usercount;  

        } 
        catch (\Exception $e) {
             
            DB::rollBack();
            Log::error("Something Went Wrong in Agency Count!!" . $e->getMessage());
            throw $e;
        }
    }
}
