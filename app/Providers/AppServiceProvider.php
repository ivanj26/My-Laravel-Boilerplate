<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // @money Blade directive
        Blade::directive('money', function ($amount, $flag = 'IDR') {
            return "<?php echo '{$flag}' . number_format($amount, 2); ?>";
        });
    }
}
