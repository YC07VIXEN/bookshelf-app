<?php

namespace App\Http/Controllers;

use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookController extends Controller
{
    /**
     * 書籍の登録処理（ストア）
     */
    public function store(Request $request)
    {
        // PMとの決定事項に合わせたバリデーション
        $request->validate([
            'genre_id'    => ['required', 'exists:genres,id'],
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100'], // 概要説明は最大100文字
        ]);

        // ログイン中のユーザーIDを紐づけて書籍を保存
        Book::create([
            'user_id'     => Auth::id(),
            'genre_id'    => $request->genre_id,
            'title'       => $request->title,
            'author'      => $request->author,
            'description' => $request->description,
        ]);

        // 書籍詳細画面（または一覧）へリダイレクトし、フラッシュメッセージを表示
        return redirect()->back()->with('success', '書籍を登録しました。');
    }

    /**
     * 書籍の更新処理（アップデート）
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'genre_id'    => ['required', 'exists:genres,id'],
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        $book->update([
            'genre_id'    => $request->genre_id,
            'title'       => $request->title,
            'author'      => $request->author,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', '書籍情報を更新しました。');
    }

    /**
     * 書籍の削除処理（デストロイ）
     */
    public function destroy(Book $book)
    {
        // 物理削除を実行（紐づくレビュー等はDBのcascade制約で自動消去されます）
        $book->delete();

        return redirect()->back()->with('success', '書籍を削除しました。');
    }
}
