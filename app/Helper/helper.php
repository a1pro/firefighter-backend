<?php

use App\Models\StandardIconCategory;
use App\Models\FaqCategory;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Support\Facades\DB;

if (!function_exists('getIconCategory')) {
    function getIconCategory($id)
    {
        // Ensure the query is correctly retrieving the category name
        $category = StandardIconCategory::where('id', $id)->first();
        return $category ? $category->category_name : 'Category Not Found'; // Return category name or default if not found
    }
}



// ==========================GET FAQ Category=====================================================

if (!function_exists('getFaqCategory')) {
    function getFaqCategory($id)
    {
        // Ensure the query is correctly retrieving the category name
        $category = FaqCategory::where('id', $id)->first();
        return $category ? $category->category_name : 'Category Not Found'; // Return category name or default if not found
    }
}


//======================================END==========================================================
// ===============================================================================
//-----------------------Get Agencies----------------------------------------
// ===============================================================================


if (!function_exists('getAgencies')) {
    function getAgencies()
    {
        // Ensure the query is correctly retrieving the category name
        $Agencies = Agency::all();
       //echo "<pre>"; print_r($Agencies);die;

        return $Agencies; // Return category name or default if not found
    }

// ===============================================================================
//********************************************************************************
//================================================================================


// ===============================================================================
//-----------------------Count Agency User----------------------------------------
// ===============================================================================
    if (!function_exists('agencyUserCount')) {
        function agencyUserCount($id)
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
    
// ===============================================================================
//********************************************************************************
//================================================================================





}



?>
