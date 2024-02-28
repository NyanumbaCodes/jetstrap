<?php declare(strict_types=1);

namespace NascentAfrica\Jetstrap\Console\SwapHandlers;

use Illuminate\Filesystem\Filesystem;
use NascentAfrica\Jetstrap\Jetstrap;
use NascentAfrica\Jetstrap\JetstrapFacade;

/**
 * Class BreezeHandler
 *
 * @package NascentAfrica\Jetstrap\Console\SwapHandlers\Breeze
 */
class BreezeHandler extends AbstractHandler
{
    /**
     * @return string
     */
    public static function getStartMessage(): string
    {
        return "Swapping Breeze resources to Bootstrap.";
    }

    /**
     * @return string
     */
    public static function getFinishMessage(): string
    {
        return "Breeze resources swapped successfully.";
    }

    public function swap()
    {
        switch ($this->stack) {
            case Jetstrap::STACK_LIVEWIRE:
                $this->swapLivewireStack($this->getFile());
                break;
            case Jetstrap::STACK_INERTIA:
                $this->swapInertiaStack($this->getFile());
                break;
            default:
                throw new \InvalidArgumentException("Invalid Breeze stack [{$this->stack}]");
        }
    }

    /**
     * Swap TailwindCss resources in Laravel Breeze.
     *
     * @return void
     */
    protected function swapLivewireStack(Filesystem $file)
    {
        // Views...
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_AUTH_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_COMPONENTS_DIR));

        $file->copyDirectory(
            Jetstrap::breezeResourcesPath(Jetstrap::VIEWS_AUTH_DIR), resource_path(Jetstrap::VIEWS_AUTH_DIR)
        );
        $file->copyDirectory(
            Jetstrap::breezeResourcesPath(Jetstrap::VIEWS_LAYOUTS_DIR), resource_path(Jetstrap::VIEWS_LAYOUTS_DIR)
        );
        $file->copyDirectory(
            Jetstrap::breezeResourcesPath(Jetstrap::JS_COMPONENTS_DIR),
            resource_path(Jetstrap::JS_COMPONENTS_DIR)
        );

        copy(Jetstrap::breezeResourcesPath(
            Jetstrap::VIEWS_DASHBOARD_FILE),
            resource_path(Jetstrap::VIEWS_DASHBOARD_FILE));
        copy(Jetstrap::stubsResourcesPath(Jetstrap::VIEWS_WELCOME_FILE), resource_path(Jetstrap::VIEWS_WELCOME_FILE));
    }

    /**
     * Install the Inertia Breeze stack.
     *
     * @return void
     */
    protected function swapInertiaStack(Filesystem $file)
    {
        // NPM Packages...
        JetstrapFacade::updateNodePackages(function ($packages) {
            return [
                    '@inertiajs/inertia' => '^0.10.0',
                    '@inertiajs/inertia-vue3' => '^0.5.1',
                    '@inertiajs/progress' => '^0.2.6',
                    'vue' => '^3.0.5',
                    '@vue/compiler-sfc' => '^3.0.5',
                    'vue-loader' => '^16.1.2',
                ] + $packages;
        });

        // Views...
        copy(Jetstrap::stubsInertiaResources(Jetstrap::VIEWS_APP_FILE), resource_path(Jetstrap::VIEWS_APP_FILE));

        copy(Jetstrap::stubsPath('inertia/webpack.mix.js'), base_path('webpack.mix.js'));

        // Assets...
        copy(Jetstrap::stubsInertiaResources(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        // Components + Pages...
        $file->ensureDirectoryExists(resource_path('js/Components'));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_LAYOUT_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_DIR));

        $file->copyDirectory(
            Jetstrap::breezeInertiaPath('resources/js/Components'),
            resource_path('js/Components')
        );
        $file->copyDirectory(
            Jetstrap::breezeInertiaPath('resources/js/Layouts'),
            resource_path(Jetstrap::JS_LAYOUT_DIR)
        );
        $file->copyDirectory(Jetstrap::breezeInertiaPath('resources/js/Pages'), resource_path(Jetstrap::JS_PAGES_DIR));

        if ($file->exists(resource_path('js/Components/ResponsiveNavLink.vue'))) {
            $file->delete(resource_path('js/Components/ResponsiveNavLink.vue'));
        }

        copy(Jetstrap::stubsInertiaResources('js/Jetstream/Button.vue'), resource_path('js/Components/Button.vue'));
        copy(Jetstrap::stubsInertiaResources('js/Jetstream/Checkbox.vue'), resource_path('js/Components/Checkbox.vue'));
        copy(Jetstrap::stubsInertiaResources('js/Jetstream/Dropdown.vue'), resource_path('js/Components/Dropdown.vue'));
        copy(Jetstrap::stubsInertiaResources(
            'js/Jetstream/DropdownLink.vue'),
            resource_path('js/Components/DropdownLink.vue')
        );
        copy(Jetstrap::stubsInertiaResources('js/Jetstream/Input.vue'), resource_path('js/Components/Input.vue'));
        copy(Jetstrap::stubsInertiaResources(
            'js/Jetstream/InputError.vue'),
            resource_path('js/Components/InputError.vue'));
        copy(Jetstrap::stubsInertiaResources('js/Jetstream/Label.vue'), resource_path('js/Components/Label.vue'));
        copy(Jetstrap::stubsInertiaResources('js/Jetstream/NavLink.vue'), resource_path('js/Components/NavLink.vue'));
        copy(
            Jetstrap::stubsInertiaResources('js/Jetstream/ValidationErrors.vue'),
            resource_path('js/Components/ValidationErrors.vue')
        );
    }
}