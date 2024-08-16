<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1660330226afc66a168d10789c05fda8
{
    public static $files = array (
        '92326731335397e866f8396ea4110b18' => __DIR__ . '/../..' . '/includes/functions.php',
    );

    public static $prefixLengthsPsr4 = array (
        'R' => 
        array (
            'RHC\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'RHC\\' => 
        array (
            0 => __DIR__ . '/../..' . '/',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1660330226afc66a168d10789c05fda8::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1660330226afc66a168d10789c05fda8::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1660330226afc66a168d10789c05fda8::$classMap;

        }, null, ClassLoader::class);
    }
}
