<?php
// vendor/autoload.php (manual)
spl_autoload_register(function ($class) {
    if (strpos($class, 'Firebase\JWT\\') === 0) {
        $file = __DIR__ . '/firebase/php-jwt/src/' . str_replace('Firebase\JWT\\', '', $class) . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
});