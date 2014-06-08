<?php spl_autoload_register(function ($class) {
    $prefix = 'RedBeanPHP\\Plugins\\RedSql\\';
    if ( strpos($class, $prefix) === 0 ) {
        $file = __DIR__ . '/src/' . str_replace('\\', '/', str_replace($prefix, '', $class)) . '.php';
        if (file_exists($file)) {
            include $file;
        }
    }
}, true, true);

include __DIR__ . '/bootstrap.php';
