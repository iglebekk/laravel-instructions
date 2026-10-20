<?php

namespace Iglebekk\LaravelInstructions\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;

class SyncLaravelInstructionsCommand extends Command
{
    protected $signature = 'laravel-instructions:sync';

    protected $description = 'Sync Laravel instructions docs and insert short references into AI instruction files';

    public function __construct(private readonly Filesystem $files)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $config = config('laravel-instructions', []);
        $sourcePath = $this->resolveSourcePath($config['source'] ?? null);

        if (! $this->files->exists($sourcePath)) {
            $this->error("Source file not found: {$sourcePath}");

            return self::FAILURE;
        }

        $source = rtrim($this->files->get($sourcePath));
        $sectionHeader = rtrim($config['section_header'] ?? '## Laravel Instructions', "\n");
        $startMarker = $config['start_marker'] ?? '<!-- LARAVEL-INSTRUCTIONS:START -->';
        $endMarker = $config['end_marker'] ?? '<!-- LARAVEL-INSTRUCTIONS:END -->';

        $docsTarget = $config['docs_target'] ?? 'docs/laravel-instructions.md';
        if (is_string($docsTarget) && $docsTarget !== '') {
            $this->writeDocs($this->resolvePath($docsTarget), $source);
        }

        $referenceBody = trim($config['reference_body'] ?? '');
        if ($referenceBody === '') {
            $referenceBody = 'Follow the instructions in `docs/laravel-instructions.md`.';
        }

        foreach ($this->resolveTargets($config['reference_targets'] ?? []) as $targetPath) {
            $this->syncReference($targetPath, $referenceBody, $sectionHeader, $startMarker, $endMarker);
        }

        $this->info('Laravel instructions synced.');

        return self::SUCCESS;
    }

    private function writeDocs(string $targetPath, string $source): void
    {
        $this->ensureDirectory($targetPath);
        $this->files->put($targetPath, $source."\n");
        $this->line("Updated {$targetPath}");
    }

    private function syncReference(
        string $targetPath,
        string $referenceBody,
        string $sectionHeader,
        string $startMarker,
        string $endMarker,
    ): void {
        $this->ensureDirectory($targetPath);

        $existing = $this->files->exists($targetPath)
            ? $this->files->get($targetPath)
            : '';

        $section = $sectionHeader."\n\n".$referenceBody."\n";
        $updated = $this->replaceSection($existing, $section, $startMarker, $endMarker);

        $this->files->put($targetPath, $updated);
        $this->line("Updated {$targetPath}");
    }

    private function replaceSection(string $existing, string $section, string $startMarker, string $endMarker): string
    {
        $startPos = strpos($existing, $startMarker);
        $endPos = strpos($existing, $endMarker);

        if ($startPos === false || $endPos === false) {
            $startPos = strpos($existing, '<!-- LARAVEL-PRINSIPPER:START -->');
            $endPos = strpos($existing, '<!-- LARAVEL-PRINSIPPER:END -->');
            $existingEndMarker = '<!-- LARAVEL-PRINSIPPER:END -->';
        } else {
            $existingEndMarker = $endMarker;
        }

        $wrappedSection = $startMarker."\n".$section.$endMarker;

        if ($startPos === false || $endPos === false || $endPos < $startPos) {
            $trimmed = rtrim($existing);
            if ($trimmed !== '') {
                $trimmed .= "\n\n";
            }

            return $trimmed.$wrappedSection."\n";
        }

        $before = substr($existing, 0, $startPos);
        $after = substr($existing, $endPos + strlen($existingEndMarker));

        return rtrim($before)."\n\n".$wrappedSection."\n".ltrim($after);
    }

    private function resolveSourcePath(?string $source): string
    {
        if (is_string($source) && $source !== '') {
            return $this->resolvePath($source);
        }

        return realpath(__DIR__.'/../../resources/docs/laravel-instructions.md') ?: __DIR__.'/../../resources/docs/laravel-instructions.md';
    }

    /**
     * @param  array<int, string>  $targets
     * @return array<int, string>
     */
    private function resolveTargets(array $targets): array
    {
        $resolved = [];
        foreach ($targets as $target) {
            if (! is_string($target) || $target === '') {
                continue;
            }
            $resolved[] = $this->resolvePath($target);
        }

        return $resolved;
    }

    private function resolvePath(string $path): string
    {
        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        return base_path($path);
    }

    private function isAbsolutePath(string $path): bool
    {
        if (Str::startsWith($path, DIRECTORY_SEPARATOR)) {
            return true;
        }

        return (bool) preg_match('/^[A-Za-z]:\\\\/', $path);
    }

    private function ensureDirectory(string $targetPath): void
    {
        $directory = dirname($targetPath);
        if (! $this->files->isDirectory($directory)) {
            $this->files->ensureDirectoryExists($directory);
        }
    }
}
