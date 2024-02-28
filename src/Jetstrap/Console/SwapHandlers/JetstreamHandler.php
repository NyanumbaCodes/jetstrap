<?php declare(strict_types=1);

namespace NascentAfrica\Jetstrap\Console\SwapHandlers;

use Illuminate\Support\Facades\Artisan;
use NascentAfrica\Jetstrap\Helpers;
use NascentAfrica\Jetstrap\Jetstrap;
use NascentAfrica\Jetstrap\JetstrapFacade;

/**
 * Class JetstreamHandler
 *
 * @package NascentAfrica\Jetstrap\Console\SwapHandlers
 */
class JetstreamHandler extends AbstractHandler
{
    /**
     * @inheritDoc
     */
    public function swap()
    {
        switch ($this->stack) {
            case Jetstrap::STACK_LIVEWIRE:
                $this->swapLivewireStack();
                break;
            case Jetstrap::STACK_INERTIA:
                $this->swapInertiaStack();
                break;
            default:
                throw new \InvalidArgumentException("Invalid Jetstream stack [{$this->stack}]");
        }
    }

    public static function getStartMessage(): string
    {
        return "Swapping Jetstream resources...";
    }

    public static function getFinishMessage(): string
    {
        return "Jetstream resources swapped successfully!";
    }

    /**
     * Swap the Livewire stack into the application.
     *
     * @return void
     */
    protected function swapLivewireStack()
    {
        // Directories...
        $this->getFile()->ensureDirectoryExists(resource_path('views/api'));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_AUTH_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path('views/profile'));

        // Layouts
        $this->getFile()->copyDirectory(Jetstrap::stubsLivewire('layouts'), resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $this->getFile()->copyDirectory(Jetstrap::stubsLivewire('api'), resource_path('views/api'));
        $this->getFile()->copyDirectory(Jetstrap::stubsLivewire('profile'), resource_path('views/profile'));
        $this->getFile()->copyDirectory(Jetstrap::stubsLivewire('auth'), resource_path(Jetstrap::VIEWS_AUTH_DIR));

        // Single Blade Views...
        copy(Jetstrap::stubsLivewire('dashboard.blade.php'), resource_path(Jetstrap::VIEWS_DASHBOARD_FILE));
        copy(Jetstrap::stubsLivewire('navigation-menu.blade.php'), resource_path('views/navigation-menu.blade.php'));
        copy(Jetstrap::stubsLivewire('terms.blade.php'), resource_path('views/terms.blade.php'));
        copy(Jetstrap::stubsLivewire('policy.blade.php'), resource_path('views/policy.blade.php'));

        // Assets...
        $this->getFile()->copy(Jetstrap::stubsResourcesPath(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        // Publish...
        Artisan::call('vendor:publish', ['--tag' => 'jetstrap-views', '--force' => true]);

        // Teams...
        if ($this->swapTeamStack) {
            $this->swapLivewireTeamStack();
        }
    }

    /**
     * Swap the Livewire team stack into the application.
     *
     * @return void
     */
    protected function swapLivewireTeamStack()
    {
        // Directories...
        $this->getFile()->ensureDirectoryExists(resource_path('views/teams'));

        $this->getFile()->copyDirectory(Jetstrap::stubsLivewire('teams'), resource_path('views/teams'));
    }

    /**
     * Swap the Inertia stack into the application.
     *
     * @return void
     */
    protected function swapInertiaStack()
    {
        // Install NPM packages...
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

        // Necessary for vue compilation
        copy(Jetstrap::stubsPath('inertia/webpack.mix.js'), base_path('webpack.mix.js'));

        // Blade Views...
        copy(Jetstrap::stubsInertiaResources(Jetstrap::VIEWS_APP_FILE), resource_path(Jetstrap::VIEWS_APP_FILE));

        // Assets...
        copy(Jetstrap::stubsInertiaResources(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_JETSTREAM_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_LAYOUT_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_API_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_AUTH_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_PROFILE_DIR));
        $this->getFile()->ensureDirectoryExists(resource_path('views'));

        if (file_exists(resource_path(Jetstrap::VIEWS_WELCOME_FILE))) {
            unlink(resource_path(Jetstrap::VIEWS_WELCOME_FILE));
        }

        // Inertia Pages...
        copy(Jetstrap::stubsInertiaResources('js/Pages/Dashboard.vue'), resource_path('js/Pages/Dashboard.vue'));
        copy(Jetstrap::stubsInertiaResources(
            'js/Pages/PrivacyPolicy.vue'),
            resource_path('js/Pages/PrivacyPolicy.vue'));
        copy(Jetstrap::stubsInertiaResources(
            'js/Pages/TermsOfService.vue'),
            resource_path('js/Pages/TermsOfService.vue'));
        copy(Jetstrap::stubsInertiaResources('js/Pages/Welcome.vue'), resource_path('js/Pages/Welcome.vue'));

        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources(Jetstrap::JS_JETSTREAM_DIR), resource_path(Jetstrap::JS_JETSTREAM_DIR)
        );
        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources(Jetstrap::JS_LAYOUT_DIR), resource_path(Jetstrap::JS_LAYOUT_DIR)
        );
        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources(Jetstrap::JS_PAGES_API_DIR), resource_path(Jetstrap::JS_PAGES_API_DIR)
        );
        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources(Jetstrap::JS_PAGES_AUTH_DIR), resource_path(Jetstrap::JS_PAGES_AUTH_DIR)
        );
        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources(
                Jetstrap::JS_PAGES_PROFILE_DIR),
            resource_path(Jetstrap::JS_PAGES_PROFILE_DIR)
        );

        // Teams...
        if ($this->swapTeamStack) {
            $this->swapInertiaTeamStack();
        }
    }

    /**
     * Swap the Inertia team stack into the application.
     *
     * @return void
     */
    protected function swapInertiaTeamStack()
    {
        // Directories...
        $this->getFile()->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_PROFILE_DIR));

        // Pages...
        $this->getFile()->copyDirectory(
            Jetstrap::stubsInertiaResources('js/Pages/Teams'),
            resource_path('js/Pages/Teams'));
    }
}