<?php

// Projecten
$router->get('/projecten', 'ProjectController', 'index');
$router->get('/projecten/nieuw', 'ProjectController', 'create');
$router->post('/projecten/nieuw', 'ProjectController', 'store');
$router->get('/projecten/{id}', 'ProjectController', 'show');
$router->get('/projecten/{id}/edit', 'ProjectController', 'edit');
$router->post('/projecten/{id}/edit', 'ProjectController', 'update');
$router->post('/projecten/{id}/delete', 'ProjectController', 'delete');
$router->post('/projecten/{id}/taak', 'ProjectController', 'storeTask');
$router->get('/projecten/{id}/taak/{tid}/edit', 'ProjectController', 'editTask');
$router->post('/projecten/{id}/taak/{tid}/edit', 'ProjectController', 'updateTask');
$router->post('/projecten/{id}/taak/{tid}/status', 'ProjectController', 'updateTaskStatus');
$router->post('/projecten/{id}/taak/{tid}/delete', 'ProjectController', 'deleteTask');
