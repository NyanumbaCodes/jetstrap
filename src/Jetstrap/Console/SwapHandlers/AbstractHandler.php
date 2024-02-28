<?php declare(strict_types=1);

namespace NascentAfrica\Jetstrap\Console\SwapHandlers;

use Illuminate\Filesystem\Filesystem;
use NascentAfrica\Jetstrap\Jetstrap;
use NascentAfrica\Jetstrap\JetstrapFacade;

/**
 * Class AbstractHandler
 *
 * @package NascentAfrica\Jetstrap\Console\SwapHandlers
 */
abstract class AbstractHandler
{
    protected ?string $stack = null;

    protected bool $swapTeamStack = false;

    protected Filesystem $files;

    /**
     * @param Filesystem $file
     */
    public function __construct(Filesystem $file)
    {
        $this->files = $file;
    }

    /**
     * @return string
     */
    abstract public static function getStartMessage(): string;

    /**
     * @return string
     */
    abstract public static function getFinishMessage(): string;

    /**
     * @return Filesystem
     */
    public function getFile(): Filesystem
    {
        return $this->files;
    }

    /**
     * @param string $stack
     * @return AbstractHandler
     */
    public function setStack(string $stack): AbstractHandler
    {
        $this->stack = $stack;

        return $this;
    }

    /**
     * @param bool $swapTeamStack
     * @return $this
     */
    public function setSwapTeamStack(bool $swapTeamStack): AbstractHandler
    {
        $this->swapTeamStack = $swapTeamStack;

        return $this;
    }

    /**
     * Swap the Jetstream scaffolding for the application.
     *
     * @return void
     */
    abstract public function swap();

    /**
     * Initialize the Jetstream swap for the application.
     *
     * @return AbstractHandler
     */
    public function initialize(): AbstractHandler
    {
        if (empty($this->stack)) {
            throw new \InvalidArgumentException('Jetstream stack must be set before initializing.');
        }

        $this->replaceTailwindConfigFile();
        $this->prepareDirectories();

        copy(Jetstrap::jetstreamResourcesPath(
            Jetstrap::VIEWS_WELCOME_FILE),
            resource_path(Jetstrap::VIEWS_WELCOME_FILE));

        $this->updateNodeDependencies();

        return $this;
    }

    /**
     * Update the "package.json" file to include the latest packages.
     *
     * @return void
     */
    protected function updateNodeDependencies()
    {
        JetstrapFacade::updateNodePackages(function ($packages) {
            return [
                    'alpinejs' => '^3.2.6',
                ] + $packages;
        });
    }

    /**
     * Replace the Tailwind configuration file with webpack.mix.js and webpack.config.js
     *
     * @return void
     */
    protected function replaceTailwindConfigFile()
    {
        if ($this->getFile()->exists(base_path('tailwind.config.js'))) {
            $this->getFile()->delete(base_path('tailwind.config.js'));
        }

        // Bootstrap Configuration...
        copy(Jetstrap::stubsPath('webpack.mix.js'), base_path('webpack.mix.js'));
        copy(Jetstrap::stubsPath('webpack.config.js'), base_path('webpack.config.js'));
    }

    /**
     * Ensure all relevant directories exist.
     *
     * @return void
     */
    protected function prepareDirectories()
    {
        // Assets...
        $this->getFile()->deleteDirectory(resource_path('css'));
        $this->getFile()->ensureDirectoryExists(resource_path('sass'));
        $this->getFile()->ensureDirectoryExists(resource_path('js'));
        $this->getFile()->ensureDirectoryExists(resource_path('views'));
        $this->getFile()->copyDirectory(Jetstrap::jetstreamResourcesPath('js'), resource_path('js'));
        $this->getFile()->copyDirectory(Jetstrap::jetstreamResourcesPath('sass'), resource_path('sass'));
    }
}