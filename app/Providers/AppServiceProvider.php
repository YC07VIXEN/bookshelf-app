<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // パスワードの標準ルールを「最低8文字以上」に設定
        Password::defaults(function () {
            return Password::min(8);
        });
    }
}
