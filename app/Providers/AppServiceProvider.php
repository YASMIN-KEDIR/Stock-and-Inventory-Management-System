<?php

namespace App\Providers;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Efficient memoized view composer for header notifications
        View::composer('components.layouts.app', function ($view) {
            static $cachedLowStockCount = null;

            if ($cachedLowStockCount === null && Schema::hasTable('products')) {
                $cachedLowStockCount = Product::where('is_active', true)
                    ->where('current_stock', '<=', DB::raw('minimum_stock_level'))
                    ->count();
            }

            $view->with('lowStockCount', $cachedLowStockCount ?? 0);
        });
    }
}
