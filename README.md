# Laravel Instructions (pakke)

Denne pakken publiserer dokumentasjon med kodestandarder og beste praksis for Laravel. Den installerer ikke Bootstrap eller agent-skills i app-prosjekter.

Den publiserte dokumentasjonen anbefaler Bootstrap 6 alpha for egenprodusert frontend, med Blade og app-spesifikke komponenter.

Støttede Laravel-versjoner: `9.x`, `10.x`, `11.x`, `12.x`, `13.x`.

## Installasjon

```bash
composer require iglebekk/laravel-instructions --dev
```

## Publiser dokumentasjon

```bash
php artisan vendor:publish --tag=laravel-instructions-docs
```

Dette legger dokumentasjonen i `docs/laravel-instructions.md` i prosjektet ditt.

## Synkronisering til instruksjonsfiler

For å sikre at siste versjon alltid er tilgjengelig i prosjektet og AI-instruksjonsfiler, kjør:

```bash
php artisan laravel-instructions:sync
```

Standard atferd:
- `docs/laravel-instructions.md` overskrives med full prinsipptekst (kilde).
- `AGENTS.md`, `CLAUDE.md`, `GEMINI.md` får kun en kort referanse mellom markørene som peker til docs-fila — for å unngå duplisert innhold.

Markører:
```
<!-- LARAVEL-INSTRUCTIONS:START -->
<!-- LARAVEL-INSTRUCTIONS:END -->
```

## Konfigurasjon

```bash
php artisan vendor:publish --tag=laravel-instructions-config
```

I `config/laravel-instructions.php` kan du justere `source`, `docs_target`, `reference_targets`, `reference_body` og markører.

## Oppdatering

Når retningslinjene oppdateres i pakken, kjør:

```bash
composer update iglebekk/laravel-instructions
php artisan laravel-instructions:sync
```

Ved overgang fra det tidligere pakkenavnet: erstatt Composer-kravet og eventuelle Composer-skript med de nye navnene over. Flytt egne config-endringer til `config/laravel-instructions.php`. Første synkronisering erstatter gamle markører i agentfilene; den gamle dokumentfilen i `docs/` må fjernes manuelt når den ikke lenger brukes.

## Utvikling

```bash
composer test     # Pest via orchestra/testbench
composer format   # Laravel Pint
```

## Automatisering via composer

Legg til i prosjektets `composer.json`:

```json
{
  "scripts": {
    "post-install-cmd": [
      "@php -r \"require_once 'vendor/autoload.php'; if (! class_exists('Iglebekk\\\\LaravelInstructions\\\\LaravelInstructionsServiceProvider')) { exit(0); } passthru(PHP_BINARY . ' artisan laravel-instructions:sync --no-interaction', $exitCode); exit($exitCode);\""
    ],
    "post-update-cmd": [
      "@php -r \"require_once 'vendor/autoload.php'; if (! class_exists('Iglebekk\\\\LaravelInstructions\\\\LaravelInstructionsServiceProvider')) { exit(0); } passthru(PHP_BINARY . ' artisan laravel-instructions:sync --no-interaction', $exitCode); exit($exitCode);\""
    ]
  }
}
```

Dette er viktig hvis pakken er installert med `--dev`. I produksjon kjøres ofte `composer install --no-dev`, og da finnes ikke kommandoen. Guard-en over avslutter derfor stille med `0` når pakken ikke er tilgjengelig.
