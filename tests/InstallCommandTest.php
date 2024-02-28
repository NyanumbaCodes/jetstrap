<?php

namespace NascentAfrica\Jetstrap\Tests;

use NascentAfrica\Jetstrap\Console\SwapHandlers\JetstreamHandler;
use NascentAfrica\Jetstrap\Jetstrap;

class InstallCommandTest extends TestCase
{
    /** @test */
    public function package_validation()
    {
        $stacks = [Jetstrap::STACK_LIVEWIRE, Jetstrap::STACK_INERTIA];
        $stack = $stacks[rand(0, 1)];

        $this->artisan('jetstrap:swap invalid-name --stack='.$stack)
            ->expectsOutput(sprintf(
                'Invalid package name. Please specify either %s or %s.',
                Jetstrap::STACK_LIVEWIRE,
                Jetstrap::STACK_INERTIA))
            ->assertExitCode(0);
    }

    /** @test */
    public function stack_validation()
    {
        $answers = [Jetstrap::STACK_LIVEWIRE, Jetstrap::STACK_INERTIA];
        $answer = $answers[rand(0, 1)];

        $this->artisan('jetstrap:swap '.$answer)
            ->expectsChoice(
                'Which development stack would you like to use?',
                $answer,
                $answers
            )
            ->expectsOutput(sprintf(
                'Invalid package name. Please specify either %s or %s.',
                Jetstrap::STACK_LIVEWIRE,
                Jetstrap::STACK_INERTIA))
            ->assertExitCode(0);
    }

    /** @test */
    public function livewire_swapped()
    {
        // Run the make command
        $this->artisan('jetstrap:swap jetstream --stack=livewire')
            ->expectsOutput(JetstreamHandler::getStartMessage())
            ->expectsOutput(JetstreamHandler::getFinishMessage())
            ->expectsOutput(Jetstrap::RUN_NPM_MESSAGE)
            ->assertExitCode(0);

        $this->basicTests();
        $this->basicLivewireTests();
    }

    /** @test */
    public function inertia_swapped()
    {
        // Run the make command
        $this->artisan('jetstrap:swap jetstream --stack=inertia')
            ->expectsOutput(JetstreamHandler::getStartMessage())
            ->expectsOutput(JetstreamHandler::getFinishMessage())
            ->expectsOutput(Jetstrap::RUN_NPM_MESSAGE)
            ->assertExitCode(0);

        $this->basicTests();
        $this->basicInertiaTests();
    }

    /** @test */
    public function livewire_swapped_with_teams()
    {
        // Run the make command
        $this->artisan('jetstrap:swap jetstream --stack=livewire --teams')
            ->expectsOutput(JetstreamHandler::getStartMessage())
            ->expectsOutput(JetstreamHandler::getFinishMessage())
            ->expectsOutput(Jetstrap::RUN_NPM_MESSAGE)
            ->assertExitCode(0);

        $this->basicTests();
        $this->basicLivewireTests();
        $this->livewireTeamTests();
    }

    /** @test */
    public function inertia_swapped_teams()
    {
        // Run the make command
        $this->artisan('jetstrap:swap jetstream --stack=inertia --teams')
            ->expectsOutput(JetstreamHandler::getStartMessage())
            ->expectsOutput(JetstreamHandler::getFinishMessage())
            ->expectsOutput(Jetstrap::RUN_NPM_MESSAGE)
            ->assertExitCode(0);

        $this->basicTests();
        $this->basicInertiaTests();
        $this->inertiaTeamTests();
    }
}
