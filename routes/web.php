<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Author\PostController as AuthorPostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blog Routes
|--------------------------------------------------------------------------
*/

// Homepage
Route::get('/', [PostController::class, 'index'])
    ->name('posts.index');

// Single Post
Route::get('/posts/{slug}', [PostController::class, 'show'])
    ->name('posts.show');

/*
|--------------------------------------------------------------------------
| Author Portal
|--------------------------------------------------------------------------
*/

Route::middleware('auth')
    ->prefix('author')
    ->name('author.')
    ->group(function () {
        Route::resource('posts', AuthorPostController::class)->except('show');
    });

/*
|--------------------------------------------------------------------------
| Comments
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::post('/posts/{post}/comments', [CommentController::class, 'store'])
        ->name('comments.store');

    Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])
        ->name('comments.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin Panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'dashboard'])
            ->name('dashboard');

        Route::resource('categories', AdminCategoryController::class);

        // Users are managed read-only apart from role and deletion; there is
        // deliberately no create/store route.
        Route::resource('users', AdminUserController::class)
            ->only(['index', 'edit', 'update', 'destroy']);
    });

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Profile Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
