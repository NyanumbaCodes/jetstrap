<?php

namespace NascentAfrica\Jetstrap\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use NascentAfrica\Jetstrap\Helpers;
use NascentAfrica\Jetstrap\Jetstrap;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jetstrap:swap {stack : The development stack that should be installed}
                                              {--teams : Indicates if team support should be installed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap TailwindCss for Bootstrap 4.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->info('Performing swap...');
        $file = new Filesystem;

        // Remove Tailwind Configuration...
        if ($file->exists(base_path('tailwind.config.js'))) {
            $file->delete(base_path('tailwind.config.js'));
        }

        // Bootstrap Configuration...
        copy(jet_stubs_path('webpack.mix.js'), base_path('webpack.mix.js'));
        copy(jet_stubs_path('webpack.config.js'), base_path('webpack.config.js'));

        // Assets...
        $file->deleteDirectory(resource_path('css'));
        $file->ensureDirectoryExists(resource_path('sass'));
        $file->ensureDirectoryExists(resource_path('js'));
        $file->ensureDirectoryExists(resource_path('views'));
        $file->copyDirectory(jet_stubs_resources_path('js'), resource_path('js'));
        $file->copyDirectory(jet_stubs_resources_path('sass'), resource_path('sass'));

        copy(jet_stubs_resources_path(Jetstrap::VIEWS_WELCOME_FILE), resource_path(Jetstrap::VIEWS_WELCOME_FILE));

        Helpers::updateNodePackages(function ($packages) {
            return [
                'alpinejs' => '^3.2.6',
            ] + $packages;
        });

        switch ($this->argument('stack')) {
            case 'livewire':
                $this->swapJetstreamLivewireStack($file);
                break;
            case 'inertia':
                $this->swapJetstreamInertiaStack($file);
                break;
            case 'breeze':
                $this->swapBreezeStack($file);
                break;
            case 'breeze-inertia':
                $this->swapBreezeInertiaStack($file);
                break;
            default:
                $this->error('Stack not supported. Please use "livewire", "inertia", "breeze" or "breeze-inertia".');
                break;
        }
    }

    /**
     * Swap the Livewire stack into the application.
     *
     * @return void
     */
    protected function swapJetstreamLivewireStack(Filesystem $file)
    {
        $this->line('');
        $this->info('Installing livewire stack...');

        // Directories...
        $file->ensureDirectoryExists(resource_path('views/api'));
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_AUTH_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $file->ensureDirectoryExists(resource_path('views/profile'));

        // Layouts
        $file->copyDirectory(jet_stubs_livewire('layouts'), resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $file->copyDirectory(jet_stubs_livewire('api'), resource_path('views/api'));
        $file->copyDirectory(jet_stubs_livewire('profile'), resource_path('views/profile'));
        $file->copyDirectory(jet_stubs_livewire('auth'), resource_path(Jetstrap::VIEWS_AUTH_DIR));

        // Single Blade Views...
        copy(jet_stubs_livewire('dashboard.blade.php'), resource_path(Jetstrap::VIEWS_DASHBOARD_FILE));
        copy(jet_stubs_livewire('navigation-menu.blade.php'), resource_path('views/navigation-menu.blade.php'));
        copy(jet_stubs_livewire('terms.blade.php'), resource_path('views/terms.blade.php'));
        copy(jet_stubs_livewire('policy.blade.php'), resource_path('views/policy.blade.php'));

        // Assets...
        $file->copy(jet_stubs_resources_path(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        // Publish...
        $this->callSilent('vendor:publish', ['--tag' => 'jetstrap-views', '--force' => true]);

        // Teams...
        if ($this->option('teams')) {
            $this->swapJetstreamLivewireTeamStack($file);
        }

        $this->line('');
        $this->info('Bootstrap scaffolding swapped for livewire successfully.');
        $this->comment(Jetstrap::RUN_NPM_MESSAGE);
    }

    /**
     * Swap the Livewire team stack into the application.
     *
     * @return void
     */
    protected function swapJetstreamLivewireTeamStack(Filesystem $file)
    {
        // Directories...
        $file->ensureDirectoryExists(resource_path('views/teams'));

        $file->copyDirectory(jet_stubs_livewire('teams'), resource_path('views/teams'));
    }

    /**
     * Swap the Inertia stack into the application.
     *
     * @return void
     */
    protected function swapJetstreamInertiaStack(Filesystem $file)
    {
        $this->line('');
        $this->info('Installing inertia stack...');

        // Install NPM packages...
        Helpers::updateNodePackages(function ($packages) {
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
        copy(jet_stubs_path('inertia/webpack.mix.js'), base_path('webpack.mix.js'));

        // Blade Views...
        copy(jet_stubs_inertia_resources(Jetstrap::VIEWS_APP_FILE), resource_path(Jetstrap::VIEWS_APP_FILE));

        // Assets...
        copy(jet_stubs_inertia_resources(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_JETSTREAM_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_LAYOUT_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_API_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_AUTH_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_PROFILE_DIR));
        $file->ensureDirectoryExists(resource_path('views'));

        if (file_exists(resource_path(Jetstrap::VIEWS_WELCOME_FILE))) {
            unlink(resource_path(Jetstrap::VIEWS_WELCOME_FILE));
        }

        // Inertia Pages...
        copy(jet_stubs_inertia_resources('js/Pages/Dashboard.vue'), resource_path('js/Pages/Dashboard.vue'));
        copy(jet_stubs_inertia_resources('js/Pages/PrivacyPolicy.vue'), resource_path('js/Pages/PrivacyPolicy.vue'));
        copy(jet_stubs_inertia_resources('js/Pages/TermsOfService.vue'), resource_path('js/Pages/TermsOfService.vue'));
        copy(jet_stubs_inertia_resources('js/Pages/Welcome.vue'), resource_path('js/Pages/Welcome.vue'));

        $file->copyDirectory(
            jet_stubs_inertia_resources(Jetstrap::JS_JETSTREAM_DIR), resource_path(Jetstrap::JS_JETSTREAM_DIR)
        );
        $file->copyDirectory(
            jet_stubs_inertia_resources(Jetstrap::JS_LAYOUT_DIR), resource_path(Jetstrap::JS_LAYOUT_DIR)
        );
        $file->copyDirectory(
            jet_stubs_inertia_resources(Jetstrap::JS_PAGES_API_DIR), resource_path(Jetstrap::JS_PAGES_API_DIR)
        );
        $file->copyDirectory(
            jet_stubs_inertia_resources(Jetstrap::JS_PAGES_AUTH_DIR), resource_path(Jetstrap::JS_PAGES_AUTH_DIR)
        );
        $file->copyDirectory(
            jet_stubs_inertia_resources(Jetstrap::JS_PAGES_PROFILE_DIR), resource_path(Jetstrap::JS_PAGES_PROFILE_DIR)
        );


        // Teams...
        if ($this->option('teams')) {
            $this->swapJetstreamInertiaTeamStack($file);
        }

        $this->line('');
        $this->info('Bootstrap scaffolding swapped for inertia successfully.');
        $this->comment(Jetstrap::RUN_NPM_MESSAGE);
    }

    /**
     * Swap the Inertia team stack into the application.
     *
     * @return void
     */
    protected function swapJetstreamInertiaTeamStack(Filesystem $file)
    {
        // Directories...
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_PROFILE_DIR));

        // Pages...
        $file->copyDirectory(jet_stubs_inertia_resources('js/Pages/Teams'), resource_path('js/Pages/Teams'));
    }

    /**
     * Swap TailwindCss resources in Laravel Breeze.
     *
     * @return void
     */
    protected function swapBreezeStack(Filesystem $file)
    {
        // Views...
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_AUTH_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::VIEWS_LAYOUTS_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_COMPONENTS_DIR));

        $file->copyDirectory(
            jet_breeze_resources_path(Jetstrap::VIEWS_AUTH_DIR), resource_path(Jetstrap::VIEWS_AUTH_DIR)
        );
        $file->copyDirectory(
            jet_breeze_resources_path(Jetstrap::VIEWS_LAYOUTS_DIR), resource_path(Jetstrap::VIEWS_LAYOUTS_DIR)
        );
        $file->copyDirectory(
            jet_breeze_resources_path(Jetstrap::JS_COMPONENTS_DIR),
            resource_path(Jetstrap::JS_COMPONENTS_DIR)
        );

        copy(jet_breeze_resources_path(Jetstrap::VIEWS_DASHBOARD_FILE), resource_path(Jetstrap::VIEWS_DASHBOARD_FILE));
        copy(jet_stubs_resources_path(Jetstrap::VIEWS_WELCOME_FILE), resource_path(Jetstrap::VIEWS_WELCOME_FILE));

        $this->info('Breeze scaffolding swapped successfully.');
        $this->comment(Jetstrap::RUN_NPM_MESSAGE);
    }

    /**
     * Install the Inertia Breeze stack.
     *
     * @return void
     */
    protected function swapBreezeInertiaStack(Filesystem $file)
    {
        // NPM Packages...
        Helpers::updateNodePackages(function ($packages) {
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
        copy(jet_stubs_inertia_resources(Jetstrap::VIEWS_APP_FILE), resource_path(Jetstrap::VIEWS_APP_FILE));

        copy(jet_stubs_path('inertia/webpack.mix.js'), base_path('webpack.mix.js'));

        // Assets...
        copy(jet_stubs_inertia_resources(Jetstrap::JS_APP_FILE), resource_path(Jetstrap::JS_APP_FILE));

        // Components + Pages...
        $file->ensureDirectoryExists(resource_path('js/Components'));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_LAYOUT_DIR));
        $file->ensureDirectoryExists(resource_path(Jetstrap::JS_PAGES_DIR));

        $file->copyDirectory(
            jet_breeze_inertia_path('resources/js/Components'),
            resource_path('js/Components')
        );
        $file->copyDirectory(jet_breeze_inertia_path('resources/js/Layouts'), resource_path(Jetstrap::JS_LAYOUT_DIR));
        $file->copyDirectory(jet_breeze_inertia_path('resources/js/Pages'), resource_path(Jetstrap::JS_PAGES_DIR));

        if ($file->exists(resource_path('js/Components/ResponsiveNavLink.vue'))) {
            $file->delete(resource_path('js/Components/ResponsiveNavLink.vue'));
        }

        copy(jet_stubs_inertia_resources('js/Jetstream/Button.vue'), resource_path('js/Components/Button.vue'));
        copy(jet_stubs_inertia_resources('js/Jetstream/Checkbox.vue'), resource_path('js/Components/Checkbox.vue'));
        copy(jet_stubs_inertia_resources('js/Jetstream/Dropdown.vue'), resource_path('js/Components/Dropdown.vue'));
        copy(jet_stubs_inertia_resources(
            'js/Jetstream/DropdownLink.vue'),
            resource_path('js/Components/DropdownLink.vue')
        );
        copy(jet_stubs_inertia_resources('js/Jetstream/Input.vue'), resource_path('js/Components/Input.vue'));
        copy(jet_stubs_inertia_resources('js/Jetstream/InputError.vue'), resource_path('js/Components/InputError.vue'));
        copy(jet_stubs_inertia_resources('js/Jetstream/Label.vue'), resource_path('js/Components/Label.vue'));
        copy(jet_stubs_inertia_resources('js/Jetstream/NavLink.vue'), resource_path('js/Components/NavLink.vue'));
        copy(
            jet_stubs_inertia_resources('js/Jetstream/ValidationErrors.vue'),
            resource_path('js/Components/ValidationErrors.vue')
        );

        $this->info('Breeze scaffolding swapped successfully.');
        $this->comment(Jetstrap::RUN_NPM_MESSAGE);
    }
}
