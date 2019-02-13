<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\{Request, Response, RedirectResponse};

class SystemMessagesServiceProvider extends ServiceProvider
{
    const KEY = 'system-message';
    /**
     * Register the application's response macros.
     *
     * @return void
     */
    public function boot()
    {
        $systemMessage = function ($text, $type = 'success') {
            session()->flash(self::KEY, ['type' => $type, 'text' => $text]);
            return $this;
        };
        $systemError = function ($text) {
            session()->flash(self::KEY, ['type' => 'error', 'text' => $text]);
            return $this;
        };
        $systemWarning = function ($text) {
            session()->flash(self::KEY, ['type' => 'warning', 'text' => $text]);
            return $this;
        };
        $systemInfo = function ($text) {
            session()->flash(self::KEY, ['type' => 'info', 'text' => $text]);
            return $this;
        };
        $systemSuccess = function ($text) {
            session()->flash(self::KEY, ['type' => 'success', 'text' => $text]);
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
        
        Request::macro('getSystemMessage', function() {
            return session()->get(self::KEY);
        });
        Request::macro('getSystemMessageKey', function() {
            return self::KEY;
        });
    }
}