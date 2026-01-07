<?php

declare(strict_types=1);

namespace app\middlewares;

use flight\Engine;
use app\records\StationRecord;
use Tracy\Debugger;

class WeatherstationUpdateMiddleware {

    /** @var Engine */
    protected Engine $app;

    public function __construct(Engine $app)
    {
        $this->app = $app;
    }

    public function before(): void
    {   
        // Check station key
        $query= $this->app->request()->query;
        $StationRecord = new StationRecord($this->app->db());
        $StationRecord->find($query->ID);
        if (!($StationRecord->isHydrated() && $query->PASSWORD && password_verify($query->PASSWORD, $StationRecord->key))) {
            Debugger::dump($query->PASSWORD);
            Debugger::dump($StationRecord->key);
            $this->app->halt('405','Unauthorized');
        }
        
        // Minimum interval of 60s between updates - TODO: Make this a parameter?
        if ($StationRecord->last_update && (time() - strtotime($StationRecord->last_update) < 60)) {
            $this->app->halt('400', 'Respect minimum update interval');
        }
        
        // Check data
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