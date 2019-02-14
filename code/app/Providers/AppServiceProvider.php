<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use Illuminate\Support\Facades\Blade;

use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        Blade::directive('active', function ($expression) {
            list($pattern, $class) = 
                explode(
                    ',', 
                    $this->normalize($expression)
                )
            ;
            return "<?= request()->is('$pattern') ? '$class' : ''; ?>";
        }); 
        
        Blade::directive('errors', function ($expression) {
            list($pattern, $class) = 
                explode(
                    ',', 
                    $this->normalize($expression)
                )
            ;
            
            return '<?= $errors->has("' . $pattern . '") ? \'' . $class . '\' : ""; ?>';
        }); 
        
        Blade::directive('route', function ($expression) {
            $routeName = $this->normalize($expression);
            return "<?= route('$routeName'); ?>";
        }); 
    }
    
    /**
     * Removes quotes & other characters passed to blade directives
     * 
     * When calling directives, the expressions given as parameters sometimes 
     * are quoted, even though this is not necessary. 
     * This method removes quotes, along with parentheses and 
     * blank space characters, there by approximating the actual value to be used.
     * 
     * @param string $expression
     * @return string
     */
    protected function normalize(string $expression): string
    {
        return str_replace(['(',')', ' ', "'"], '', $expression);
    }
    
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
