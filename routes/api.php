<?php
use App\Http\Controllers\Api\PostController;
use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
 Route::post('login',[PostController::class,'login'])->middleware('checkuser');
 Route::post('forgetpassword',[PostController::class,'forget'])->middleware('checkuser');
Route::get('verify-email/{verification_code}',[PostController::class,'verify_email']);
Route::post('signup',[PostController::class,'signup']);
Route::post('reset/{email}',[PostController::class,'reset']);
Route::post('profile',[PostController::class,'profile']);
Route::apiResource('posts',PostController::class);
//http://127.0.0.1:8000/api/posts
