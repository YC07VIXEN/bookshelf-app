<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\GenreController;

/*
|--------------------------------------------------------------------------
| 1. パブリックルート（未ログイン・ログイン共通）
|--------------------------------------------------------------------------
| 💡 ナビゲーションバー（navigation.blade.php）に直書きされているルート名は、
|    未ログイン状態のテスト（ExampleTest）でも呼び出されるため、ミドルウェアの外側に配置します。
*/

// ランキング画面（トップページ）
Route::get('/', [RankingController::class, 'index'])->name('ranking.index');
Route::get('/ranking', [RankingController::class, 'index']);

// ジャンル一覧画面およびジャンル別書籍一覧画面
Route::get('/genres', function () {
    return view('genres.index');
})->name('genres.index');

Route::get('/genres/{id}', [GenreController::class, 'show'])->name('genres.show');

// お気に入り一覧画面
Route::get('/favorites', function () {
    return view('favorites.index');
})->name('favorites.index');

// 書籍詳細画面
Route::get('/books/{book}', function (\App\Models\Book $book) {
    $book->load(['genre', 'user', 'reviews.user']);
    return view('books.show', compact('book'));
})->name('books.show');


/*
|--------------------------------------------------------------------------
| 2. ゲスト専用ルート（未ログイン時のみアクセス可能）
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::view('/register', 'auth.register')->name('register');
    Route::view('/login', 'auth.login')->name('login');
});


/*
|--------------------------------------------------------------------------
| 3. 認証必須ルート（ログインユーザーのみアクセス可能）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // --- 書籍CRUD関連 ---
    Route::prefix('books')->name('books.')->group(function () {
        // 書籍登録画面の表示と処理
        Route::view('/create', 'books.create')->name('create');
        Route::post('/', [BookController::class, 'store'])->name('store');
        
        // 書籍編集画面の表示と処理
        Route::get('/{book}/edit', function (\App\Models\Book $book) {
            return view('books.edit', compact('book'));
        })->name('edit');
        Route::put('/{book}', [BookController::class, 'update'])->name('update');
        
        // 書籍削除処理
        Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy');
    });

    // --- レビュー投稿関連 ---
    Route::post('/books/{bookId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // --- インタラクション関連（お気に入り・いいねのトグル処理） ---
    Route::post('/books/{book}/favorite', [InteractionController::class, 'toggleFavorite'])->name('favorites.toggle');
    Route::post('/reviews/{review}/like', [InteractionController::class, 'toggleLike'])->name('likes.toggle');
});
