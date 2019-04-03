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
Auth::routes();

Route::get('/', function() {
    return redirect()->route('login');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function() {
        return view('dashboard');
    })->name('dashboard');

    Route::name('entities.')->prefix('/entities/')->group(function() {
        Route::get('', 'EntitesController@all')->name('list');
        Route::post('datatable', 'EntitesController@datatable')->name('datatable');
        Route::get('create', 'EntitesController@create')->name('create');
        Route::post('create', 'EntitesController@store');
        Route::get('{entity}/edit', 'EntitesController@edit')->name('edit');
        Route::post('{entity}/edit', 'EntitesController@update');
        Route::post('{entity}/delete', 'EntitesController@delete')->name('delete');
        Route::post('{entity}/activate-deactivate', 'EntitesController@changeActive')->name('change_active');
        Route::post('{entity}/delete-photo', 'EntitesController@deletePhoto')->name('delete_photo');
    
        Route::name('child_entities.')->prefix('{masterEntity}/child-entities/')->group(function() {
            Route::get('create', 'ChildEntitesController@create')->name('create');
            Route::post('create', 'ChildEntitesController@store');
            Route::get('{entity}/edit', 'ChildEntitesController@edit')->name('edit');
            Route::post('{entity}/edit', 'ChildEntitesController@update');
            Route::post('{entity}/delete', 'ChildEntitesController@delete')->name('delete');
            Route::post('{entity}/change-status', 'ChildEntitesController@changeStatus')->name('change_status');
            Route::post('{entity}/delete-photo', 'ChildEntitesController@deletePhoto')->name('delete_photo');
        });
    });

    Route::name('users.')->prefix('/users/')->group(function() {
        $c = 'UsersController@';
        Route::get('',                       $c . 'all')->name('list');
        Route::post('datatable',             $c . 'datatable')->name('datatable');
        Route::get('create',                 $c . 'create')->name('create');
        Route::post('create',                $c . 'store');
        Route::get('{entity}/edit',          $c . 'edit')->name('edit');
        Route::post('{entity}/edit',         $c . 'update');
        Route::post('{entity}/delete',       $c . 'delete')->name('delete');
        Route::post('{entity}/delete-photo', $c . 'deletePhoto')->name('delete_photo');
    });

    /**
     * If a controller is in a sub-folder (ex. app/http/Controllers/Admin):
     * 
     * When would you use this?
     * Usually for entities that have a lot of child entities, 
     * and all of their "children" come with their own convoluted business logic.
     * In that case, since we want to emphasize their connection and the fact 
     * that they represent a portion all on their own, 
     * we would have a structure similar to this one:
     * 
     * App
     *      Models
     *              Section
     *                      Section.php
     *                      SubSection.php
     *                      SubSubSection.php
     *              SomeEntity.php
     *              SomeOtherEntity.php
     *              ...
     *      Http
     *              Controllers
     *                      Section
     *                          SectionsController.php
     *                          SubSectionsController.php // if needed
     *                          SubSubSectionsController.php // if needed
     *                      SomeEntitiesController.php
     *                      SomeOtherEntitiesController.php
     * 
     * example:
     */
    Route::name('section.')->prefix('/section')->namespace('Section')->group(function() {
        require __DIR__ . '/web/section.php';
    });
});

/*
|
| Route::match(...) & Route::any(...) are discouraged, because they map more 
| than one HTTP method to one Controller method, and this would break the 
| single responsibility principle.
| @link https://www.sitepoint.com/the-single-responsibility-principle/
| @link https://blog.codinghorror.com/curlys-law-do-one-thing/
*/
