<?php

/*
|--------------------------------------------------------------------------
| Web Routes - Standard
|--------------------------------------------------------------------------
|
| * URLs - always kebab-cased ( http://wiki.c2.com/?KebabCase ), always begining with a '/' 
| (ex. '/users', '/users/get/{id}', '/users/add-image')
| * Names - underscore case, always
| 
| * Grouping - by controller - minimum. In each group, a url prefix & name prefix 
| are a must, the namspace prefix is optional if the controller is in a 
| sub-folder.
| examples: 
| 
| Route::name('backend_users.')->prefix('/backend-users')->group(function() {
|     Route::get('/list-users', 'BackendUsersController@listUsers')->name('list_users'); 
| });
| 
| if controller is in a sub-folder (ex. app/http/Controllers/Admin):
| Route::name('admin.')->prefix('/admin')->namespace('Admin')->group(function() {
|     require __DIR__ . '/web/admin.php';
| });
|
| Route::match(...) & Route::any(...) are discouraged, because they map more 
| than one HTTP method to one Controller method, and this would break the single 
| responsibility principle.
| @link https://www.sitepoint.com/the-single-responsibility-principle/
| @link https://blog.codinghorror.com/curlys-law-do-one-thing/
|
*/

Route::get('/', function () {
    return redirect('/aaa')->withSystemMessage('aaa');
});
Route::get('/aaa', function () {
    dump(session('system-message'));
    return '<a href="/back">back</a>';
});
Route::get('/back', function () {
    return back()->withSystemWarning('warning');
});
