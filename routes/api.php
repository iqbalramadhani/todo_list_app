<?php

use App\Http\Controllers\api\ChecklistController;
use App\Http\Controllers\api\ChecklistItemController;
use App\Http\Controllers\Api\DanaWakafController;
use App\Http\Controllers\Api\OrganisasiDanaWakafController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [ApiController::class, 'authenticate']);
Route::post('register', [ApiController::class, 'register']);

Route::group(['middleware' => ['jwt.verify']],function () {
        Route::post('get_user', [ApiController::class, 'get_user']);
        Route::post('logout', [ApiController::class, 'logout']);
        Route::get('user', [UserController::class, 'show']);
        Route::post('user/update-profile', [UserController::class, 'update'])->name('user.update-profile');
        Route::post('user/upload-photo-profile', [UserController::class, 'upload_photo_profile'])->name('user.update-photo-profile');
        Route::get('show-member-user-list', [UserController::class, 'showMemberUserList'])->name('user.show-member-list');
        
        Route::get('show-all-checklist', [ChecklistController::class, 'showAllChecklist'])->name('checklist-list');
        Route::post('add-checklist', [ChecklistController::class, 'addChecklist'])->name('add.checklist-list');
        Route::delete('remove-checklist', [ChecklistController::class, 'deleteChecklist'])->name('remove.checklist-list');
        
        Route::get('show-detail-checklist-item', [ChecklistItemController::class, 'showItemChecklist'])->name('checklist-item');
        Route::post('add-checklist-item', [ChecklistItemController::class, 'addChecklistItem'])->name('checklist-item.add');
        Route::delete('remove-checklist-item', [ChecklistItemController::class, 'deleteChecklistItem'])->name('checklist-item.remove');
        Route::put('update-checklist-item', [ChecklistItemController::class, 'updateChecklistItem'])->name('checklist-item.update');
    }
);

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });