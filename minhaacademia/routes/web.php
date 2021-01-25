<?php

use Illuminate\Support\Facades\Route;
use App\Setting;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/ajude', 'DonationController@index')->name('donation');

Route::middleware('menugenerator')->group(function () {
    Route::get('/', 'CourseController@index')->name('home');
    Route::get('/contato', 'PageController@contact')->name('contact');
    Route::post('/contato/enviar', 'PageController@contactSend')->name('contactSend');
    Route::get('/sobre', 'PageController@about')->name('about');
    Route::get('login', 'Auth\LoginController@showLoginGoogle')->name('login');
    Route::get('login/sobre', 'PageController@aboutLogin')->name('aboutLogin');
    Route::get('login/google', 'Auth\LoginController@providerRedirect')->name('login.google.url');
    Route::get('login/google/callback', 'Auth\LoginController@handleProviderCallback')->name('login.google.callback');

    Route::get('login-panel', 'Auth\LoginController@showLoginForm')->name('login-panel');
    Route::post('login-panel', 'Auth\LoginController@login')->name('login-panel.post');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/cursos', 'CourseController@index')->name('courses');

    Route::get('/certificados/verificar', 'CertifyController@verify')->name('certifyVerify');

    Route::get('/termos', 'PageController@terms')->name('terms');
    Route::get('/privacidade', 'PageController@privacy')->name('privacy');

});

Route::middleware('menugenerator', 'mathjax')->group(function () {
    Route::get('/formatar-texto', 'PageController@format')->name('format');
});

Route::middleware('auth')->group(function () {
    Route::post('/imagens', 'ImageController@store')->name('imageStore');
    Route::delete('/imagens/{image}', 'ImageController@destroy')->name('imageDestroy');
    Route::post('/usuarios', 'UserController@store')->name('imageStore');

    Route::put('/usuarios/{user}/senha', 'UserController@updatePassword')->name('userUpdatePassword');
    Route::put('/usuarios/{user}', 'UserController@update')->name('userUpdate');
    Route::delete('/usuarios/{user}', 'UserController@destroy')->name('userDestroy');

    Route::delete('/certificados/{certify}', 'CertifyController@destroy')->name('certifyDestroy');

    Route::put('/configuracoes', 'SettingController@update')->name('settingUpdate');
    Route::get('/configuracoes/senha/limpar', 'SettingController@clearUserMailPassword')->name('settingClearUserMailPassowrd');

    Route::get('/atualizar', 'SettingController@updateApplication')
        ->name('updateApplication');

    Route::post('/cursos', 'CourseController@store')->name('courseStore');
    Route::put('/cursos/{course}', 'CourseController@update')->name('courseUpdate');
    Route::delete('/cursos/{course}', 'CourseController@destroy')->name('courseDestroy');
    Route::get('/cursos/importar', function (){
        return redirect()->route('home');
    });
    Route::get('/cursos/{course}/exportar', function (){
        return redirect()->route('home');
    });
    Route::post('/cursos/importar', 'CourseController@import')->name('courseImport');
    Route::post('/cursos/{course}/exportar', 'CourseController@export')->name('courseExport');

    Route::get('/aulas', function (){
        return redirect()->route('home');
    })->name('lessons');
    Route::post('/aulas', 'LessonController@store')->name('lessonStore');
    Route::post('/aulas/importar', 'LessonController@importStore')->name('lessomImportStore');
    Route::put('/aulas/{lesson}', 'LessonController@update')->name('lessonUpdate');
    Route::delete('/aulas/{lesson}', 'LessonController@destroy')->name('lessonDestroy');

    Route::post('/modulos', 'ModuleController@store')->name('moduleStore');
    Route::put('/modulos/{module}', 'ModuleController@update')->name('moduleUpdate');
    Route::delete('/modulos/{module}', 'ModuleController@destroy')->name('moduleDestroy');

    Route::get('/atividades', function (){
        return redirect()->route('home');
    })->name('activities');
    Route::post('/atividades', 'ActivityController@store')->name('activityStore');
    Route::put('/atividades/{activity}', 'ActivityController@update')->name('activityUpdate');
    Route::delete('/atividades/{activity}', 'ActivityController@destroy')->name('activityDestroy');

    Route::get('/questoes', function (){
        return redirect()->route('home');
    })->name('questoes');
    Route::get('/questoes/{question}', function (){
        return redirect()->route('home');
    })->name('questionShow');
    Route::post('/questoes', 'QuestionController@store')->name('questionStore');
    Route::put('/questoes/{question}', 'QuestionController@update')->name('questionUpdate');
    Route::delete('/questoes/{question}', 'QuestionController@destroy')->name('questionDestroy');
    
    Route::get('/itens', function (){
        return redirect()->route('home');
    })->name('itens');
    Route::get('/itens/{item}', function (){
        return redirect()->route('home');
    })->name('itemShow');
    Route::post('/itens', 'ItemController@store')->name('itemStore');
    Route::put('/itens/{item}', 'ItemController@update')->name('itemUpdate');
    Route::delete('/itens/{item}', 'ItemController@destroy')->name('itemDestroy');
});

Route::middleware(['subscribed', 'checkprogress', 'mathjax', 'menugenerator'])->group(function () {
    Route::get('/cursos/{course}', 'CourseController@show')->name('courseShow');
});

Route::middleware('auth', 'menugenerator', 'paginate')->group(function () {
    Route::get('/usuarios', 'UserController@index')->name('users');
    Route::get('/certificados', 'CertifyController@index')->name('certifies');
});

Route::middleware('auth', 'menugenerator')->group(function () {
    Route::get('/usuarios/novo', 'UserController@create')->name('userCreate');
    Route::get('/usuarios/remover', 'UserController@destroyConfirmation')->name('userDestroyConfirmation');
    Route::get('/usuarios/{user}', 'UserController@show')->name('userShow');
    Route::get('/usuarios/{user}/editar', 'UserController@edit')->name('userEdit');
    Route::get('/usuarios/{user}/senha', 'UserController@editPassword')->name('userEditPassword');

    Route::get('/certificados/{certify}', 'CertifyController@show')->name('certifyShow');

    Route::get('/meupainel', 'PanelController@show')->name('mypanel');
    Route::get('/contayoutube', 'PageController@index')->name('youtubeAccount');

    Route::get('/configuracoes', 'SettingController@index')->name('settings');
});

Route::middleware('auth', 'mathjax', 'menugenerator')->group(function () {
    Route::get('/novo/cursos', 'CourseController@create')->name('courseCreate');
    Route::get('/cursos/{course}/editar', 'CourseController@edit')->name('courseEdit');
     
    Route::get('/modulos/{module}/aulas/novo', 'LessonController@create')->name('lesson');
    Route::get('/modulos/{module}/aulas/importar', 'LessonController@import')->name('lessonImport');
    
    Route::get('/aulas/{lesson}/editar', 'LessonController@edit')->name('lessonEdit');
    Route::get('/cursos/{course}/modulos/novo', 'ModuleController@create')->name('module');
    
    Route::get('/modulos/{module}/editar', 'ModuleController@edit')->name('moduleEdit');  
    Route::get('/modulos/{module}/atividades/novo', 'ActivityController@create')->name('activity');
    
    Route::get('/atividades/{activity}/editar', 'ActivityController@edit')->name('activityEdit');
    Route::get('/atividades/{activity}/questoes/novo', 'QuestionController@create')->name('question');
    
    Route::get('/questoes/{question}/editar', 'QuestionController@edit')->name('questionEdit');
    Route::get('/questoes/{question}/item/novo', 'ItemController@create')->name('item');
    
    Route::get('/itens/{item}/editar', 'ItemController@edit')->name('itemEdit');    
});

Route::middleware(['auth', 'subscribed', 'checkprogress', 'mathjax', 'menugenerator'])->group(function () {
    Route::get('/aulas/{lesson}', 'LessonController@show')->name('lessonShow');
    Route::get('/atividades/{activity}', 'ActivityController@show')->name('activityShow');
    Route::post('/atividades/{activity}/responder', 'ActivityController@answer')->name('activityAnswer');
});