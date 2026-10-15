<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * 一括画面割り当て（マスアサインメント）を許可する属性
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * シリアライズ時に非表示にする属性
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * ユーザーが登録した書籍（1対多）
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    /**
     * ユーザーが投稿したレビュー（1対多）
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
