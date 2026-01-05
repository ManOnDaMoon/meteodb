<?php

declare(strict_types=1);

namespace app\controllers;

use flight\Engine;
use app\records\StationRecord;
use app\records\DatapointRecord;

class StationController
{
    /** @var Engine */
    protected Engine $app;

    /**
     * Constructor
     */
    public function __construct(Engine $app)
    {
        $this->app = $app;
    }
    
    /**
     * Index
     *
     * @return void
     */
    public function index(): void
    {
        $StationRecord = new StationRecord($this->app->db());
        $stations = $StationRecord->order('station_id ASC')->findAll();
       
        foreach($stations as &$station) {
            $DataPointRecord = new DatapointRecord($this->app->db());
            $station->currentDataPoint = $DataPointRecord->equal('station_id', $station->station_id)->order('dateutc DESC')->find();
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
        $station->currentDataPoint = $DatapointRecord->eq('station_id', $station->station_id)->order('dateutc DESC')->find();
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
        // CODE TO UPDATE DataPoints
        
        // MAYBE MOVE TO DATAPOINTS CONTROLLER ?
        
    }
    
}
