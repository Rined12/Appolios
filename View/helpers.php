<?php

declare(strict_types=1);

function difficulty_label_fr(string $code): string
{
    $map = [
        'beginner' => 'Débutant',
        'intermediate' => 'Intermédiaire',
        'advanced' => 'Avancé',
    ];
    return $map[$code] ?? $code;
}
