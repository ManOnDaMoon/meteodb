<?php

declare(strict_types=1);

namespace app\records;

/**
 * ActiveRecord class for the datapoints table.
 * @link https://docs.flightphp.com/awesome-plugins/active-record
 *
 * @property int $id
 * @property string $station_id
 * @property string $action
 * @property int $realtime
 * @property int $rtfreq
 * @property string $dateutc
 * @property float $baromin
 * @property float $tempf
 * @property float $dewptf
 * @property int $humidity
 * @property float $windspeedmph
 * @property float $windgustmph
 * @property int $winddir
 * @property float $rainin
 * @property float $dailyrainin
 * @property float $solarradiation
 * @property float $UV
 * @property float $indoortempf
 * @property int $indoorhumidity
 */
class DatapointRecord extends \flight\ActiveRecord
{
    /**
     * @var array $relations Set the relationships for the model
     *   https://docs.flightphp.com/awesome-plugins/active-record#relationships
     */
    protected array $relations = [];

    /**
     * Constructor
     * @param mixed $databaseConnection The connection to the database
     */
    public function __construct($databaseConnection)
    {
        parent::__construct($databaseConnection, 'datapoints');
    }
}
