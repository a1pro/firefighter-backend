<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserControllerApi;
use App\Http\Controllers\Api\RegisterController;
use App\Http\Controllers\Api\BuildingController;
use App\Http\Controllers\Api\BuildingOtpScreen;
use App\Http\Controllers\Api\DragIconOtpController;
use App\Http\Controllers\Api\SearchBuildingController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\DragIconController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});



/***=============2025 January 03 ========***/
//Frontend Api Endpoints ends here
Route::group(['namespace' => 'Api\V1', 'prefix' => 'v1', 'as' => 'v1.'], function () {
//BookingControler Api's Routes
 /***=============Notify   routes ========***/

 Route::post('/send-notification', [NotificationController::class, 'sendTestNotification']);

    /***=============login   routes ========***/


Route::post('/login',[AuthController::class,'login']);
Route::middleware('cors')->post('/react/login', [AuthController::class, 'login']);


Route::post('/register',[RegisterController::class,'store']);

Route::post('/send-sms', [RegisterController::class, 'sendSms']);

Route::post('/verify/registerotp', [RegisterController::class, 'verifyOtpLogin']);

Route::post('/send/forgot/passwordotp', [ForgotPasswordController::class, 'sendEmailOtp']);

Route::post('/verify/passwordotp', [ForgotPasswordController::class, 'verifyEmailOtp']);

Route::post('/reset/password', [ForgotPasswordController::class, 'setNewPassword']);


Route::group(['middleware' => ['auth:sanctum']], function () {

    // ======================Buiding Api=================================

Route::post('/add/building',[BuildingController::class,'add_building']);
Route::get('/get/building',[BuildingController::class,'getBuildingDetail']); 
Route::get('/get/building/test',[BuildingController::class,'getBuildingDetailtest']);
Route::get('/get/icons',[BuildingController::class,'getStandardIcons']);

Route::post('/get/gallery',[BuildingController::class,'GetBuildingGallery']);


Route::post('/floor/image/delete', [BuildingController::class, 'deleteFloorImage']);
Route::post('/basement/image/delete', [BuildingController::class, 'deleteBasementImage']);



Route::post('/search/building',[SearchBuildingController::class,'search_building']);

Route::post('/search/buildings',[BuildingController::class,'searchBuilding']);

Route::post('/update/building',[BuildingController::class,'edit_building']);

Route::post('/add/floor/detail',[BuildingController::class,'uploadFloorDetail']);

Route::post('/get/floor/gallery',[BuildingController::class,'getFloorDetailByBuilding']);
Route::post('/delete/floor/images',[BuildingController::class,'deleteFloorBasementImage']);

Route::post('/delete/floor/gallery',[BuildingController::class,'deleteBasement']);

// ===========================================Building OTP Controller==============================
Route::post('/send/building/otp',[BuildingOtpScreen::class,'buildingOtp']);
Route::post('/verify/building/otp',[BuildingOtpScreen::class,'verifyOtpBuilding']);


// ====================================Drag Icon Otp Controller======================================
Route::post('/drag/icon/opt/send',[DragIconOtpController::class,'dragIconOtp']);
Route::post('/drag/icon/opt/verify',[DragIconOtpController::class,'verifyOtpDragIcons']);


//==================================Icon Drag=====================================================
Route::post('/drag/icon/store',[DragIconController::class,'dragIconSave']);
Route::post('/get/drag/icon',[DragIconController::class,'getDragIcons']);


// ====================Drag Icon Delete=========================================
Route::post('/delete/drag/icon',[DragIconController::class,'deleteDragIcon']);


 
    // POST route with DragIconController
   
    Route::middleware('cors')->get('/get/drag/icon/react', [DragIconController::class, 'getDragReactIcons']);

 
    
        // GET route with inline response
    
 
//========================React Native Drag Icon Api=======================================
Route::post('/drag/icon/save',[DragIconController::class,'dragIconReactNSave']);
Route::post('drag/icon/update',[DragIconController::class,'dragIconUpdate']);
/***=============booking routes ========***/
Route::group(['prefix' => 'booking'], function () {
 
});


// =================Drag Icon Resize==============================
Route::post('drag/icon/resize/store',[DragIconController::class,'dragIconResize']);
Route::get('drag/icon/resize/get',[DragIconController::class,'getResizeIcons']);
Route::post('drag/icon/resize/delete',[DragIconController::class,'deleteDragIconResize']);




// =====================Drag Icon Resize End========================


// =================================FAQ===================================================

Route::get('/get/faq',[FaqController::class,'getFaq']);
Route::post('/search/faq',[FaqController::class,'searchFaq']);


//===================================END FAQ=============================================
/***============= get user details ========***/
    Route::get('/get_user_detail',[UserControllerApi::class,'getuser_detail']);

    Route::post('/update/profile',[UserControllerApi::class,'updateUserProfile']);

    Route::get('/user/profile',[UserControllerApi::class,'getUserProfile']);

    Route::post('/generateUserUpdate',[UserControllerApi::class,'generateUserUpdate']);
    
    Route::post('/logout',[AuthController::class,'logout']);


    


    Route::get('/user', function (Request $request) {
        return $request->user();
     });


     //

	});

});

Route::get('/kpltest/testapi', function () {
       echo "testing";
    }); 
