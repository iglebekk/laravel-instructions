<?php

namespace Iglebekk\LaravelInstructions;

use Iglebekk\LaravelInstructions\Console\SyncLaravelInstructionsCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelInstructionsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-instructions')
            ->hasConfigFile('laravel-instructions')
            ->hasCommand(SyncLaravelInstructionsCommand::class);
    }

    public function packageBooted(): void
    {
        $this->publishes([
            __DIR__.'/../resources/docs/laravel-instructions.md' => base_path('docs/laravel-instructions.md'),
        ], 'laravel-instructions-docs');
    }
}
