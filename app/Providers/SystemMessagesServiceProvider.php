<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Http\{RedirectResponse, Request, Response};

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
        $key = self::KEY;
        $systemMessage = function ($text, $type = 'success') use ($key) {
            session()->flash($key, ['type' => $type, 'text' => $text]);
            return $this;
        };
        $systemError = function ($text) use ($key) {
            session()->flash($key, ['type' => 'error', 'text' => $text]);
            return $this;
        };
        $systemWarning = function ($text) use ($key) {
            session()->flash($key, ['type' => 'warning', 'text' => $text]);
            return $this;
        };
        $systemInfo = function ($text) use ($key) {
            session()->flash($key, ['type' => 'info', 'text' => $text]);
            return $this;
        };
        $systemSuccess = function ($text) use ($key) {
            session()->flash($key, ['type' => 'success', 'text' => $text]);
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
        
        Request::macro('getSystemMessage', function () use ($key) {
            return session()->get($key);
        });
        Request::macro('getSystemMessageText', function () use ($key) {
            $systemMessage = session()->get($key);
            if (is_array($systemMessage) && isset($systemMessage['text'])) {
                return $systemMessage['text'];
            }
        });
        Request::macro('getSystemMessageKey', function () use ($key) {
            return $key;
        });
    }
}
