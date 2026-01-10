<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\DatapointRecord;
use Tracy\Debugger;

class DataPointsController extends BaseController
{
    public function dailytemp(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            avg(tempf) as tempf,
            min(tempf) as mintempf,
            max(tempf) as maxtempf')
        ->eq('station_id', $station_id)
        ->gte('dateutc', date('Y-m-d H:i:s', time() - 86400))
        ->groupBy('hour')->findAll();
        $result = [];
        foreach ($data as $dataRecord) {
            $result[] = $dataRecord->toArray();
        }

        $this->app->json($result);
    }
    
    public function dailypress(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            avg(baromin) as baromin,
            min(baromin) as minbaromin,
            max(baromin) as maxbaromin')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 86400))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
}
