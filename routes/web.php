<?php

use App\Http\Controllers\Auth\TwoFactorAuth;
use Illuminate\Support\Facades\Route;

Auth::routes(['register' => false]);

Route::redirect('/', '/azaniaportal/login');

Route::redirect('/home', '/admin');
Route::middleware('auth')->prefix('two_factor_authentication')->group(function () {
    Route::get('/', [TwoFactorAuth::class, 'index'])->name('two_factor_authentication.index');
    Route::post('/verify', [TwoFactorAuth::class, 'verifyOtp'])->name('two-fa.verify');
    Route::get('/resend', [TwoFactorAuth::class, 'resendOtp'])->name('two-fa.resend');
});
Route::get('change_password', 'Auth\ChangePasswordController@index')->name('change_password');
Route::post('update_password', 'Auth\ChangePasswordController@updatePassword')->name('update-password');

//recover password
Route::get('forgot_password', 'Auth\ForgotPasswordController@index')->name('forgot-password');
Route::post('recover/update_password', 'Auth\ForgotPasswordController@updatePassword')->name('recover-update-password');
Route::post('send_link', 'Auth\ForgotPasswordController@sendResetLink')->name('send-link');
Route::get('set_credentials', 'Auth\ForgotPasswordController@showResetForm')->name('set-credentials');


Route::group(['prefix' => 'admin', 'as' => 'admin.', 'namespace' => 'Admin', 'middleware' => ['auth', 'verified']], function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('change-password', 'HomeController@changePassword')->name('admin-change-password');
    Route::post('update-password', 'HomeController@updatePassword')->name('admin-update-password');
    Route::get('roles/user', 'permission\UserPermission@index')->name('home');
    Route::post('roles/asyc/add', 'permission\ajax\PermissionController@addRole')->name('add-role-permission');
    Route::post('roles/asyc/role/permission/update', 'permission\ajax\PermissionController@updateRolePermission')->name('update-role-permission');
    Route::post('roles/asyc/role/user/update', 'permission\ajax\UserGroupsController@updateRoleUser')->name('update-role-user');
    Route::get('roles/asyc/list', 'permission\ajax\PermissionController@listRoles')->name('list-role');
    Route::get('roles/asyc/role/{role_id}', 'permission\ajax\PermissionController@getRolePermissions')->name('get-role-permission');
    Route::get('roles/asyc/user/{user_id}', 'permission\ajax\UserGroupsController@getRolebasedOnUser')->name('admin.roles.user');

    Route::get('device/create', 'UsersController@deviceCreateView');
    Route::get('devices', 'UsersController@getDevices');
    Route::post('device/store', 'UsersController@storeDevice');
    Route::get('devices/{id}', 'UsersController@editDevice');
    Route::put('device/update', 'UsersController@updateDevice');
    Route::post('activate', 'UsersController@activateDevice');
    Route::post('deactivate', 'UsersController@deactivateDevice');

    Route::post('permissions', 'PermissionsController@store');
    Route::get('permissions/{id}', 'PermissionsController@edit');
    Route::post('permissions/edit', 'PermissionsController@update');
    Route::post('permissions/delete', 'PermissionsController@destroy');
    
    Route::post('roles', 'RolesController@store');
    Route::post('roles/delete', 'RolesController@destroy');
    Route::get('roles/{id}', 'RolesController@edit');
    Route::post('roles/edit', 'RolesController@update');
    Route::resource('permissions', 'PermissionsController');
    Route::delete('roles/destroy', 'RolesController@massDestroy')->name('roles.massDestroy');
    Route::resource('roles', 'RolesController');
    Route::delete('users/destroy', 'UsersController@massDestroy')->name('users.massDestroy');

    Route::post('user/show', 'UsersController@show');
    Route::get('user/evance', 'UsersController@evance');

    Route::resource('users', 'UsersController');
    Route::get('users/approve/{id}', 'UsersController@approveUser')->name('users.approve');
    Route::put('users/approve/{id}', 'UsersController@approveUserAct')->name('users.approveAct');
    Route::put('users/delete/{id}', 'UsersController@deleteUser')->name('users.deleteUser');
    Route::get('users/deleteApproval/{id}', 'UsersController@deleteUserApproval')->name('users.deleteApproval');
    Route::put('users/deleteApproval/{id}', 'UsersController@deleteUserActApproval')->name('users.deleteActApproval');
    Route::post('user/reset', 'UsersController@reset')->name('users.reset');

    Route::put('users/new/update', 'UsersController@update');
    Route::delete('products/destroy', 'ProductsController@massDestroy')->name('products.massDestroy');
    Route::resource('products', 'ProductsController', ['except' => ['show']]);
    Route::get('products/{product_id}', 'ProductsController@customShow')->name('show');

    //password policy routes
    Route::get('password_policy', 'Settings\PasswordPolicyController@index');
    Route::post('password_policy/store', 'Settings\PasswordPolicyController@storePasswordPolicy');


});

Route::group(['prefix' => 'agency', 'as' => 'agency.', 'namespace' => 'agency', 'middleware' => ['auth', 'verified']], function () {
    Route::get('users', 'AgentController@index')->name('agents');
    Route::get('user/create', 'AgentController@create');
    Route::get('user/edit/{id}', 'AgentController@editAgent');
    Route::put('user/update/{id}', 'AgentController@updateAgent');
    Route::post('user/store', 'AgentController@store');
    Route::post('user/status', 'AgentController@statusAgent');

    Route::get('users/approve/{id}', 'AgentController@approveAgent')->name('agent.approve');
    Route::put('users/approve/{id}/', 'AgentController@approveAgentAct')->name('agent.approveAct');
    //added by Evance Nganyaga
    Route::post('user/resetpin', 'AgentController@agentResetPIN');
    Route::post('user/verifyAccount', 'AgentController@verifyAccount')->name('verify.account');


    Route::get('operator/create/{id}', 'AgentController@operatorCreateView');
    Route::get('operators/{id}', 'AgentController@getOperators')->name('operators');
    Route::post('operator/store/{id}', 'AgentController@storeOperator');
    //added by Evance Nganyaga
    Route::post('operator/resetpin', 'AgentController@resetOperatorPIN');
    Route::post('operator/status', 'AgentController@statusOperator');
    Route::get('operator/edit/{id}', 'AgentController@editOperator');
    Route::post('operator/update', 'AgentController@updateOperator');

    Route::get('operator/approve/{id}', 'AgentController@approveOperator')->name('operator.approve');
    Route::put('operator/approve/{id}/', 'AgentController@approveOperatorAct')->name('operator.approveAct');

    Route::put('operator/delete/{id}', 'AgentController@deleteOperator')->name('operator.deleteOperator');
    Route::get('operator/deleteApproval/{id}', 'AgentController@deleteOperatorApproval')->name('operator.deleteApproval');
    Route::put('operator/deleteApproval/{id}', 'AgentController@deleteOperatorActApproval')->name('operator.deleteActApproval');

    Route::get('device/create/{id}', 'AgentController@deviceCreateView');
    Route::get('devices/{id}', 'AgentController@getDevices')->name('devices');
    Route::post('device/store/{id}', 'AgentController@storeDevice');

    Route::get('device/approve/{id}', 'AgentController@approveDevice')->name('device.approve');
    Route::put('device/approve/{id}/', 'AgentController@approveDeviceAct')->name('device.approveAct');
    //added by Evance Nganyaga
    Route::post('device/status', 'AgentController@updateDeviceStatus');

    Route::get('commissions', 'AgentController@getCommissions');
    Route::get('transactions', 'AgentController@getTransactions');
    Route::get('transaction/{txn_id}', 'AgentController@viewTransaction');
    Route::post('transaction/reverse', 'AgentController@reverseTransaction')->name('transaction.reverse');


    Route::get('account/service', 'AgentController@getAccountServices');

    Route::get('institutionaccounts', 'AgentController@getInstitutionAccounts');
    Route::get('institutionaccounts/edit/{id}', 'AgentController@editInstitutionAccount');
    Route::post('institutionaccounts/update', 'AgentController@updateInstitutionAccount');
    Route::post('institutionaccounts/store', 'AgentController@storeInstitutionAccount');
    Route::post('institutionaccounts/approve', 'AgentController@approveInstitutionAccount');

    Route::get('account/create', 'AgentController@createAccountService');
    Route::post('account/store', 'AgentController@storeAccountService');
    Route::get('commission/create', 'AgentController@createCommission');
    Route::post('commission/store', 'AgentController@storeCommission');
    Route::get('bank/change_status/{id}', 'SettingsController@changeBankStatus');
    Route::get('biller/change_status/{id}', 'SettingsController@changeBillerStatus');
    Route::get('commission/approve/{id}', 'SettingsController@approveCommission')->name('commission.approve');
    Route::put('commission/approve/{id}/', 'SettingsController@approveCommissionAct')->name('commission.approveAct');
    Route::get('agentcommissions', 'AgentCommisionController@index');
    Route::post('agentcommissions/generate', 'AgentCommisionController@generateCommssionBatches');
    Route::post('agentcommissions/batch', 'AgentCommisionController@commisionBatchOperations');
    Route::get('agentcommissions/batch/{id}', 'AgentCommisionController@indexCommssionBatch');


    //added by Evance Nganyagaga
    Route::post('account/approve', 'AgentController@approveAccountService');

    //added Evance Nganyaga
    Route::get('account/service/edit/{id}', 'AgentController@editAccountServices');
    Route::post('account/update', 'AgentController@updateAccountService');

    //new route added by James
    Route::get('commission/edit/{id}', 'AgentController@editCommission');
    Route::put('commission/update', 'AgentController@updateCommission');

    //end new route

    Route::put('account/edit/{id}', 'AgentController@editAccountService');
    Route::get('account/edit/{id}/{ac_no}', 'AgentController@editAccountView');

    //added by Evance Nganyaga
    Route::get('accounts/{id}', 'AgentController@accountCreateView')->name('accounts');
    Route::post('accounts/store', 'AgentController@storeAgentAccount');
    Route::post('accounts/approve', 'AgentController@approveAgentAccount');
    Route::get('accounts/edit/{id}', 'AgentController@editAgentAccount');
    Route::put('accounts/update', 'AgentController@updateAgentAccount');

    Route::post('servicecharges/import', 'ChargeController@import')->name('servicecharges.import');
    Route::resources([
        'charges' => 'ChargeController',
        'users' => 'UserController',
    ]);


    Route::get('servicecharges/{id}', 'ChargeController@indexServiceChargesByBatch');
    Route::post('servicecharges/batch', 'ChargeController@batchOperations');
    Route::post('servicecharges/delete', 'ChargeController@deleteServiceCharge');
    Route::post('servicecharges/batch/download', 'ChargeController@batchDownload');

    //added by Evance Nganyaga
    Route::post('batch/store', 'BatchController@storeBatch');
    Route::get('batch/delete/{$id}', 'BatchController@deleteBatch');


    Route::put('commission/edit/{id}', 'AgentController@editCommission');
    Route::get('commission/edit/{id}/{service_id}/{name}/{value}', 'AgentController@editCommissionView');

    //GEPG Institution
    Route::get('view_gepg_institution', 'SettingsController@createGEPGInstitution')->name('view_gepg_institution');
    Route::post('view_gepg_institution', 'SettingsController@storeGEPGInstitution');
    Route::get('view_gepg_institution/edit/{id}', 'SettingsController@editGEPGInstitution')->name('edit_gepg_institution');
    Route::put('update_gepg_institution', 'SettingsController@updateGEPGInstitution');

    Route::get('view_gepg_institution/approve/{id}', 'SettingsController@approveGEPGInstitution')->name('view_gepg_institution.approve');
    Route::put('view_gepg_institution/approve/{id}/', 'SettingsController@approveGEPGInstitutionAct')->name('view_gepg_institution.approveAct');

    //SADAKA DIGITAL
    Route::get('view_sadaka_digital', 'SettingsController@createSadakaDigital')->name('view_sadaka_digital');
    Route::post('view_sadaka_digital', 'SettingsController@storeSadakaDigital');
    Route::get('view_sadaka_digital/edit/{id}', 'SettingsController@editSadakaDigital')->name('edit_sadaka_digital');
    Route::put('update_sadaka_digital', 'SettingsController@updateSadakaDigital');

    Route::get('view_sadaka_digital/approve/{id}', 'SettingsController@approveSadakaDigital')->name('view_sadaka_digital.approve');
    Route::put('view_sadaka_digital/approve/{id}/', 'SettingsController@approveSadakaDigitalAct')->name('view_sadaka_digital.approveAct');

    //Branches
    Route::get('view_branch', 'SettingsController@createBranch')->name('view_branch');
    Route::post('view_branch', 'SettingsController@storeBranch')->name('view_branch');
    Route::get('view_branch/approve/{id}', 'SettingsController@approveBranch')->name('branch.approve');
    Route::put('view_branch/approve/{id}/', 'SettingsController@approveBranchAct')->name('branch.approveAct');

    Route::get('view_branch/view/{id}/', 'SettingsController@viewBranch')->name('branch.view');
    Route::get('view_branch/disable/{id}', 'SettingsController@disableBranch')->name('branch.disable');
    Route::put('viw_branch/disable/{id}', 'SettingsController@disableBranchAct')->name('branch.disableAct');
    Route::get('viw_branch/disableApproval/{id}', 'SettingsController@disableBranchApproval')->name('branch.disableApproval');
    Route::put('viw_branch/disableApproval/{id}', 'SettingsController@disableBranchActApproval')->name('branch.disableActApproval');
    Route::get('view_branch/edit/{id}', 'SettingsController@editBranch')->name('edit_branch');
    Route::put('update_branch', 'SettingsController@updateBranch')->name('update_branch');

    Route::get('view_branch/enable/{id}', 'SettingsController@enableBranch')->name('branch.enable');
    Route::put('viw_branch/enable/{id}', 'SettingsController@enableBranchAct')->name('branch.enableAct');
    Route::get('viw_branch/enableApproval/{id}', 'SettingsController@enableBranchApproval')->name('branch.enableApproval');
    Route::put('viw_branch/enableApproval/{id}', 'SettingsController@enableBranchActApproval')->name('branch.enableActApproval');

    Route::get('view_account_product', 'SettingsController@createAccountProduct')->name('view_account_product');
    Route::post('view_account_product', 'SettingsController@storeAccountProduct');

    Route::put('requests/actions', 'AbActionRequestController@abRequestHandler');


    //Add Banks, Billers and Biller Group
    Route::get('view_bank', 'SettingsController@createBank')->name('view_bank');
    Route::post('view_bank', 'SettingsController@storeBank');
    Route::post('upload_bank_file', 'SettingsController@storeBankBatch');
    Route::get('download_template', 'SettingsController@downloadtemplate');


    Route::put('view_bank/delete/{id}', 'SettingsController@deleteBank')->name('view_bank.deleteBank');
    Route::get('view_bank/deleteApproval/{id}', 'SettingsController@deleteBankApproval')->name('view_bank.deleteApproval');
    Route::put('view_bank/deleteApproval/{id}', 'SettingsController@deleteBankActApproval')->name('view_bank.deleteActApproval');

    Route::post('view_bank/approve', 'SettingsController@approveBank');


    Route::get('view_bank/edit/{id}', 'SettingsController@editBank')->name('edit_bank');
    Route::put('update_bank', 'SettingsController@updateBank');

    Route::get('view_bank/approve/{id}', 'SettingsController@approveBank')->name('bank.approve');
    Route::put('view_bank/approve/{id}/', 'SettingsController@approveBankAct')->name('bank.approveAct');

    Route::get('view_biller', 'SettingsController@createBiller')->name('view_biller');
    Route::post('view_biller', 'SettingsController@storeBiller')->name('store_biller');
    Route::get('view_biller/approve/{id}', 'SettingsController@approveBiller')->name('view_biller.approve');
    Route::get('view_biller/approve/{id}', 'SettingsController@approveBiller')->name('view_biller.approve');
    Route::put('view_biller/approve/{id}/', 'SettingsController@approveBillerAct')->name('view_biller.approveAct');

    Route::post('edit_biller', 'SettingsController@editBiller')->name('edit_biller');
    Route::put('update_biller', 'SettingsController@updateBiller')->name('update_biller');

    Route::get('view_biller_group', 'SettingsController@createBillerGroup')->name('view_biller_group');
    Route::post('view_biller_group', 'SettingsController@storeBillerGroup')->name('store_biller_group');
    //added by Evance Nganyaga
    Route::get('view_biller_group/approve/{id}', 'SettingsController@approveBillerGroup')->name('view_biller_group.approve');
    Route::put('view_biller_group/approve/{id}', 'SettingsController@approveBillerGroupAct')->name('view_biller_group.approveAct');

    Route::get('view_biller_group/edit/{id}', 'SettingsController@editBillerGroup')->name('edit_biller_group');
    Route::put('update_biller_group', 'SettingsController@updateBillerGroup')->name('update_biller_group');

    //Added by Evance Nganyaga
    Route::get('securitypolicies', 'SettingsController@indexSecurityPolicies')->name('sPolicy.index');
    Route::post('securitypolicies/otp', 'SettingsController@updateOTPSecurityPolicies');
    Route::post('securitypolicies/pin', 'SettingsController@updatePINSecurityPolicies');
    //new
    Route::get('pinpolicies/approve/{id}', 'SettingsController@approvePinPolicy')->name('pPolicy.approve');
    Route::put('pinpolicies/approve/{id}/', 'SettingsController@approvePinPolicyAct')->name('pPolicy.approveAct');
    Route::get('pinpolicies/view/{id}/', 'SettingsController@viewPinPolicy')->name('pPolicy.view');
    Route::get('otppolicies/approve/{id}', 'SettingsController@approveOtpPolicy')->name('oPolicy.approve');
    Route::put('otppolicies/approve/{id}/', 'SettingsController@approveOtpPolicyAct')->name('oPolicy.approveAct');
    Route::get('otppolicies/view/{id}/', 'SettingsController@viewOtpPolicy')->name('oPolicy.view');

    //Added by Evance Nganyaga
    Route::get('reports', 'ReportsController@index');
    Route::post('reports/export', 'ReportsController@export');
    Route::post('reports/commission/export', 'ReportsController@exportCommissionDistribution');
    Route::post('reports/commission/approve', 'ReportsController@approveCommissionDistribution');


});


Auth::routes();
Route::get('reload-captcha', 'Auth\LoginController@reloadCaptcha');
Route::get('/home', 'HomeController@index')->middleware('auth', 'verified')->name('home');
Route::get('/admin/audit_trail', 'AuditController@index')->middleware('auth', 'verified');


//async dashboard
Route::group(['prefix' => 'dashboard/', 'middleware' => ['auth', 'verified']], function () {
    Route::get('all_transactions', [\App\Http\Controllers\HomeController::class, 'getTransactions'])->name('all.transactions');
    Route::get('user_count', [\App\Http\Controllers\HomeController::class, 'getUserCount'])->name('all.users');
});

