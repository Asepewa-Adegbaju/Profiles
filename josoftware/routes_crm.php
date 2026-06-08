<?php
// CRM
$router->get('/crm', 'CrmController', 'index');
$router->get('/crm/meetings', 'CrmController', 'meetings');
$router->get('/crm/import', 'CrmController', 'showImport');
$router->post('/crm/import', 'CrmController', 'processImport');
$router->get('/crm/create', 'CrmController', 'createCompany');
$router->post('/crm/create', 'CrmController', 'storeCompany');
$router->get('/crm/{id}', 'CrmController', 'showCompany');
$router->get('/crm/{id}/edit', 'CrmController', 'editCompany');
$router->post('/crm/{id}/edit', 'CrmController', 'updateCompany');
$router->post('/crm/{id}/delete', 'CrmController', 'deleteCompany');
$router->post('/crm/{id}/contact', 'CrmController', 'storeContact');
$router->post('/crm/contact/{id}/delete', 'CrmController', 'deleteContact');
$router->post('/crm/{id}/log', 'CrmController', 'storeLog');
$router->get('/crm/{id}/meeting', 'CrmController', 'createMeeting');
$router->post('/crm/{id}/meeting', 'CrmController', 'storeMeeting');
$router->post('/crm/meeting/{id}/status', 'CrmController', 'updateMeetingStatus');
