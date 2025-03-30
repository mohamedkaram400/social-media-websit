<?php

use App\Http\Controllers\GroupController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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

// Home Page
Route::get('/', [HomeController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// Profile Page
Route::get('/u/{user:username}', [ProfileController::class, 'index'])
    ->name('profile');

// Group Page
Route::controller(GroupController::class)->group(function () {
    Route::get('/g/{group:slug}', 'profile')
        ->name('group.profile');

    Route::get('/group/approve-invitation/{token}', 'approveInvitation')
        ->name('group.approveInvitation');
});


// Auth Routes
Route::middleware('auth')->group(function () {
    // Group
    Route::controller(GroupController::class)->group(function () {
        Route::post('/group/update-images/{group:slug}', 'updateImage')
            ->name('group.updateImages');
        
        Route::post('/group/invite/{group:slug}', 'inviteUsers')
            ->name('group.inviteUsers');
        
        Route::post('/group/join/{group:slug}', 'join')
            ->name('group.join');
    });


    // Profile
    Route::controller(ProfileController::class)->group(function () {
        //    Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')
            ->name('profile.update');

        Route::delete('/profile', 'destroy')
            ->name('profile.destroy');

        Route::post('/profile/update-images', 'updateImage')
            ->name('profile.updateImages');
    });


    // Posts
    Route::controller(PostController::class)->group(function () {
        Route::post('/post', 'store')
            ->name('post.create');

        Route::put('/post/{post}', 'update')
            ->name('post.update');

        Route::delete('/post/{post}', 'destroy')
            ->name('post.destroy');

        Route::get('/post/download/{attachment}', 'downloadAttachment')
            ->name('post.download');

        Route::post('/post/{post}/reaction', 'postReaction')
            ->name('post.reaction');

        Route::post('/post/{post}/comment', 'createComment')
            ->name('post.comment.create');

        // Comments
        Route::delete('/comment/{comment}', 'deleteComment')
            ->name('comment.delete');

        Route::put('/comment/{comment}', 'updateComment')
            ->name('comment.update');

        Route::post('/comment/{comment}/reaction', 'commentReaction')
            ->name('comment.reaction');
    });


    // Groups
    Route::controller(GroupController::class)->group(function () {
        Route::post('/group', 'store')
            ->name('group.create');

        Route::post('/group/approve-request/{group:slug}', 'approveRequest')
            ->name('group.approveRequest');
    });
});

require __DIR__.'/auth.php';

