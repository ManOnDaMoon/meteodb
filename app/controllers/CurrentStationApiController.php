<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\StationRecord;
use app\services\PressureTrend;

class CurrentStationApiController extends BaseController
{
    public function show(string $station_id): void
    {
        $stationRecord = new StationRecord($this->db());
        $station = $stationRecord->with('currentDataPoint')->find($station_id);

        if (!$station->isHydrated()) {
            $this->app->json(['error' => ['code' => 'station_not_found', 'message' => 'Station not found.']], 404);
            return;
        }

        $point = $station->currentDataPoint;
        if (!$point || !$point->isHydrated()) {
            $this->app->json(['error' => ['code' => 'measurement_not_found', 'message' => 'Station has no current measurements.']], 404);
            return;
        }

        $numeric = static function (string $field) use ($point): ?float {
            $value = $point->{$field};
            return $value === null ? null : (float) $value;
        };

        $text = static function (string $field) use ($point): ?string {
            $value = $point->{$field};
            return $value === null ? null : (string) $value;
        };

        $this->app->json([
            'station' => ['id' => (string) $station->station_id, 'name' => (string) $station->description],
            'updated_at' => gmdate('Y-m-d\TH:i:s\Z', strtotime($point->dateutc . ' UTC')),
            'pressure_trend' => PressureTrend::forCurrent($this->db(), $station_id, $point),
            'measurements' => [
                'temperature_c' => $numeric('tempc'),
                'indoor_temperature_c' => $numeric('indoortempc'),
                'humidity_pct' => $numeric('humidity'),
                'indoor_humidity_pct' => $numeric('indoorhumidity'),
                'pressure_hpa' => $numeric('barohpa'),
                'dew_point_c' => $numeric('dewptc'),
                'daily_rain_mm' => $numeric('dailyrainmm'),
                'rain_rate_mm_h' => $numeric('rainmm'),
                'solar_radiation_w_m2' => $numeric('solarradiation'),
                'uv_index' => $numeric('UV'),
                'wind_speed_kmh' => $numeric('windspeedkmh'),
                'wind_gust_kmh' => $numeric('windgustkmh'),
                'wind_direction_degrees' => $numeric('winddir'),
                'wind_direction_cardinal' => $text('windcardinal'),
            ],
        ]);
    }
}
