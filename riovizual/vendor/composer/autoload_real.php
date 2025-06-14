<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit58a2a4ea6ed78ee90e7aab2927062cfe
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit58a2a4ea6ed78ee90e7aab2927062cfe', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit58a2a4ea6ed78ee90e7aab2927062cfe', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit58a2a4ea6ed78ee90e7aab2927062cfe::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
