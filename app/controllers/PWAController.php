<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\StationRecord;
use flight\Engine;

class PWAController extends BaseController
{
    /**
     * Progressive Web App manifest JSON file
     *
     * @return void
     */
    public function pwa(): void
    {        
        $manifest = [
            "name" => $this->app->get('meteodb.pwa.app_name'),
            "short_name" => $this->app->get('meteodb.pwa.app_short_name'),
            "display" => "standalone",
            "scope" => "/",
            "start_url" => "/"
        ];
        
        $this->app->json($manifest);
    }
    
    public function stationpwa(string $station_id): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $StationRecord->with('currentDataPoint')->find($station_id);
        
        if ($StationRecord->isHydrated()){
            $manifest = [
                "name" => $StationRecord->description . ' - ' . $this->app->get('meteodb.pwa.app_name'),
                "short_name" => $StationRecord->description . ' - ' . $this->app->get('meteodb.pwa.app_short_name'),
                "display" => "standalone",
                "scope" => "/",
                "start_url" => $this->app->getUrl('station', [ 'station_id' => $station_id])
            ];
            
            $this->app->json($manifest);
        }
        
        $this->app->response()->status(404);
    }
}
