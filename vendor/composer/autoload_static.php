<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite0d69727fc0803c47c28d8f0f088803e
{
    public static $files = array (
        '92c8763cd6170fce6fcfe7e26b4e8c10' => __DIR__ . '/..' . '/symfony/phpunit-bridge/bootstrap.php',
    );

    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Symfony\\Bridge\\PhpUnit\\' => 23,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Symfony\\Bridge\\PhpUnit\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/phpunit-bridge',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite0d69727fc0803c47c28d8f0f088803e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite0d69727fc0803c47c28d8f0f088803e::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
