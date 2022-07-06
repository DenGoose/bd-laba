<?php
$router = new \Silex\Application();

$router->get('/', [\App\Controller\IndexController::class, 'exec']);

$router->get('/order/', [\App\Controller\OrderController::class, 'show']);
$router->get('/order/add/', [\App\Controller\OrderController::class, 'add']);
$router->post('/order/add/', [\App\Controller\OrderController::class, 'addAction']);
$router->get('/order/update/', [\App\Controller\OrderController::class, 'update']);
$router->post('/order/update/', [\App\Controller\OrderController::class, 'updateAction']);
$router->get('/order/delete/', [\App\Controller\OrderController::class, 'deleteAction']);

$router->get('/pick-point/', [\App\Controller\PickPointController::class, 'show']);
$router->get('/pick-point/add/', [\App\Controller\PickPointController::class, 'add']);
$router->post('/pick-point/add/', [\App\Controller\PickPointController::class, 'addAction']);
$router->get('/pick-point/update/', [\App\Controller\PickPointController::class, 'update']);
$router->post('/pick-point/update/', [\App\Controller\PickPointController::class, 'updateAction']);
$router->get('/pick-point/delete/', [\App\Controller\PickPointController::class, 'deleteAction']);

$router->get('/product/', [\App\Controller\ProductController::class, 'show']);
$router->get('/product/add/', [\App\Controller\ProductController::class, 'add']);
$router->post('/product/add/', [\App\Controller\ProductController::class, 'addAction']);
$router->get('/product/update/', [\App\Controller\ProductController::class, 'update']);
$router->post('/product/update/', [\App\Controller\ProductController::class, 'updateAction']);
$router->get('/product/delete/', [\App\Controller\ProductController::class, 'deleteAction']);

$router->get('/stock/', [\App\Controller\StockController::class, 'show']);
$router->get('/stock/add/', [\App\Controller\StockController::class, 'add']);
$router->post('/stock/add/', [\App\Controller\StockController::class, 'addAction']);
$router->get('/stock/update/', [\App\Controller\StockController::class, 'update']);
$router->post('/stock/update/', [\App\Controller\StockController::class, 'updateAction']);
$router->get('/stock/delete/', [\App\Controller\StockController::class, 'deleteAction']);

$router->get('/user/', [\App\Controller\UserController::class, 'show']);
$router->get('/user/add/', [\App\Controller\UserController::class, 'add']);
$router->post('/user/add/', [\App\Controller\UserController::class, 'addAction']);
$router->get('/user/update/', [\App\Controller\UserController::class, 'update']);
$router->post('/user/update/', [\App\Controller\UserController::class, 'updateAction']);
$router->get('/user/delete/', [\App\Controller\UserController::class, 'deleteAction']);

$router->get('/product-section/', [\App\Controller\SectionController::class, 'show']);
$router->get('/product-section/add/', [\App\Controller\SectionController::class, 'add']);
$router->post('/product-section/add/', [\App\Controller\SectionController::class, 'addAction']);
$router->get('/product-section/update/', [\App\Controller\SectionController::class, 'update']);
$router->post('/product-section/update/', [\App\Controller\SectionController::class, 'updateAction']);
$router->get('/product-section/delete/', [\App\Controller\SectionController::class, 'deleteAction']);

$router->run();