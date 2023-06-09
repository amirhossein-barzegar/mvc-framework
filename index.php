<?php

session_start();
ini_set('display_errors',"1");

require __DIR__ . '/vendor/autoload.php';

use App\Controllers\AdminController;
use App\Controllers\Article\ArticleController;
use App\Controllers\Article\ChapterController;
use App\Controllers\Article\LawCollectionController;
use App\Controllers\Article\SectionController;
use App\Controllers\Article\TopicController;
use App\Controllers\Question\CategoryController;
use App\Controllers\Question\QuestionController;

$core = \App\Core::getInstance();

//$core->addMiddleware(\App\Middlewares\Core\VisitMiddleware::class);
//$core->router->addRoute('GET', '/customers', CustomerController::class, 'customers');
//// Customers Authentication Routes
//$core->router->addRoute('POST', '/register', CustomerController::class, 'register')
//    ->addMiddleware(\App\Middlewares\GuestMiddleware::class)
//    ->addMiddleware(\App\Middlewares\AuthAttemptMiddleware::class);
//$core->router->addRoute('POST', '/login', CustomerController::class, 'login')
//    ->addMiddleware(\App\Middlewares\GuestMiddleware::class)
//    ->addMiddleware(\App\Middlewares\AuthAttemptMiddleware::class);
//$core->router->addRoute('POST', '/confirm', CustomerController::class, 'confirm')
//    ->addMiddleware(\App\Middlewares\GuestMiddleware::class);
//$core->router->addRoute('POST', '/logout', CustomerController::class, 'logout')
//    ->addMiddleware(\App\Middlewares\Customer\TokenMiddleware::class);

// Admin Routes
//$core->router->addRoute('POST', '/admin/register', AdminController::class, 'register');
$core->router->addRoute('POST', '/admin/login', AdminController::class, 'login');
$core->router->addRoute('POST', '/admin/logout', AdminController::class, 'logout');
$core->router->addGroup(function($router) {
    // Law Collection Routes
    $router->haveRoute('GET', '/law-collections', LawCollectionController::class, 'all');
    $router->haveRoute('POST', '/law-collections', LawCollectionController::class, 'create');
    $router->haveRoute('GET', '/law-collections/:id', LawCollectionController::class, 'show');
    $router->haveRoute('PUT', '/law-collections/:id', LawCollectionController::class, 'update');
    $router->haveRoute('DELETE', '/law-collections/:id', LawCollectionController::class, 'delete');
        // Each law collection has many sections
        $router->haveRoute('GET', '/law-collections/:id/sections', LawCollectionController::class, 'getSections');
    // Section Routes
    $router->haveRoute('GET', '/sections', SectionController::class, 'all');
    $router->haveRoute('POST', '/sections', SectionController::class, 'create');
    $router->haveRoute('GET', '/sections/:id', SectionController::class, 'show');
    $router->haveRoute('PUT', '/sections/:id', SectionController::class, 'update');
    $router->haveRoute('DELETE', '/sections/:id', SectionController::class, 'delete');
        // Each Section belongs to a law collection
        $router->haveRoute('GET', '/sections/:id/law-collection', SectionController::class, 'getLawCollection');
        // Each Section has many chapters
        $router->haveRoute('GET', '/sections/:id/chapters', SectionController::class, 'getChapters');
    // Chapter Routes
    $router->haveRoute('GET', '/chapters', ChapterController::class, 'all');
    $router->haveRoute('POST', '/chapters', ChapterController::class, 'create');
    $router->haveRoute('GET', '/chapters/:id', ChapterController::class, 'show');
    $router->haveRoute('PUT', '/chapters/:id', ChapterController::class, 'update');
    $router->haveRoute('DELETE', '/chapters/:id', ChapterController::class, 'delete');
        // Each chapter belongs to a section
        $router->haveRoute('GET', '/chapters/:id/section', ChapterController::class, 'getSection');
        // Each topic has many topics
        $router->haveRoute('GET', '/chapters/:id/topics', ChapterController::class, 'getTopics');
    // Topic Routes
   $router->haveRoute('GET', '/topics', TopicController::class, 'all');
   $router->haveRoute('POST', '/topics', TopicController::class, 'create');
   $router->haveRoute('GET', '/topics/:id', TopicController::class, 'show');
   $router->haveRoute('PUT', '/topics/:id', TopicController::class, 'update');
   $router->haveRoute('DELETE', '/topics/:id', TopicController::class, 'delete');
        // Each Topic belongs to a chapter
        $router->haveRoute('GET', '/topics/:id/chapter', TopicController::class, 'getChapter');
        $router->haveRoute('GET', '/topics/:id/articles', TopicController::class, 'getArticles');
    // Article Routes
    $router->haveRoute('GET', '/articles', ArticleController::class, 'all');
    $router->haveRoute('POST', '/articles', ArticleController::class, 'create');
    $router->haveRoute('GET', '/articles/:id', ArticleController::class, 'show');
    $router->haveRoute('PUT', '/articles/:id', ArticleController::class, 'update');
    $router->haveRoute('DELETE', '/articles/:id', ArticleController::class, 'delete');
        // Each article belongs to a topic
        $router->haveRoute('GET', 'articles/:id/topic', ArticleController::class, 'getTopic');
    // Question Routes
    $router->haveRoute('GET', '/questions', QuestionController::class, 'all');
    $router->haveRoute('POST', '/questions', QuestionController::class, 'create');
    $router->haveRoute('GET', '/questions/:id', QuestionController::class, 'show');
    $router->haveRoute('PUT', '/questions/:id', QuestionController::class, 'update');
    $router->haveRoute('DELETE', '/questions/:id', QuestionController::class, 'delete');
    // Category Routes
    $router->haveRoute('GET', '/categories', CategoryController::class, 'all');
    $router->haveRoute('POST', '/categories', CategoryController::class, 'create');
    $router->haveRoute('GET', '/categories/:id', CategoryController::class, 'show');
    $router->haveRoute('PUT', '/categories/:id', CategoryController::class, 'update');
    $router->haveRoute('DELETE', '/categories/:id', CategoryController::class, 'delete');
},[
    new \App\Middlewares\AuthAdminMiddleware(),
]);

// Customer Passwords Routes
//$core->router->addRoute('GET', '/passwords/:id/customer', CustomerController::class, 'getCustomer');
//// Customer Routes
//$core->router->addRoute('GET', '/customers/:id/passwords', CustomerController::class, 'getPasswords');





//$core->router->addRoute('GET','/', '\App\Controllers\UserController', 'home','home',[])
//    ->addMiddleware(new \App\Middlewares\AuthMiddleware())
//    ->addMiddleware(new \App\Middlewares\AccessMiddleware());
//$core->router->addRoute('GET','/user/:id', '\App\Controllers\UserController', 'index','single-user');
//$core->router->addRoute('GET','/user/:id/categories/:categoryId/:something', '\App\Controllers\UserController', 'category');

$core->run();