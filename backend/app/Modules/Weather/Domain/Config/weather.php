<?php

declare(strict_types=1);

return [
    'open_meteo' => [
        'url' => 'https://api.open-meteo.com/v1/forecast?latitude=50.25841&longitude=19.02754&hourly=temperature_2m&timezone=Europe%2FWarsaw&forecast_days=1',
        'timeout_seconds' => 3,
        // Must match the timezone parameter in the URL: Open-Meteo returns hourly times in it.
        'timezone' => 'Europe/Warsaw',
    ],
];
