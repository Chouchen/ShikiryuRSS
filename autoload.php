<?php

spl_autoload_register(static function ($class) {

    $folders = explode('\\', $class);

    if ($folders[0] === 'Shikiryu' && $folders[1] === 'SRSS') {

        $folders = array_slice($folders, 2);

        $path = sprintf('%s%ssrc%s%s.php',
            __DIR__,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            implode(DIRECTORY_SEPARATOR, $folders)
        );

        if (file_exists($path)) {
            require $path;
        }

    }

});