<?php

declare(strict_types=1);

namespace app\services;

/** Estimates a short-term outlook from the three-hour pressure tendency. */
class PressureTrend
{
    private const PERIOD_SECONDS = 10800;
    private const REFERENCE_TOLERANCE_SECONDS = 300;
    private const MAX_CURRENT_AGE_SECONDS = 600;

    public static function forCurrent(object $db, string $stationId, object $point): array
    {
        $currentPressure = $point->barohpa;
        // dateutc is a local wall-clock value in the PHP-configured timezone.
        $currentTime = strtotime((string) $point->dateutc);
        $currentAge = $currentTime === false ? null : time() - $currentTime;
        if ($currentPressure === null || !is_numeric($currentPressure) || $currentAge === null
            || $currentAge > self::MAX_CURRENT_AGE_SECONDS || $currentAge < -300) {
            return self::unavailable();
        }

        $referenceTime = $currentTime - self::PERIOD_SECONDS;
        $start = date('Y-m-d H:i:s', $referenceTime - self::REFERENCE_TOLERANCE_SECONDS);
        $end = date('Y-m-d H:i:s', $referenceTime + self::REFERENCE_TOLERANCE_SECONDS);
        $row = $db->fetchRow(
            'SELECT AVG(baromin) AS average_baromin, COUNT(*) AS sample_count
             FROM datapoints
             WHERE station_id = ? AND dateutc BETWEEN ? AND ? AND baromin IS NOT NULL',
            [$stationId, $start, $end]
        );

        $averageBaromin = $row['average_baromin'] ?? null;
        $sampleCount = (int) ($row['sample_count'] ?? 0);
        if ($sampleCount < 1 || !is_numeric($averageBaromin)) {
            return self::unavailable();
        }

        $referencePressure = (float) $averageBaromin * 33.8639;
        $change = round((float) $currentPressure - $referencePressure, 1);
        $classification = self::classify($change);
        $conditions = [
            'strong_rise' => ['rising', 'sun', 'Soleil estimé'],
            'moderate_rise' => ['rising', 'partly_cloudy', 'Éclaircies estimées'],
            'steady' => ['steady', 'cloud', 'Temps stable estimé'],
            'moderate_fall' => ['falling', 'rain', 'Pluie possible'],
            'strong_fall' => ['falling', 'storm', 'Temps perturbé possible'],
        ];
        [$direction, $condition, $label] = $conditions[$classification];

        return [
            'period_hours' => 3,
            'change_hpa' => $change,
            'classification' => $classification,
            'direction' => $direction,
            'condition' => $condition,
            'label' => $label,
            'reference_at' => gmdate('Y-m-d\\TH:i:s\\Z', $referenceTime),
        ];
    }

    public static function classify(float $changeHpa): string
    {
        if ($changeHpa > 3.5) {
            return 'strong_rise';
        }
        if ($changeHpa >= 1.6) {
            return 'moderate_rise';
        }
        if ($changeHpa >= -1.5) {
            return 'steady';
        }
        if ($changeHpa >= -3.5) {
            return 'moderate_fall';
        }
        return 'strong_fall';
    }

    private static function unavailable(): array
    {
        return [
            'period_hours' => 3,
            'change_hpa' => null,
            'classification' => 'unavailable',
            'direction' => 'unavailable',
            'condition' => 'unavailable',
            'label' => 'Tendance indisponible',
            'reference_at' => null,
        ];
    }
}
