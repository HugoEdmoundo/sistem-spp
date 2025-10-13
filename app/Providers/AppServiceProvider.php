<?php
// app/Providers/AppServiceProvider.php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use App\Models\Pembayaran;

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
        // Share data pembayaran pending count ke semua view untuk admin
        View::composer('*', function ($view) {
            if (auth()->check() && auth()->user()->isAdmin()) {
                $pembayaranPendingCount = Pembayaran::where('status', 'pending')->count();
                $view->with('pembayaranPendingCount', $pembayaranPendingCount);
            }
        });
    }
}