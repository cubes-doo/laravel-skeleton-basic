<?php

return [
    'model' => App\Models\JobStatus::class,
    'event_manager' => \Imtigger\LaravelJobStatus\EventManagers\DefaultEventManager::class,
    'database_connection' => null
];
