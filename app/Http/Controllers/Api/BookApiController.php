<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Book;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BookApiController extends Controller
{
    /**
     * 【READ】書籍一覧の取得 (GET /api/books)
     */
    public function index()
    {
        $books = Book::with(['genre', 'user'])->get();
        
        // PM指示：status: success, data: [...] の形式で返却
        return response()->json([
            'status' => 'success',
            'data'   => $books
        ], 200);
    }

    /**
     * 【CREATE】書籍の新規登録 (POST /api/books)
     */
    public function store(Request $request)
    {
        // APIではリダイレクトさせずJSONでエラーを返すため、Validatorを手動で制御します
        $validator = Validator::make($request->all(), [
            'genre_id'    => ['required', 'exists:genres,id'],
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100'], // PM指示：概要100文字
        ]);

        // バリデーションエラー時 (422) の構造統一
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        // ※認証なしAPIのため、仮で最初のユーザーID（またはシステム共通ID）を紐付けます
        $book = Book::create([
            'user_id'     => 1, 
            'genre_id'    => $request->genre_id,
            'title'       => $request->title,
            'author'      => $request->author,
            'description' => $request->description,
        ]);

        return response()->json([
            'status' => 'success',
            'data'   => $book
        ], 21); // 201 Created
    }

    /**
     * 【READ】書籍詳細の取得 (GET /api/books/{id})
     */
    public function show($id)
    {
        $book = Book::with(['genre', 'user', 'reviews'])->find($id);

        // データが見つからない時 (404) の構造統一
        if (!$book) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Book not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $book
        ], 200);
    }

    /**
     * 【UPDATE】書籍情報の更新 (PUT /api/books/{id})
     */
    public function update(Request $request, $id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Book not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'genre_id'    => ['required', 'exists:genres,id'],
            'title'       => ['required', 'string', 'max:255'],
            'author'      => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:100'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors()
            ], 422);
        }

        $book->update($request->all());

        return response()->json([
            'status' => 'success',
            'data'   => $book
        ], 200);
    }

    /**
     * 【DELETE】書籍の削除 (DELETE /api/books/{id})
     */
    public function destroy($id)
    {
        $book = Book::find($id);

        if (!$book) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Book not found'
            ], 404);
        }

        $book->delete(); // 物理削除

        return response()->json([
            'status'  => 'success',
            'message' => 'Book deleted successfully'
        ], 200);
    }
}
