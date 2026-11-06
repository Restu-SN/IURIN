<?php

namespace App\Libraries;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

/**
 * Membuat EntityManager Doctrine dari pengaturan database CodeIgniter (.env).
 */
class Doctrine
{
    private static ?EntityManager $em = null;

    public static function em(): EntityManager
    {
        if (self::$em === null) {
            $config = ORMSetup::createAttributeMetadataConfiguration(
                paths: [APPPATH . 'Entities'],
                isDevMode: ENVIRONMENT !== 'production',
                proxyDir: WRITEPATH . 'cache/doctrine',
                // Cache per request saja: entitasnya sedikit, dan perubahan tabel
                // langsung terbaca tanpa harus mengosongkan cache di hosting.
                cache: new ArrayAdapter(),
            );
            $config->setAutoGenerateProxyClasses(true);

            $db = config('Database')->default;
            $conn = DriverManager::getConnection([
                'driver'   => 'pdo_mysql',
                'host'     => $db['hostname'],
                'port'     => (int) ($db['port'] ?? 3306),
                'dbname'   => $db['database'],
                'user'     => $db['username'],
                'password' => $db['password'],
                'charset'  => 'utf8mb4',
            ], $config);

            self::$em = new EntityManager($conn, $config);
        }

        return self::$em;
    }
}
