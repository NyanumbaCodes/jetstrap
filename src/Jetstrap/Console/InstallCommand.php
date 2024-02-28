<?php declare(strict_types=1);

namespace NascentAfrica\Jetstrap\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use NascentAfrica\Jetstrap\Console\SwapHandlers\AbstractHandler;
use NascentAfrica\Jetstrap\Jetstrap;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class InstallCommand
 *
 * @package NascentAfrica\Jetstrap\Console
 */
class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'jetstrap:swap';

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
        $this->info('');

        $package = $this->argument('package');
        $stack = $this->option('stack');
        $teams = $this->option('teams');

        $this->validate($package, $stack);

        try {
            /** @var AbstractHandler $handler */
            $handler = App::make('jetstrap.'.$package . '.handler');
        } catch (\Exception $e) {
            $this->error('Unable to find a handler for the specified package.');
            return;
        }

        $this->info($handler->getStartMessage());

        $handler->setSwapTeamStack(!empty($teams))
            ->setStack($stack)
            ->initialize()
            ->swap();

        $this->info($handler->getFinishMessage());
        $this->info('Please execute the "npm install && npm run dev" command to build your assets.');
    }

    /**
     * @param string $package
     * @param string $stack
     * @return bool
     */
    protected function validate(string $package, ?string $stack): bool
    {
        $result = true;

        if (!in_array($package, ['jetstream', 'breeze'])) {
            $this->error(sprintf(
                'Invalid package name. Please specify either %s or %s.',
                Jetstrap::STACK_LIVEWIRE,
                Jetstrap::STACK_INERTIA));
            $result = false;
        }

        if (empty($stack)) {
            $stack = $this->choice(
                'Which development stack would you like to use?',
                [Jetstrap::STACK_LIVEWIRE, Jetstrap::STACK_INERTIA],
                Jetstrap::STACK_LIVEWIRE
            );

            if (empty($stack)) {
                $this->validate($package, $stack);
            }
        }

        if (!in_array($stack, [Jetstrap::STACK_LIVEWIRE, Jetstrap::STACK_INERTIA])) {
            $this->error(sprintf(
                    'Invalid stack name. Please specify either %s or %s.',
                    Jetstrap::STACK_LIVEWIRE,
                    Jetstrap::STACK_INERTIA)
            );
            $result = false;
        }

        return $result;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['package', InputArgument::REQUIRED, 'The package to swap: jetstream or breeze'],
        ];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['teams', null, InputOption::VALUE_NONE, 'Indicates if team support should be installed'],
            ['stack', null, InputOption::VALUE_OPTIONAL, 'The development stack that should be installed'],
        ];
    }
}
