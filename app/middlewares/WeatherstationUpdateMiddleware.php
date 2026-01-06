<?php

declare(strict_types=1);

namespace app\middlewares;

use flight\Engine;
use app\records\StationRecord;

class WeatherstationUpdateMiddleware {

    /** @var Engine */
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(): void
    {   
        $query= $this->app->request()->query;
        $StationRecord = new StationRecord($this->app->db());
        $station = $StationRecord->find($query->ID);
        if (!($station->isHydrated() && $query->PASSWORD && password_verify($query->PASSWORD, $station->key))) {
            $this->app->halt('405','Unauthorized');
        }
        
        if (!isset($query->ID, $query->action, $query->realtime,
                $query->rtfreq, $query->dateutc, $query->baromin,
                $query->tempf, $query->dewptf, $query->humidity,
                $query->windspeedmph, $query->windgustmph, $query->winddir,
                $query->rainin, $query->dailyrainin, $query->solarradiation,
                $query->UV, $query->indoortempf, $query->indoorhumidity)) {
            $this->app->halt('400', 'Bad request');   
        }
    }
}