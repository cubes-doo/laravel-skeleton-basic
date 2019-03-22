<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    //List desired view composers to load
    protected $composers = [
        //'layout.partials.head' => App\ViewComposers\LayoutPartialsHead::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function boot()
    {
        // register view composers from $composers array
        $this->registerViewComposers();

        // share variables on all view scripts
        \View::share('loggedInUser', auth()->user());

        // add some more composers if needed
        //\View::composer('*', function(\Illuminate\View\View $view) {});

        // add custom blade directives
        \Blade::directive('activeClass', function ($expression) {
            list($pattern, $class) = 
                explode(
                    ',', 
                    $this->normalize($expression)
                )
            ;
            return "<?= request()->is('$pattern') ? '$class' : ''; ?>";
        }); 
        
        \Blade::directive('errorClass', function ($expression) {
            list($pattern, $class) = 
                explode(
                    ',', 
                    $this->normalize($expression)
                )
            ;
            
            return '<?= $errors->has("' . $pattern . '") ? \'' . $class . '\' : ""; ?>';
        }); 
        
        \Blade::directive('route', function ($expression) {
            $routeName = $this->normalize($expression);
            return "<?= route('$routeName'); ?>";
        });

        \Blade::component('_layout.partials.form.error', 'formError');
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

    protected function registerViewComposers()
    {
        foreach ($this->composers as $viewScript => $viewComposer) {
            \View::composer($viewScript, $viewComposer);
        }
    }
}
