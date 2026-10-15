<?php

namespace App\Http\Controllers;

use App\Models\Book;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    /**
     * ランキング一覧画面の表示
     */
    public function index()
    {
        // PM指示：アクセス時にリアルタイムにクエリで集計（非キャッシュ）
        $rankedBooks = Book::with(['genre', 'user'])
            // 1. 各書籍に紐づくレビューの「平均評価（星の数）」を算出（カラム名：reviews_avg_stars）
            ->withAvg('reviews', 'stars')
            // 2. 各書籍に紐づく「レビュー総数」を算出（カラム名：reviews_count）
            ->withCount('reviews')
            
            // 【PMソート指示】
            // ① 平均評価点（星の平均）が高い順（降順）
            ->orderByRaw('reviews_avg_stars DESC')
            // ② 同点の場合はレビュー数が多い順（降順）
            ->orderBy('reviews_count', 'DESC')
            // ③ さらに同点の場合は書籍登録日が新しい順（降順）
            ->orderBy('created_at', 'DESC')
            
            // 上位20件など、ランキングに表示する件数を制限（必要に応じて調整してください）
            ->take(20)
            ->get();

        // ranking/index.blade.php テンプレートへデータを渡して表示
        return view('ranking.index', compact('rankedBooks'));
    }
}
