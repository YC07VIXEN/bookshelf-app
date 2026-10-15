<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\InteractionController;
use App\Http\Controllers\RankingController;
use App\Http\Controllers\GenreController;

/*
|--------------------------------------------------------------------------
| 1. ゲスト（未ログイン）でも閲覧できるパブリックルート
|--------------------------------------------------------------------------
*/

// ランキング画面（トップページまたはランキング専用画面）
Route::get('/', [RankingController::class, 'index'])->name('ranking.index');
Route::get('/ranking', [RankingController::class, 'index']);

// ジャンル別書籍一覧画面
Route::get('/genres/{id}', [GenreController::class, 'show'])->name('genres.show');

// 書籍詳細画面（レビュー一覧などもここに内包）
Route::get('/books/{book}', function (\App\Models\Book $book) {
    // 画面設計に合わせ、紐づくジャンル・ユーザー・レビューを一緒に読み込んで画面を表示
    $book->load(['genre', 'user', 'reviews.user']);
    return view('books.show', compact('book'));
})->name('books.show');


/*
|--------------------------------------------------------------------------
| 2. 認証（ログイン・会員登録）関連のルート
|--------------------------------------------------------------------------
| ※Laravel Breeze等のパッケージが route/auth.php を自動生成している場合は
|   既存のままでOKです。手動で繋ぎこむ場合の基本セットを以下に定義します。
*/
Route::middleware('guest')->group(function () {
    // 会員登録画面と処理
    Route::view('/register', 'auth.register')->name('register');
    // ※実際の登録処理（POST）はAuth系のコントローラーに紐づけます

    // ログイン画面と処理
    Route::view('/login', 'auth.login')->name('login');
});


/*
|--------------------------------------------------------------------------
| 3. ログイン認証必須（authミドルウェア）の保護ルート
|--------------------------------------------------------------------------
| 書籍のCRUD、レビュー投稿、いいね、お気に入りなど、ログインユーザーのみに許された操作
*/
Route::middleware('auth')->group(function () {

    // --- 書籍CRUD関連 ---
    Route::prefix('books')->name('books.')->group(function () {
        // 書籍登録画面の表示
        Route::view('/create', 'books.create')->name('create');
        // 書籍登録処理（POST）
        Route::post('/', [BookController::class, 'store'])->name('store');
        
        // 書籍編集画面の表示
        Route::get('/{book}/edit', function (\App\Models\Book $book) {
            return view('books.edit', compact('book'));
        })->name('edit');
        // 書籍更新処理（PUT/PATCH）
        Route::put('/{book}', [BookController::class, 'update'])->name('update');
        
        // 書籍削除処理（DELETE）
        Route::delete('/{book}', [BookController::class, 'destroy'])->name('destroy');
    });

    // --- レビュー投稿関連 ---
    // 書籍詳細画面などからPOSTされるレビューを処理
    Route::post('/books/{bookId}/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // --- インタラクション関連（お気に入り・いいね） ---
    // 書籍のお気に入り切り替え（トグル）
    Route::post('/books/{book}/favorite', [InteractionController::class, 'toggleFavorite'])->name('favorites.toggle');
    
    // レビューのいいね切り替え（トグル）
    Route::post('/reviews/{review}/like', [InteractionController::class, 'toggleLike'])->name('likes.toggle');
});
