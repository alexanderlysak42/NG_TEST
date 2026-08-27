<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use App\Controllers\PageAController;
use App\Controllers\RegistrationController;

session_start();

$method = $_SERVER['REQUEST_METHOD'];
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($method === 'GET' && $path === '/') {
    (new RegistrationController())->showForm();
    return;
}

if ($method === 'POST' && $path === '/register') {
    (new RegistrationController())->register();
    return;
}

if ($method === 'GET' && preg_match('#^/p/([a-f0-9]{64})$#', $path, $m)) {
    (new PageAController())->show($m[1]);
    return;
}

if ($method === 'POST' && preg_match('#^/p/([a-f0-9]{64})/regenerate$#', $path, $m)) {
    (new PageAController())->regenerate($m[1]);
    return;
}

if ($method === 'POST' && preg_match('#^/p/([a-f0-9]{64})/deactivate$#', $path, $m)) {
    (new PageAController())->deactivate($m[1]);
    return;
}

if ($method === 'POST' && preg_match('#^/p/([a-f0-9]{64})/play$#', $path, $m)) {
    (new PageAController())->play($m[1]);
    return;
}

if ($method === 'GET' && preg_match('#^/p/([a-f0-9]{64})/history$#', $path, $m)) {
    (new PageAController())->history($m[1]);
    return;
}

http_response_code(404);
echo '404 Not Found';