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
            $e = explode(
                ',',
                $expression
            );

            $pattern = $e[0] ?? '';
            $class   = $this->normalize($e[1] ?? 'active');

            return '<?php echo (request()->is(' . $pattern . ') ? \'' . $class . '\' : null); ?>';
        });
        
        \Blade::directive('errorClass', function ($expression) {
            $e = explode(
                ',',
                $expression
            );

            $pattern = $e[0] ?? '';
            $class   = $this->normalize($e[1] ?? 'is-invalid');
            
            return '<?php echo ($errors->has(' . $pattern . ') ? \'' . $class . '\' : null); ?>';
        });
        
        \Blade::directive('route', function ($expression) {
            $e = explode(
                ',',
                $expression
            );

            $routeName = $e[0] ?? '';
            $params    = $e[1] ?? [];

            $out = '';

            if(!empty($params)) {
                $out = "<?php echo (route($routeName, $params)); ?>";
            } else {
                $out = "<?php echo (route($routeName)); ?>";
            }

            return $out;
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
     *
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
