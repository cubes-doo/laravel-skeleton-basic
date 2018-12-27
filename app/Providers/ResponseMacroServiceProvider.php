<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Response;
use Illuminate\Http\RedirectResponse;

class ResponseMacroServiceProvider extends ServiceProvider
{
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        $systemMessage = function ($text, $type = 'success') {
            session()->flash('system-message', ['type' => $type, 'text' => $text]);
            return $this;
        };
        $systemError = function ($text) {
            session()->flash('system-message', ['type' => 'error', 'text' => $text]);
            return $this;
        };
        $systemWarning = function ($text) {
            session()->flash('system-message', ['type' => 'warning', 'text' => $text]);
            return $this;
        };
        $systemInfo = function ($text) {
            session()->flash('system-message', ['type' => 'info', 'text' => $text]);
            return $this;
        };
        $systemSuccess = function ($text) {
            session()->flash('system-message', ['type' => 'success', 'text' => $text]);
            return $this;
        };
        Response::macro('withSystemMessage', $systemMessage);
        RedirectResponse::macro('withSystemMessage', $systemMessage);
        Response::macro('withSystemError', $systemError);
        RedirectResponse::macro('withSystemError', $systemError);
        Response::macro('withSystemWarning', $systemWarning);
        RedirectResponse::macro('withSystemWarning', $systemWarning);
        Response::macro('withSystemInfo', $systemInfo);
        RedirectResponse::macro('withSystemInfo', $systemInfo);
        Response::macro('withSystemSuccess', $systemSuccess);
        RedirectResponse::macro('withSystemSuccess', $systemSuccess);
    }
}