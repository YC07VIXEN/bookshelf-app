<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\Request;

class GenreController extends Controller
{
    /**
     * ジャンル別書籍一覧画面の表示
     */
    public function show($id)
    {
        // 対象のジャンルを取得（存在しない場合は404）
        $genre = Genre::findOrFail($id);

        // そのジャンルに属する書籍を、登録日が新しい順で取得（1対多のリレーションを活用）
        $books = $genre->books()->with(['user'])->orderBy('created_at', 'DESC')->get();

        // genres/show.blade.php（または指定のBlade）へデータを渡す
        return view('genres.show', compact('genre', 'books'));
    }
}
