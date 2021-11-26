<?php

use Aura\SqlQuery\QueryFactory;
use Delight\Auth\Auth;
use DI\ContainerBuilder;
use JasonGrimes\Paginator;
use League\Plates\Engine;


$containerBuilder = new ContainerBuilder();
$containerBuilder->addDefinitions([
    Engine::class => function() {
        return new Engine('../app/views');
    },

    PDO::class => function() {
        $driver = 'mysql';
        $host = "localhost";
        $dbname = "minicr";
        $username = "root";
        $pass = "";

        return new PDO("$driver:host=$host; dbname=$dbname", $username, $pass);
    },

    Auth::class => function($container) {
        return new Auth($container->get('PDO'), '', '', false);
    },

    QueryFactory::class => function() {
        return new QueryFactory('mysql');
    },

    Paginator::class => function($totalItems, $itemsPerPage, $currentPage, $urlPattern) {
        return new Auth($totalItems, $itemsPerPage, $currentPage, $urlPattern);
    }
]);

$container = $containerBuilder->build();