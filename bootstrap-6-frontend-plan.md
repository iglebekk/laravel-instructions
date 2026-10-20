# Bootstrap 6 som frontend-standard

## Mål

Laravel Instructions anbefaler Bootstrap 6 alpha for egenprodusert frontend. Blade og app-spesifikke komponenter beholdes.

## Endringer

1. Erstatt Flux UI, Tailwind og Alpine i begge kopier av instruksjonsdokumentet. Fjern Filament som anbefalt frontend-alternativ.
2. Beskriv Bootstrap-oppsett med npm og Vite, samt komponenter, grid, utilities og CSS-variabler med [Bootstrap 6-dokumentasjonen](https://getbootstrap.com/docs/6.0/getting-started/install/) som kilde.
3. Oppdater Blade-eksempler og sjekklister. Henvis AI-agenter til [maskinlesbar dokumentasjon og relevante skills](https://getbootstrap.com/docs/6.0/getting-started/ai/).
4. Oppdater README og changelog. Publiser som v5.0.0 med commit, tag og push.

## Verifisering

Kontroller at dokumentkopiene er identiske og at publisert tekst anbefaler Bootstrap 6 uten Flux, Tailwind eller Alpine. Kjør Pest, Pint, Composer-validering og `git diff --check`. Bekreft at GitHub-taggen peker på release-commiten.

Pakken publiserer instruksjoner; den installerer ikke Bootstrap eller agent-skills i app-prosjekter.
