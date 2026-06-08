<?php

// Uren
$router->get('/uren', 'TimeController', 'index');
$router->get('/uren/nieuw', 'TimeController', 'create');
$router->post('/uren/nieuw', 'TimeController', 'store');
$router->get('/uren/export', 'TimeController', 'exportCsv');
$router->get('/uren/{id}/edit', 'TimeController', 'edit');
$router->post('/uren/{id}/edit', 'TimeController', 'update');
$router->post('/uren/{id}/delete', 'TimeController', 'delete');

// Kilometers
$router->get('/kilometers', 'TimeController', 'kmIndex');
$router->get('/kilometers/nieuw', 'TimeController', 'kmCreate');
$router->post('/kilometers/nieuw', 'TimeController', 'kmStore');
$router->get('/kilometers/{id}/edit', 'TimeController', 'kmEdit');
$router->post('/kilometers/{id}/edit', 'TimeController', 'kmUpdate');
$router->post('/kilometers/{id}/delete', 'TimeController', 'kmDelete');
