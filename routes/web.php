<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AIWebsiteController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthGoogleController;
use App\Http\Controllers\BuilderController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CompanyEmailTemplateController;
use App\Http\Controllers\CompanySettingsController;
use App\Http\Controllers\CronJobsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\FeatureController;
use App\Http\Controllers\FileManagerController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\Install\InstallController;
use App\Http\Controllers\Install\UpdateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\PaymentMethodController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\Select2Controller;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\SystemController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UtilityController;
use App\Http\Controllers\WebsiteController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['install']], function () {

    Auth::routes();
    Route::get('sign_up', [WebsiteController::class, 'sign_up']);
    Route::get('site/contactus', [WebsiteController::class, 'contactus']);
    Route::get('site/{page}', [WebsiteController::class, 'site']);
    Route::post('contact/send_message', [WebsiteController::class, 'send_message']);

    Route::get('/logout', [LoginController::class, 'logout']);
    Route::match(['get', 'post'],'register/client_signup', [RegisterController::class, 'client_signup']);

    Route::group(['middleware' => ['auth','verified']], function () {

        Route::get('/dashboard', [DashboardController::class, 'index']);

        //Profile Controller
        Route::get('profile/edit', [ProfileController::class, 'edit']);
        Route::post('profile/update', [ProfileController::class, 'update']);
        Route::get('profile/change_password', [ProfileController::class, 'change_password']);
        Route::post('profile/update_password', [ProfileController::class, 'update_password']);

        //Membertship Controller
        Route::get('membership/my_subscription', [MembershipController::class, 'my_subscription']);  //View Subscription Details
        Route::get('membership/extend', [MembershipController::class, 'extend']);

        //Select Payment Gateway
        Route::post('membership/pay', [MembershipController::class, 'pay']);

        //Payment Gateway PayPal
        Route::get('membership/paypal/{action?}', [MembershipController::class, 'paypal']);

        //Payment Gateway Stripe
        Route::get('membership/stripe_payment/{action}/{payment_id?}', [MembershipController::class, 'stripe_payment']);

        //Payment Gateway RazorPay
        Route::post('membership/razorpay_payment/{payment_id}', [MembershipController::class, 'razorpay_payment']);

        //Paystack Payment Gateway
        Route::get('membership/paystack_payment/{payment_id}/{reference}', [MembershipController::class, 'paystack_payment']);

        //FlutterWave Payment GateWay
        Route::get('membership/flutterwave_payment/{payment_id}/{reference}', [MembershipController::class, 'flutterwave_payment']);

        //Billplz Gateway RazorPay
        Route::post('membership/billplz_payment/{payment_id}', [MembershipController::class, 'billplz_payment']);
        Route::get('membership/billplz_success/{payment_id}', [MembershipController::class, 'billplz_success']);

        //Paddle Payment Gateway
        Route::post('membership/Paddle_payment/{payment_id}', [MembershipController::class, 'Paddle_payment']);
        Route::get('membership/Paddle_success/{payment_id}', [MembershipController::class, 'Paddle_success']);

        /** Admin Only Route **/
        Route::group(['middleware' => ['admin']], function () {
            //User Controller
            Route::get('users/type/{user_type}', [UserController::class, 'index']);
            Route::resource('users', UserController::class);

            //Payment Controller
            Route::get('offline_payment/create', [PaymentController::class, 'create_offline_payment']);
            Route::post('offline_payment/store', [PaymentController::class, 'store_offline_payment']);
            Route::get('members/payment_history', [PaymentController::class, 'payment_history']);

            //Feature Controller
            Route::resource('features', FeatureController::class);

            //FAQ Controller
            Route::resource('faqs', FaqController::class);

            //Package Controller
            Route::resource('packages', PackageController::class);

            //Language Controller
            Route::resource('languages', LanguageController::class);

            //Utility Controller
            Route::match(['get', 'post'],'administration/general_settings/{store?}', function(Request $request, $store = null) {
                return app('App\Http\Controllers\UtilityController')->settings($store, $request);
            });
            Route::match(['get', 'post'],'administration/theme_option/{store?}', [UtilityController::class, 'theme_option']);
            Route::post('administration/upload_logo', [UtilityController::class, 'upload_logo']);
            Route::match(['get', 'patch'],'administration/currency_rates/{id?}', [UtilityController::class, 'currency_rates']);
            Route::get('administration/backup_database', [UtilityController::class, 'backup_database']);
            // System Support
            Route::get('administration/system_support', [UtilityController::class, 'support_info']);

            //Theme Option
            Route::match(['get', 'post'],'administration/theme_option/{store?}', function(Request $request, $store = null) {
                return app('App\Http\Controllers\UtilityController')->theme_option($store, $request);
            });

            //Email Template
            Route::resource('email_templates', EmailTemplateController::class)->only([
                'index', 'show', 'edit', 'update'
            ]);

            Route::get('/system-update', [SystemController::class, 'getSystemUpdate'])->name('system.update');
            Route::post('/system-update', [SystemController::class, 'postSystemUpdate'])->name('post.system.update');

        });

        Route::group(['middleware' => ['company']], function () {

            //Project Controller
            Route::post('projects/get_table_data', [ProjectController::class, 'get_table_data']);
            Route::resource('projects', ProjectController::class);

            //Builder
            Route::resource('project/builder', BuilderController::class);
            Route::get('updateproject/builder/{id}', [BuilderController::class, 'index']);
            Route::get('project/larabuilder', [BuilderController::class, 'larabuilder']);
            Route::get('builder/novi', [BuilderController::class, 'novi']);
            Route::get('builder/lara', [BuilderController::class, 'lara'])->name('builder.lara');
            Route::match(['get', 'post'],'api/ajax', [BuilderController::class, 'ajax']);
            Route::get('test/backend/assets/builder', [BuilderController::class, 'empty']);

            Route::post('/img/remove', [ProjectController::class, 'imgRemove'])->name('delete.image');
            Route::post('/video/remove', [ProjectController::class, 'videoRemove'])->name('delete.video');

            //Payment Method
            Route::resource('payment_methods', PaymentMethodController::class);

            //Staff Controller
            Route::resource('staffs', StaffController::class);

            //User Roles
            Route::resource('roles', RoleController::class);

            //File Manager Controller
            Route::get('file_manager/directory/{parent_id}', [FileManagerController::class, 'index'])->name('file_manager.index');
            Route::get('file_manager/create_folder/{parent_id?}', [FileManagerController::class, 'create_folder'])->name('file_manager.create_folder');
            Route::post('file_manager/store_folder', [FileManagerController::class, 'store_folder'])->name('file_manager.create_folder');
            Route::get('file_manager/edit_folder/{id}', [FileManagerController::class, 'edit_folder'])->name('file_manager.edit_folder');
            Route::patch('file_manager/update_folder/{id}', [FileManagerController::class, 'update_folder'])->name('file_manager.edit_folder');
            Route::get('file_manager/create/{parent_id?}', [FileManagerController::class, 'create'])->name('file_manager.create');
            Route::resource('file_manager', FileManagerController::class);

            //Company Settings Controller
            Route::post('company/upload_logo', [CompanySettingsController::class, 'upload_logo'])->name('company.change_logo');
            Route::match(['get', 'post'],'company/general_settings/{store?}', [CompanySettingsController::class, 'settings'])->name('company.change_settings');

            Route::match(['get', 'post'],'company/crm_settings', [CompanySettingsController::class, 'crm_settings'])->name('company.crm_settings');

            //Company Email Template
            Route::get('company_email_template/get_template/{id}', [CompanyEmailTemplateController::class, 'get_template']);
            Route::resource('company_email_template', CompanyEmailTemplateController::class);

            //Permission Controller
            Route::get('permission/control/{user_id?}', [PermissionController::class, 'index'])->name('permission.manage');
            Route::post('permission/store', [PermissionController::class, 'store'])->name('permission.manage');

        });

        Route::group(['middleware' => ['client']], function () {

            //Projects
            Route::get('client/projects', [ClientController::class, 'projects']);
            Route::get('client/projects/{id}', [ClientController::class, 'view_project']);

        });

    });

    //Convert Currency
    Route::get('convert_currency/{from}/{to}/{amount}', [AccountController::class, 'convert_currency']);

    //Get Client Info
//    Route::get('contacts/get_client_info/{id}', [ContactController::class, 'get_client_info']);

    //Get Client Info
    Route::get('projects/get_project_info/{id}', [ProjectController::class, 'get_project_info']);

    //View Invoice & Quotation without login
    Route::get('client/view_invoice/{id}', [ClientController::class, 'view_invoice']);

    //Online Invoice Payment
    Route::get('client/invoice_payment/{id}/{payment_method}', [ClientController::class, 'invoice_payment']);

    //Stripe Payment Gateway
    Route::get('client/stripe_payment/{action}/{invoice_id}', [ClientController::class, 'stripe_payment']);

    //PayPal Payment Gateway
    Route::get('client/paypal/{action?}/{invoice_id?}', [ClientController::class, 'paypal']);

    //Payment Gateway RazorPay
    Route::post('client/razorpay_payment/{invoice_id}', [ClientController::class, 'razorpay_payment']);

    //Paystack Payment Gateway
    Route::get('client/paystack_payment/{invoice_id}/{reference}', [ClientController::class, 'paystack_payment']);

    //Invoice & Quotation PDF Download
    Route::get('invoices/download_pdf/{id}', [ClientController::class, 'download_invoice_pdf']);

});

Route::get('/installation', [InstallController::class, 'index']);
Route::get('install/database', [InstallController::class, 'database']);
Route::post('install/process_install', [InstallController::class, 'process_install']);
Route::get('install/create_user', [InstallController::class, 'create_user']);
Route::post('install/store_user', [InstallController::class, 'store_user']);
Route::get('install/system_settings', [InstallController::class, 'system_settings']);
Route::post('install/finish', [InstallController::class, 'final_touch']);

//Ajax Select2 Controller
Route::get('ajax/get_table_data', [Select2Controller::class, 'get_table_data']);

Route::post('createProject', [ApiController::class, 'createProject']);
Route::post('updateProject/{id}', [ApiController::class, 'updateProject']);

//Show Notification
Route::get('notification/{id}', [NotificationController::class, 'show'])->middleware('auth');

//Google Login
Route::get('google/redirect', [SocialAuthGoogleController::class, 'redirect']);
Route::get('google/callback', [SocialAuthGoogleController::class, 'callback']);

//Update System
Route::get('migration/update', [UpdateController::class, 'update_migration']);

//PayPal IPN for Membership Payment
Route::post('membership/paypal_ipn', [MembershipController::class, 'paypal_ipn']);

//PayPal IPN for Invoice Payment
Route::post('client/paypal_ipn', [ClientController::class, 'paypal_ipn']);

Route::get('console/run', [CronJobsController::class, 'run']);

Route::middleware(['auth'])->group(function () {
    // AI Generator
    Route::get('/larabuilder/ai-generator', [AIWebsiteController::class, 'showGenerationForm'])
        ->name('larabuilder.ai-generator');
    Route::post('/larabuilder/generate-website', [AIWebsiteController::class, 'generateWebsite'])
        ->name('larabuilder.generate-website');
    Route::post('/larabuilder/generate-section', [AIWebsiteController::class, 'generateSection'])
        ->name('larabuilder.generate-section');

    // Builder & Preview
    Route::get('/website-editor/{id}', [BuilderController::class, 'larabuilderEditor'])
        ->name('larabuilder.editor');
    Route::get('/render-website-preview/{id}', [BuilderController::class, 'renderPreview'])
        ->name('projects.renderPreview');
    Route::get('website-editor/{id}', [BuilderController::class, 'larabuilderEditor'])->name('larabuilder.editor');
    Route::post('/update-project/{id}', [AIWebsiteController::class, 'updateProject']);
    Route::get('/delete-project/{id}', [ProjectController::class, 'deleteProject'])->name('projects.delete');

    // Image upload route
    Route::post('/upload-images', [ImageController::class, 'uploadImages'])
        ->name('images.upload');

    // Download Unsplash image route
    Route::post('/download-unsplash-image', [ImageController::class, 'downloadUnsplashImage'])
        ->name('images.download-unsplash');

    // Get project images route
    Route::get('/project-images/{project}', [ImageController::class, 'getProjectImages'])
        ->name('images.project');

    // Delete image route
    Route::delete('/images/{image}', [ImageController::class, 'deleteImage'])
        ->name('images.delete');
});

//Route::group(['middleware' => ['install']], function () {
//
//	    Auth::routes();
//	Route::get('sign_up', 'WebsiteController@sign_up');
//	Route::get('site/contactus', 'WebsiteController@contactus');
//	Route::get('site/{page}', 'WebsiteController@site');
//	Route::post('contact/send_message', 'WebsiteController@send_message');
//
//	Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');
//    Route::match(['get', 'post'],'register/client_signup','\App\Http\Controllers\Auth\RegisterController@client_signup');
//
//	Route::group(['middleware' => ['auth','verified']], function () {
//
//		Route::get('/dashboard', 'DashboardController@index');
//
//		//Profile Controller
//		Route::get('profile/edit', 'ProfileController@edit');
//		Route::post('profile/update', 'ProfileController@update');
//		Route::get('profile/change_password', 'ProfileController@change_password');
//		Route::post('profile/update_password', 'ProfileController@update_password');
//
//		//Membertship Controller
//		Route::get('membership/my_subscription', 'MembershipController@my_subscription');  //View Subscription Details
//		Route::get('membership/extend', 'MembershipController@extend');
//
//		//Select Payment Gateway
//		Route::post('membership/pay','MembershipController@pay');
//
//		//Payment Gateway PayPal
//		Route::get('membership/paypal/{action?}','MembershipController@paypal');
//
//		//Payment Gateway Stripe
//		Route::get('membership/stripe_payment/{action}/{payment_id?}','MembershipController@stripe_payment');
//
//		//Payment Gateway RazorPay
//		Route::post('membership/razorpay_payment/{payment_id}','MembershipController@razorpay_payment');
//
//		//Paystack Payment Gateway
//		Route::get('membership/paystack_payment/{payment_id}/{reference}','MembershipController@paystack_payment');
//
//        //FlutterWave Payment GateWay
//		Route::get('membership/flutterwave_payment/{payment_id}/{reference}','MembershipController@flutterwave_payment');
//
//		//Billplz Gateway RazorPay
//		Route::post('membership/billplz_payment/{payment_id}','MembershipController@billplz_payment');
//		Route::get('membership/billplz_success/{payment_id}','MembershipController@billplz_success');
//
//		//Paddle Payment Gateway
//		Route::post('membership/Paddle_payment/{payment_id}','MembershipController@Paddle_payment');
//		Route::get('membership/Paddle_success/{payment_id}','MembershipController@Paddle_success');
//
//		/** Admin Only Route **/
//		Route::group(['middleware' => ['admin']], function () {
//			//User Controller
//			Route::get('users/type/{user_type}','UserController@index');
//			Route::resource('users','UserController');
//
//
//            //Payment Controller
//			Route::get('offline_payment/create','PaymentController@create_offline_payment');
//			Route::post('offline_payment/store','PaymentController@store_offline_payment');
//			Route::get('members/payment_history','PaymentController@payment_history');
//
//			//Feature Controller
//			Route::resource('features','FeatureController');
//
//			//FAQ Controller
//			Route::resource('faqs','FaqController');
//
//			//Package Controller
//			Route::resource('packages','PackageController');
//
//			//Language Controller
//			Route::resource('languages','LanguageController');
//
//			//Utility Controller
//			Route::match(['get', 'post'],'administration/general_settings/{store?}', function(Request $request, $store = null) {
//				return app('App\Http\Controllers\UtilityController')->settings($store, $request);
//			});
//			Route::match(['get', 'post'],'administration/theme_option/{store?}', 'UtilityController@theme_option');
//			Route::post('administration/upload_logo', 'UtilityController@upload_logo');
//			Route::match(['get', 'patch'],'administration/currency_rates/{id?}', 'UtilityController@currency_rates');
//			Route::get('administration/backup_database', 'UtilityController@backup_database');
//			// System Support
//			Route::get('administration/system_support', 'UtilityController@support_info');
//
//			//Theme Option
//			Route::match(['get', 'post'],'administration/theme_option/{store?}', function(Request $request, $store = null) {
//				return app('App\Http\Controllers\UtilityController')->theme_option($store, $request);
//			});
//
//			//Email Template
//			Route::resource('email_templates','EmailTemplateController')->only([
//				'index', 'show', 'edit', 'update'
//			]);
//
//
//			Route::get('/system-update', 'SystemController@getSystemUpdate')->name('system.update');
//			Route::post('/system-update', 'SystemController@postSystemUpdate')->name('post.system.update');
//
//		});
//
//		Route::group(['middleware' => ['company']], function () {
//
//
//			//Project Controller
//			Route::post('projects/get_table_data','ProjectController@get_table_data');
//			Route::resource('projects','ProjectController');
//
//			//Builder
//			Route::resource('project/builder','BuilderController');
//			Route::get('updateproject/builder/{id}','BuilderController@index');
//			Route::get('project/larabuilder','BuilderController@larabuilder');
//			Route::get('builder/novi','BuilderController@novi');
//			Route::get('builder/lara','BuilderController@lara')->name('builder.lara');
//			Route::match(['get', 'post'],'api/ajax','BuilderController@ajax');
//			Route::get('test/backend/assets/builder','BuilderController@empty');
//
//			Route::post('/img/remove','ProjectController@imgRemove')->name('delete.image');
//			Route::post('/video/remove','ProjectController@videoRemove')->name('delete.video');
//
//			//Payment Method
//			Route::resource('payment_methods','PaymentMethodController');
//
//			//Staff Controller
//			Route::resource('staffs','StaffController');
//
//			//User Roles
//			Route::resource('roles','RoleController');
//
//			//File Manager Controller
//			Route::get('file_manager/directory/{parent_id}','FileManagerController@index')->name('file_manager.index');
//			Route::get('file_manager/create_folder/{parent_id?}','FileManagerController@create_folder')->name('file_manager.create_folder');
//			Route::post('file_manager/store_folder','FileManagerController@store_folder')->name('file_manager.create_folder');
//			Route::get('file_manager/edit_folder/{id}','FileManagerController@edit_folder')->name('file_manager.edit_folder');
//			Route::patch('file_manager/update_folder/{id}','FileManagerController@update_folder')->name('file_manager.edit_folder');
//			Route::get('file_manager/create/{parent_id?}','FileManagerController@create')->name('file_manager.create');
//			Route::resource('file_manager','FileManagerController');
//
//
//			//Company Settings Controller
//			Route::post('company/upload_logo', 'CompanySettingsController@upload_logo')->name('company.change_logo');
//			Route::match(['get', 'post'],'company/general_settings/{store?}', 'CompanySettingsController@settings')->name('company.change_settings');
//
//			Route::match(['get', 'post'],'company/crm_settings', 'CompanySettingsController@crm_settings')->name('company.crm_settings');
//
//			//Company Email Template
//			Route::get('company_email_template/get_template/{id}','CompanyEmailTemplateController@get_template');
//			Route::resource('company_email_template','CompanyEmailTemplateController');
//
//			//Permission Controller
//			Route::get('permission/control/{user_id?}', 'PermissionController@index')->name('permission.manage');
//			Route::post('permission/store', 'PermissionController@store')->name('permission.manage');
//
//
//		});
//
//		Route::group(['middleware' => ['client']], function () {
//
//		    //Projects
//		    Route::get('client/projects','ClientController@projects');
//			Route::get('client/projects/{id}','ClientController@view_project');
//
//		});
//
//
//	});
//
//
//
//    //Convert Currency
//	Route::get('convert_currency/{from}/{to}/{amount}','AccountController@convert_currency');
//
//	//Get Client Info
//	Route::get('contacts/get_client_info/{id}','ContactController@get_client_info');
//
//	//Get Client Info
//	Route::get('projects/get_project_info/{id}','ProjectController@get_project_info');
//
//	//View Invoice & Quotation without login
//	Route::get('client/view_invoice/{id}','ClientController@view_invoice');
//
//	//Online Invoice Payment
//	Route::get('client/invoice_payment/{id}/{payment_method}','ClientController@invoice_payment');
//
//	//Stripe Payment Gateway
//	Route::get('client/stripe_payment/{action}/{invoice_id}','ClientController@stripe_payment');
//
//	//PayPal Payment Gateway
//	Route::get('client/paypal/{action?}/{invoice_id?}','ClientController@paypal');
//
//	//Payment Gateway RazorPay
//	Route::post('client/razorpay_payment/{invoice_id}','ClientController@razorpay_payment');
//
//	//Paystack Payment Gateway
//	Route::get('client/paystack_payment/{invoice_id}/{reference}','ClientController@paystack_payment');
//
//	//Invoice & Quotation PDF Download
//	Route::get('invoices/download_pdf/{id}','ClientController@download_invoice_pdf');
//
//
//});
//
//Route::get('/installation', 'Install\InstallController@index');
//Route::get('install/database', 'Install\InstallController@database');
//Route::post('install/process_install', 'Install\InstallController@process_install');
//Route::get('install/create_user', 'Install\InstallController@create_user');
//Route::post('install/store_user', 'Install\InstallController@store_user');
//Route::get('install/system_settings', 'Install\InstallController@system_settings');
//Route::post('install/finish', 'Install\InstallController@final_touch');
//
////Ajax Select2 Controller
//Route::get('ajax/get_table_data','Select2Controller@get_table_data');
//
//Route::post('createProject','ApiController@createProject');
//Route::post('updateProject/{id}','ApiController@updateProject');
//
////Show Notification
//Route::get('notification/{id}','NotificationController@show')->middleware('auth');
//
////Google Login
//Route::get('google/redirect', 'Auth\SocialAuthGoogleController@redirect');
//Route::get('google/callback', 'Auth\SocialAuthGoogleController@callback');
//
////Update System
//Route::get('migration/update', 'Install\UpdateController@update_migration');
//
////PayPal IPN for Membership Payment
//Route::post('membership/paypal_ipn','MembershipController@paypal_ipn');
//
////PayPal IPN for Invoice Payment
//Route::post('client/paypal_ipn','ClientController@paypal_ipn');
//
//Route::get('console/run','CronJobsController@run');
//
//Route::middleware(['auth'])->group(function () {
//    // AI Generator
//    Route::get('/larabuilder/ai-generator', [AIWebsiteController::class, 'showGenerationForm'])
//        ->name('larabuilder.ai-generator');
//    Route::post('/larabuilder/generate-website', [AIWebsiteController::class, 'generateWebsite'])
//        ->name('larabuilder.generate-website');
//    Route::post('/larabuilder/generate-section', [AIWebsiteController::class, 'generateSection'])
//        ->name('larabuilder.generate-section');
//
//    // Builder & Preview
//    Route::get('/website-editor/{id}', [BuilderController::class, 'larabuilderEditor'])
//        ->name('larabuilder.editor');
//    Route::get('/render-website-preview/{id}', [BuilderController::class, 'renderPreview'])
//        ->name('projects.renderPreview');
//    Route::get('website-editor/{id}', [BuilderController::class, 'larabuilderEditor'])->name('larabuilder.editor');
//    Route::post('/update-project/{id}', [AIWebsiteController::class, 'updateProject']);
//    Route::get('/delete-project/{id}', [ProjectController::class, 'deleteProject'])->name('projects.delete');
//
//    // Image upload route
//    Route::post('/upload-images', [ImageController::class, 'uploadImages'])
//        ->name('images.upload');
//
//    // Download Unsplash image route
//    Route::post('/download-unsplash-image', [ImageController::class, 'downloadUnsplashImage'])
//        ->name('images.download-unsplash');
//
//    // Get project images route
//    Route::get('/project-images/{project}', [ImageController::class, 'getProjectImages'])
//        ->name('images.project');
//
//    // Delete image route
//    Route::delete('/images/{image}', [ImageController::class, 'deleteImage'])
//        ->name('images.delete');
//});


//Route::middleware(['auth'])->group(function () {
//
//    // AI Generation Routes
//    Route::get('larabuilder/ai-generator', [AIWebsiteController::class, 'showGenerationForm'])->name('larabuilder.ai-generator');
//    Route::post('/generate-website', [AIWebsiteController::class, 'generateWebsite'])->name('larabuilder.generate-website');
//    Route::post('/generate-section', [AIWebsiteController::class, 'generateSection'])->name('larabuilder.generate-section');
//
//    // Preview Routes
//    Route::get('/preview/{project}', [AIWebsiteController::class, 'showPreview'])->name('larabuilder.preview');
//    Route::get('/preview/{project}/render', [AIWebsiteController::class, 'renderPreview'])->name('larabuilder.preview.render');
//    Route::post('/preview/{project}/accept', [AIWebsiteController::class, 'acceptPreview'])->name('larabuilder.preview.accept');
//    Route::post('/preview/{project}/regenerate', [AIWebsiteController::class, 'regenerateWebsite'])->name('larabuilder.preview.regenerate');
//
//    // Debug Route (remove in production)
//    Route::get('/debug-ai', [AIWebsiteController::class, 'debugAI'])->name('larabuilder.debug-ai');
//});