<?php

return [
    'source' => null,

    'docs_target' => 'docs/laravel-instructions.md',

    'reference_targets' => [
        'AGENTS.md',
        'CLAUDE.md',
        'GEMINI.md',
    ],

    'section_header' => '## Laravel Instructions',
    'start_marker' => '<!-- LARAVEL-INSTRUCTIONS:START -->',
    'end_marker' => '<!-- LARAVEL-INSTRUCTIONS:END -->',

    'reference_body' => 'Follow the instructions in [`docs/laravel-instructions.md`](docs/laravel-instructions.md). This file is the source; do not duplicate its contents here.',
];
