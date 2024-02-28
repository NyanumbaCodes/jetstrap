<?php

namespace NascentAfrica\Jetstrap;

class Jetstrap
{
    const STACK_LIVEWIRE = 'livewire';
    const STACK_INERTIA = 'inertia';

    const VIEWS_AUTH_DIR = 'views/auth';

    const VIEWS_LAYOUTS_DIR = 'views/layouts';

    const JS_JETSTREAM_COMP_DIR = 'js/Components';

    const JS_LAYOUT_DIR = 'js/Layouts';

    const JS_PAGES_DIR = 'js/Pages';

    const JS_PAGES_API_DIR = 'js/Pages/API';

    const JS_PAGES_AUTH_DIR = 'js/Pages/Auth';

    const JS_PAGES_PROFILE_DIR = 'js/Pages/Profile';

    const JS_COMPONENTS_DIR = 'views/components';

    const JS_APP_FILE = 'js/app.js';

    const VIEWS_WELCOME_FILE = 'views/welcome.blade.php';

    const VIEWS_APP_FILE = 'views/app.blade.php';

    const VIEWS_DASHBOARD_FILE = 'views/dashboard.blade.php';

    const RUN_NPM_MESSAGE = 'Please execute the "npm install && npm run dev" command to build your assets.';

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param bool $dev
     * @return void
     */
    public static function updateNodePackages(callable $callback, bool $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT).PHP_EOL
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function breezeResourcesPath(string $path = ''): string
    {
        return self::breezePath(
            'resources'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function breezeInertiaPath(string $path = ''): string
    {
        return self::breezePath(
            'inertia'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function breezePath(string $path = ''): string
    {
        return self::stubsPath(
            'breeze'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function jetstreamInertiaResources(string $path = ''): string
    {
        return self::jetstreamPath(
            'inertia'
            .DIRECTORY_SEPARATOR
            .'resources'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );

    }

    /**
     * @param string $path
     * @return string
     */
    public static function jetstreamLivewire(string $path = ''): string
    {
        return self::jetstreamPath(
            'livewire'
            .DIRECTORY_SEPARATOR
            .'resources'
            .DIRECTORY_SEPARATOR
            .'views'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function jetstreamResourcesPath(string $path = ''): string
    {
        return self::jetstreamPath(
            'resources'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );

    }

    /**
     * @param string $path
     * @return string
     */
    public static function jetstreamPath(string $path = ''): string
    {
        return self::stubsPath(
            'jetstream'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    public static function stubsPath(string $path = ''): string
    {
        return self::basePath(
            'stubs'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function resourcePath(string $path = ''): string
    {
        return self::basePath(
            'resources'
            .($path ? DIRECTORY_SEPARATOR.$path : $path)
        );
    }

    /**
     * @param string $path
     * @return string
     */
    public static function basePath(string $path = ''): string
    {
        return __DIR__ . DIRECTORY_SEPARATOR
            .'..'.DIRECTORY_SEPARATOR.'..'
            .($path ? DIRECTORY_SEPARATOR.$path : $path);
    }
}