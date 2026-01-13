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
        ->groupBy('hour')
        ->findAll();
        $result = [];
        foreach ($data as $dataRecord) {
            $result[] = $dataRecord->toArray();
        }

        $this->app->json($result);
    }
    
    // Agregate data by 3 hours span.
    // Shoutout to Stackoverflow for this:
    // https://stackoverflow.com/questions/73621451/how-to-group-datetime-into-intervals-of-3-hours-in-mysql
    public function weeklytemp(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR as hour,
            avg(tempf) as tempf,
            min(tempf) as mintempf,
            max(tempf) as maxtempf')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 604800))
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
    
    public function weeklypress(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR as hour,
            avg(baromin) as baromin,
            min(baromin) as minbaromin,
            max(baromin) as maxbaromin')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 604800))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
    
    public function dailyhumid(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            avg(humidity) as humidity,
            min(humidity) as minhumidity,
            max(humidity) as maxhumidity')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 86400))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
    
    public function weeklyhumid(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR as hour,
            avg(humidity) as humidity,
            min(humidity) as minhumidity,
            max(humidity) as maxhumidity')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 604800))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
    
    public function dailyindoortemp(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            avg(indoortempf) as indoortempf,
            min(indoortempf) as minindoortempf,
            max(indoortempf) as maxindoortempf')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 86400))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
    
    public function weeklyindoortemp(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR as hour,
            avg(indoortempf) as indoortempf,
            min(indoortempf) as minindoortempf,
            max(indoortempf) as maxindoortempf')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 604800))
            ->groupBy('hour')->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            
            $this->app->json($result);
    }
    
    public function dailyrain(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour,
            date_format(dateutc, \'%H\') as short_hour,
            max(dailyrainin) as dailyrainin')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 86400))
            ->groupBy('hour', 'short_hour')
            ->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            foreach($result as $index => &$record) {
                if ($index == 0) {
                    $record['hourlyrainmm'] = $record['dailyrainmm'];
                    continue;
                }
                if ($record['short_hour'] == '00') {
                    $record['hourlyrainmm'] = $record['dailyrainmm'];
                    continue;
                }
                $record['hourlyrainmm'] = $record['dailyrainmm'] - $result[$index - 1]['dailyrainmm'];
            }
            
            $this->app->json($result);
    }
    
    public function weeklyrain(string $station_id):void
    {
        $DataPoint = new DatapointRecord($this->db());
        $data = $DataPoint->select(
            'DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR as hour,
            date_format(DATE(dateutc) + INTERVAL (HOUR(dateutc) - MOD (HOUR(dateutc), 6)) HOUR, \'%H\') as short_hour,
            max(dailyrainin) as dailyrainin')
            ->eq('station_id', $station_id)
            ->gte('dateutc', date('Y-m-d H:i:s', time() - 604800))
            ->groupBy('hour', 'short_hour')
            ->findAll();
            $result = [];
            foreach ($data as $dataRecord) {
                $result[] = $dataRecord->toArray();
            }
            foreach($result as $index => &$record) {
                if ($index == 0) {
                    $record['hourlyrainmm'] = $record['dailyrainmm'];
                    continue;
                }
                if ($record['short_hour'] == '00') {
                    $record['hourlyrainmm'] = $record['dailyrainmm'];
                    continue;
                }
                $record['hourlyrainmm'] = $record['dailyrainmm'] - $result[$index - 1]['dailyrainmm'];
            }
            
            $this->app->json($result);
    }
}
