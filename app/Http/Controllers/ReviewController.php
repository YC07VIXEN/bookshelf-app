<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    /**
     * レビューの投稿処理
     */
    public function store(Request $request, $bookId)
    {
        // 1. PM決定事項に合わせたバリデーション（1〜5の5段階評価）
        $request->validate([
            'stars'   => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string'],
        ]);

        // 対象の書籍データを取得
        $book = Book::findOrFail($bookId);

        // 2. 【PM制限指示】自分が登録した書籍にはレビューを投稿できない
        if ($book->user_id === Auth::id()) {
            return redirect()->back()->withErrors(['review_error' => '自分が登録した書籍にはレビューできません。']);
        }

        // 3. レビューを保存（PM指示により、同一本への複数回投稿も可能）
        Review::create([
            'user_id' => Auth::id(),
            'book_id' => $book->id,
            'stars'   => $request->stars,
            'comment' => $request->comment,
        ]);

        // 当該書籍詳細画面へリダイレクトし、フラッシュメッセージを表示
        return redirect()->back()->with('success', 'レビューを投稿しました。');
    }
}
