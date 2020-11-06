#!/usr/bin/env php
<?php

namespace App;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/RabbitMQCLI/Command/DeleteConnectionsCommand.php';
require __DIR__ . '/../src/RabbitMQCLI/Command/ListConnectionsCommand.php';
require __DIR__ . '/../src/RabbitMQCLI/Command/MonitorConnectionsCommand.php';

use Symfony\Component\Console\Application;
use App\Command\DeleteConnections;
use App\Command\ListConnections;
use App\Command\MonitorConnections;

/**
 * Initialization
 */
$user_name = getenv('RABBITMQ_USERNAME');
$password = getenv('RABBITMQ_PASS');
$host = getenv('RABBITMQ_HOST');
$port = getenv('RABBITMQ_PORT');

if ($user_name === false ||
    $password === false ||
    $host === false ||
    $port === false
) {
    exit("Authorization Failed: Missing environment variable");
}

$user_info = $user_name . ":" . $password;

$utf8_urldecode = function ($user_info) {
    return html_entity_decode(
        preg_replace(
            "/%u([0-9a-f]{3,4})/i",
            "&#x\\1;",
            urldecode($user_info)
        ),
        null,
        'UTF-8'
    );
};

$authorization = $utf8_urldecode(urlencode($user_info));
$authorization = base64_encode($authorization);

define("RABBITMQ_AUTH_TOKEN", $authorization);
define("RABBITMQ_CONNECTION", $host . ":" . $port);

/**
 * Starting application
 */
$app = new Application();

$app->add(new ListConnections());
$app->add(new MonitorConnections());
$app->add(new DeleteConnections());

$app->run();
