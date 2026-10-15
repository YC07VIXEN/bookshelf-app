<?php

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Review;
use App\Models\Favorite;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InteractionController extends Controller
{
    /**
     * お気に入り登録／解除の切り替えロジック（トグル処理）
     */
    public function toggleFavorite(Book $book)
    {
        $userId = Auth::id();

        // 既にお気に入りしているか確認
        $favorite = Favorite::where('user_id', $userId)->where('book_id', $book->id)->first();

        if ($favorite) {
            // すでにお気に入りされていれば解除
            $favorite->delete();
            $message = 'お気に入りを解除しました。';
        } else {
            // お気に入りされていなければ新規登録
            Favorite::create([
                'user_id' => $userId,
                'book_id' => $book->id,
            ]);
            $message = 'お気に入りに登録しました。';
        }

        return redirect()->back()->with('success', $message);
    }

    /**
     * いいねの登録／解除の切り替えロジック（トグル処理）
     */
    public function toggleLike(Review $review)
    {
        $userId = Auth::id();

        // ⚠️【PM制限指示】自分のレビューには「いいね」を押せない制御
        if ($review->user_id === $userId) {
            return redirect()->back()->withErrors(['like_error' => '自分のレビューにいいねはできません。']);
        }

        // 既にいいねしているか確認
        $like = Like::where('user_id', $userId)->where('review_id', $review->id)->first();

        if ($like) {
            // すでにいいねされていれば解除
            $like->delete();
            $message = 'いいねを取り消しました。';
        } else {
            // いいねされていなければ新規登録
            Like::create([
                'user_id' => $userId,
                'review_id' => $review->id,
            ]);
            $message = 'レビューにいいねしました。';
        }

        return redirect()->back()->with('success', $message);
    }
}
