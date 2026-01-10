<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\DatapointRecord;
use Tracy\Debugger;

class DataPointsController extends BaseController
{
    public function daily(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        // TODO : limit to current day
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            avg(tempf) as tempf,
            avg(humidity) as humidity')
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
