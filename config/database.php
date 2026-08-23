<?php

declare(strict_types=1);

function createDatabaseConnection(): PDO
{
    $host = getenv('FDC_DB_HOST') ?: 'figurinhasdascopas.com.br';
    $port = getenv('FDC_DB_PORT') ?: '3306';
    $database = getenv('FDC_DB_NAME') ?: 'figurinh_copas';
    $username = getenv('FDC_DB_USER') ?: 'figurinh_copa';
    $password = getenv('FDC_DB_PASSWORD') ?: '~zK[O&alHYB8';

    $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $database);

    return new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 5,
    ]);
}
