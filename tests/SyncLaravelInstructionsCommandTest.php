<?php

use Illuminate\Support\Facades\File;

beforeEach(function () {
    $this->workspace = sys_get_temp_dir().'/laravel-instructions-test-'.uniqid();
    File::ensureDirectoryExists($this->workspace);

    $this->app->setBasePath($this->workspace);

    config()->set('laravel-instructions.docs_target', 'docs/laravel-instructions.md');
    config()->set('laravel-instructions.reference_targets', ['AGENTS.md', 'CLAUDE.md']);
});

afterEach(function () {
    if (isset($this->workspace) && is_dir($this->workspace)) {
        File::deleteDirectory($this->workspace);
    }
});

it('writes full docs to docs_target', function () {
    $this->artisan('laravel-instructions:sync')->assertSuccessful();

    $docs = $this->workspace.'/docs/laravel-instructions.md';
    expect(File::exists($docs))->toBeTrue();
    expect(File::get($docs))
        ->not->toBeEmpty()
        ->toContain('## Arbeidsregler for AI-assistenter')
        ->toContain('### 1. Think Before Coding')
        ->toContain('## CRUDdy by Design (obligatorisk standard)')
        ->toContain('KI-agenter skal alltid bruke CRUDdy by Design')
        ->toContain('## Delegering til underagenter (obligatorisk standard)')
        ->toContain('KI-agenter skal alltid bruke enklere underagenter');
});

it('writes reference block into agent files, not full content', function () {
    $this->artisan('laravel-instructions:sync')->assertSuccessful();

    $agents = File::get($this->workspace.'/AGENTS.md');

    expect($agents)
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:START -->')
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:END -->')
        ->toContain('docs/laravel-instructions.md');
});

it('replaces existing section between markers', function () {
    $agentsPath = $this->workspace.'/AGENTS.md';
    File::put($agentsPath, <<<'MD'
    # My agent

    Existing content.

    <!-- LARAVEL-INSTRUCTIONS:START -->
    ## Old section

    Old body.
    <!-- LARAVEL-INSTRUCTIONS:END -->

    Trailing.
    MD);

    $this->artisan('laravel-instructions:sync')->assertSuccessful();

    $agents = File::get($agentsPath);

    expect($agents)
        ->toContain('# My agent')
        ->toContain('Existing content.')
        ->toContain('Trailing.')
        ->not->toContain('Old body.');
});

it('replaces a section with the previous package markers', function () {
    $agentsPath = $this->workspace.'/AGENTS.md';
    File::put($agentsPath, <<<'MD'
    # My agent

    <!-- LARAVEL-PRINSIPPER:START -->
    Old body.
    <!-- LARAVEL-PRINSIPPER:END -->

    Trailing.
    MD);

    $this->artisan('laravel-instructions:sync')->assertSuccessful();
    $this->artisan('laravel-instructions:sync')->assertSuccessful();

    expect(File::get($agentsPath))
        ->toContain('# My agent')
        ->toContain('Trailing.')
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:START -->')
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:END -->')
        ->not->toContain('LARAVEL-PRINSIPPER')
        ->not->toContain('Old body.');
    expect(substr_count(File::get($agentsPath), '<!-- LARAVEL-INSTRUCTIONS:START -->'))->toBe(1);
});

it('appends section when markers are missing', function () {
    $agentsPath = $this->workspace.'/AGENTS.md';
    File::put($agentsPath, "# Agent\n\nSome text.\n");

    $this->artisan('laravel-instructions:sync')->assertSuccessful();

    $agents = File::get($agentsPath);

    expect($agents)
        ->toStartWith('# Agent')
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:START -->')
        ->toContain('<!-- LARAVEL-INSTRUCTIONS:END -->');
});
