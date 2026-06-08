<?php

declare(strict_types=1);

// ─── Uitgaven / Expenses ─────────────────────────────────────────────────────
$router->get('/uitgaven',                        'ExpenseController', 'index');
$router->get('/uitgaven/nieuw',                  'ExpenseController', 'create');
$router->post('/uitgaven/opslaan',               'ExpenseController', 'store');
$router->get('/uitgaven/exporteer',              'ExpenseController', 'exportCsv');
$router->get('/uitgaven/{id}',                   'ExpenseController', 'show');
$router->get('/uitgaven/{id}/bewerken',          'ExpenseController', 'edit');
$router->post('/uitgaven/{id}/bijwerken',        'ExpenseController', 'update');
$router->post('/uitgaven/{id}/verwijderen',      'ExpenseController', 'delete');
$router->get('/uitgaven/{id}/bonnetje',          'ExpenseController', 'receipt');
