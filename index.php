<?php
// Start session
session_start();

// Load configuration
require_once 'config/database.php';
require_once 'utils/Router.php';
require_once 'utils/Session.php';
require_once 'controllers/TaskController.php';
require_once 'controllers/CategoryController.php';
require_once 'controllers/UserController.php';

// Initialize router
$router = new Router();

// Register routes
$router->get('/', function() {
    $taskController = new TaskController();
    $taskController->dashboard();
});

// Task routes
$router->get('/tasks', function() {
    $taskController = new TaskController();
    $taskController->index();
});

$router->get('/tasks/create', function() {
    $taskController = new TaskController();
    $taskController->create();
});

$router->post('/tasks/store', function() {
    $taskController = new TaskController();
    $taskController->store();
});

$router->get('/tasks/edit', function() {
    $taskController = new TaskController();
    $taskController->edit();
});

$router->post('/tasks/update', function() {
    $taskController = new TaskController();
    $taskController->update();
});

$router->get('/tasks/view', function() {
    $taskController = new TaskController();
    $taskController->view();
});

$router->post('/tasks/delete', function() {
    $taskController = new TaskController();
    $taskController->delete();
});

// Category routes
$router->get('/categories', function() {
    $categoryController = new CategoryController();
    $categoryController->index();
});

$router->get('/categories/create', function() {
    $categoryController = new CategoryController();
    $categoryController->create();
});

$router->post('/categories/store', function() {
    $categoryController = new CategoryController();
    $categoryController->store();
});

$router->get('/categories/edit', function() {
    $categoryController = new CategoryController();
    $categoryController->edit();
});

$router->post('/categories/update', function() {
    $categoryController = new CategoryController();
    $categoryController->update();
});

$router->post('/categories/delete', function() {
    $categoryController = new CategoryController();
    $categoryController->delete();
});

// User routes
$router->get('/login', function() {
    $userController = new UserController();
    $userController->loginForm();
});

$router->post('/login', function() {
    $userController = new UserController();
    $userController->login();
});

$router->get('/register', function() {
    $userController = new UserController();
    $userController->registerForm();
});

$router->post('/register', function() {
    $userController = new UserController();
    $userController->register();
});

$router->get('/logout', function() {
    $userController = new UserController();
    $userController->logout();
});

// Handle the current request
$router->route();
