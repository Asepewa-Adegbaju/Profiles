<?php

// Financiën
$router->get('/financien', 'FinanceController', 'index');

// Offertes
$router->get('/financien/offertes', 'FinanceController', 'quotes');
$router->get('/financien/offertes/nieuw', 'FinanceController', 'createQuote');
$router->post('/financien/offertes/nieuw', 'FinanceController', 'storeQuote');
$router->get('/financien/offertes/{id}', 'FinanceController', 'showQuote');
$router->post('/financien/offertes/{id}/status', 'FinanceController', 'updateQuoteStatus');
$router->post('/financien/offertes/{id}/delete', 'FinanceController', 'deleteQuote');
$router->get('/financien/offertes/{id}/print', 'FinanceController', 'printQuote');

// Facturen
$router->get('/financien/facturen', 'FinanceController', 'invoices');
$router->get('/financien/facturen/nieuw', 'FinanceController', 'createInvoice');
$router->post('/financien/facturen/nieuw', 'FinanceController', 'storeInvoice');
$router->get('/financien/facturen/{id}', 'FinanceController', 'showInvoice');
$router->post('/financien/facturen/{id}/status', 'FinanceController', 'updateInvoiceStatus');
$router->post('/financien/facturen/{id}/delete', 'FinanceController', 'deleteInvoice');
$router->get('/financien/facturen/{id}/print', 'FinanceController', 'printInvoice');
