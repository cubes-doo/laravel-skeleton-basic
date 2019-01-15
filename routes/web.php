<?php

/**
 * URLs - always kebab-cased ( http://wiki.c2.com/?KebabCase ), always begining with a '/' 
 *      - order of words in a route goes as follows: 
 *          #1 name of entity in plural (ex. "entities")
 *          #2 (optional) {entity} (if action is related to one entity) this parameter is the primary key of entity model
 *          #3 action name
 * 
 *      - entity's ID is always represented as {entity}, because of policies applied in controller
 * Names - underscore case, always
 * 
 * Grouping - by controller - minimum. In each group, a url prefix & name prefix 
 * are a must, the namspace prefix is optional if the controller is in a 
 * sub-folder.
 * 
 * example:
 */
Route::name('entities.')->prefix('/entites')->group(function() {
    Route::get('/', 'EntitesController@all')->name('list');
    Route::get('/create', 'EntitesController@create')->name('create');
    Route::post('/create', 'EntitesController@store');
    Route::get('/{entity}/edit', 'EntitesController@edit')->name('edit');
    Route::post('/{entity}/edit', 'EntitesController@update');
    Route::post('/{entity}/delete', 'EntitesController@delete')->name('delete');
    Route::post('/{entity}/change-status', 'EntitesController@changeStatus')->name('change_status');
    Route::post('/{entity}/delete-photo', 'EntitesController@deletePhoto')->name('delete_photo');
});

/**
 * If a controller is in a sub-folder (ex. app/http/Controllers/Admin):
 * 
 * example:
 */
Route::name('section.')->prefix('/section')->namespace('Section')->group(function() {
    require __DIR__ . '/web/section.php';
});

/*
|
| Route::match(...) & Route::any(...) are discouraged, because they map more 
| than one HTTP method to one Controller method, and this would break the single 
| responsibility principle.
| @link https://www.sitepoint.com/the-single-responsibility-principle/
| @link https://blog.codinghorror.com/curlys-law-do-one-thing/
*/