<?php

declare(strict_types=1);

namespace app\controllers;

use app\records\StationRecord;
use app\records\DatapointRecord;
use Tracy\Debugger;

class StationController extends BaseController
{
    /**
     * Index
     *
     * @return void
     */
    public function index(): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $stations = $StationRecord->order('description ASC')->findAll();
       
        foreach($stations as &$station) {
            $DataPointRecord = new DatapointRecord($this->app->db());
            $DataPointRecord->equal('station_id', $station->station_id)->order('dateutc DESC')->find();
            if ($DataPointRecord->isHydrated()) {
                $station->currentDataPoint = $DataPointRecord;
            } else {
                $station->currentDataPoint = null;
            }
        }
        
        $this->app->render('station/index.latte', [ 'page_title' => 'Stations', 'stations' => $stations]);
    }
    
    /**
     * Create
     *
     * @return void
     */
    public function create(): void
    {
        $this->app->render('station/create.latte', [ 'page_title' => 'Ajouter une station']);
    }
    
    
    /**
     * Store
     *
     * @return void
     */
    public function store(): void
    {
        $stationData = $this->app->request()->data;
        $StationRecord = new StationRecord($this->app->db());
        // TODO : Some verification
        $StationRecord->station_id = $stationData->station_id;
        $StationRecord->description = $stationData->description;
        $StationRecord->key = password_hash($stationData->key, PASSWORD_DEFAULT);
        $StationRecord->last_update = null;
        $StationRecord->save();
        $this->app->redirect('/station');
    }
    
    /**
     * Show
     *
     * @param string $station_id The ID of the station
     * @return void
     */
    public function show(string $station_id): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $station = $StationRecord->find($station_id);
        $DatapointRecord = new DatapointRecord($this->app->db());
        $DatapointRecord->eq('station_id', $station->station_id)->order('dateutc DESC')->find();
        if ($DatapointRecord->isHydrated()) {
            $station->currentDataPoint = $DatapointRecord;
        } else {
            $station->currentDataPoint = null;
        }
        $this->app->render('station/show.latte', [ 'page_title' => $station->description, 'station' => $station ]);
    }
    
    /**
     * Edit
     *
     * @param string $station_id The ID of the station
     * @return void
     */
    public function edit(string $station_id): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $station = $StationRecord->find($station_id);
        $this->app->render('station/edit.latte', [ 'page_title' => 'Modifier la station', 'station' => $station]);
    }
    
    /**
     * Update
     *
     * @param string $station_id The ID of the station
     * @return void
     */
    public function update(string $station_id): void
    {
        $stationData = $this->app->request()->data;
        $StationRecord = new StationRecord($this->app->db());
        $StationRecord->find($station_id);
        $StationRecord->description = $stationData->description;
        if ($stationData->key) {
            $StationRecord->key = password_hash($stationData->key, PASSWORD_DEFAULT);
        }
        $StationRecord->save();
        $this->app->redirect('/station');
    }
    
    /**
     * Destroy
     *
     * @param string $statino_id The ID of the station
     * @return void
     */
    public function destroy(string $station_id): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $station = $StationRecord->find($station_id);
        $station->delete();
        $this->app->redirect('/station');
    }
    
    
    /**
     * Update weatherstationdata
     * 
     * @return void
     */
    public function updateweatherstation():void
    {
        $query= $this->request()->query;

        $DataPoint = new DatapointRecord($this->db());
        
        $DataPoint->station_id = $query->ID;
        $DataPoint->action = $query->action;
        $DataPoint->realtime = $query->realtime;
        $DataPoint->rtfreq = $query->rtfreq;
        $DataPoint->dateutc = date('Y-m-d H:i:s', $query->dateutc == 'now' ? $this->request()->getVar('REQUEST_TIME') : intval($query->dateutc));
        $DataPoint->baromin = $query->baromin;
        $DataPoint->tempf = $query->tempf;
        $DataPoint->dewptf = $query->dewptf;
        $DataPoint->humidity = $query->humidity;
        $DataPoint->windspeedmph = $query->windspeedmph;
        $DataPoint->windgustmph = $query->windgustmph;
        $DataPoint->winddir = $query->winddir;
        $DataPoint->rainin = $query->rainin;
        $DataPoint->dailyrainin = $query->dailyrainin;
        $DataPoint->solarradiation = $query->solarradiation;
        $DataPoint->UV = $query->UV;
        $DataPoint->indoortempf = $query->indoortempf;
        $DataPoint->indoorhumidity = $query->indoorhumidity;
        
        $DataPoint->save();
    }
    
    /**
     * Show evolution graphs
     *
     * @return void
     */
    public function evolution(string $station_id):void
    {
        $StationRecord = new StationRecord($this->app->db());
        $StationRecord->find($station_id);
        
        if ($StationRecord->isHydrated()){
                    
            // Weekley evol
            $DataPoint = new DatapointRecord($this->db());
            // TODO : limit to current week
            $data_week = $DataPoint->select('date_format(dateutc, \'%Y-%m-%d\') as day, avg(tempf) as tempf')->eq('station_id', $station_id)->groupBy('day')->findAll();
            
            // TODO : limit to current day
            $data_day = $DataPoint->select('date_format(dateutc, \'%Y-%m-%d %H:00:00\') as hour, avg(tempf) as tempf')->eq('station_id', $station_id)->groupBy('hour')->findAll();
            
            // TODO : limit to current year
            $data_year = $DataPoint->select('date_format(dateutc, \'%Y-%m-1\') as month, avg(tempf) as tempf')->eq('station_id', $station_id)->groupBy('month')->findAll();
            
            
            $this->app->render('station/evolution.latte', [
                'page_title' => 'Evolution',
                'station' => $StationRecord,
                'data_year' => $data_year,
                'data_week' => $data_week,
                'data_day' => $data_day
            ]);
        }
    }
}
