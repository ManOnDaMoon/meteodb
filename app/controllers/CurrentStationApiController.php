<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\StationRecord;

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

        $this->app->json([
            'station' => ['id' => (string) $station->station_id, 'name' => (string) $station->description],
            'updated_at' => gmdate('Y-m-d\TH:i:s\Z', strtotime($point->dateutc . ' UTC')),
            'measurements' => [
                'temperature_c' => isset($point->tempc) ? (float) $point->tempc : null,
                'indoor_temperature_c' => isset($point->indoortempc) ? (float) $point->indoortempc : null,
                'humidity_pct' => isset($point->humidity) ? (float) $point->humidity : null,
                'indoor_humidity_pct' => isset($point->indoorhumidity) ? (float) $point->indoorhumidity : null,
                'pressure_hpa' => isset($point->barohpa) ? (float) $point->barohpa : null,
                'dew_point_c' => isset($point->dewptc) ? (float) $point->dewptc : null,
                'daily_rain_mm' => isset($point->dailyrainmm) ? (float) $point->dailyrainmm : null,
                'rain_rate_mm_h' => isset($point->rainmm) ? (float) $point->rainmm : null,
                'solar_radiation_w_m2' => isset($point->solarradiation) ? (float) $point->solarradiation : null,
                'uv_index' => isset($point->UV) ? (float) $point->UV : null,
                'wind_speed_kmh' => isset($point->windspeedkmh) ? (float) $point->windspeedkmh : null,
                'wind_gust_kmh' => isset($point->windgustkmh) ? (float) $point->windgustkmh : null,
                'wind_direction_degrees' => isset($point->winddir) ? (float) $point->winddir : null,
                'wind_direction_cardinal' => $point->windcardinal ?? null,
            ],
        ]);
    }
}
