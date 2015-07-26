<?php

spl_autoload_register(function($class) {

    if (!empty($class)) {

        $prefix = 'TreeClosureTable\\';

        $baseDir = __DIR__ . '/src/';

        $lengthBaseNamespace = mb_strlen($prefix);

        if (strncmp($prefix, $class, $lengthBaseNamespace) !== 0) {
            return;
        }

        $fileClass =
            str_replace(['\\','/'], DIRECTORY_SEPARATOR, rtrim($baseDir, '\\//')).
            DIRECTORY_SEPARATOR.
            trim(str_replace('\\', DIRECTORY_SEPARATOR, substr($class, $lengthBaseNamespace)), '\\//').
            '.php';

        if (file_exists($fileClass)) {
            require $fileClass;
        }
    }
});