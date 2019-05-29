<?php

/**
 * Class
 *
 * PHP version 7.2
 */
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Example Job for describing standards
 *
 * Used for resource-heavy, recurring and/or time-consuming tasks.
 *
 *  - __construct():
 *  - handle():
 *  - protected methods:
 *  - properties:
 */
class ExampleJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable,
        SerializesModels;
    
    protected $example;
    
    public function __construct($example)
    {
        $this->setExample($example);
    }
    
    public function handle()
    {
    }
    
    public function getExample()
    {
        return $this->example;
    }

    public function setExample($example)
    {
        $this->example = $example;
    }
}
