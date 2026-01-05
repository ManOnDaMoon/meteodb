<?php

declare(strict_types=1);

namespace app\records;

/**
 * ActiveRecord class for the stations table.
 * @link https://docs.flightphp.com/awesome-plugins/active-record
 *
 * @property string $station_id
 * @property string $description
 * @property int $position
 * @property string $key
 * @property string $last_update
 */
class StationRecord extends \flight\ActiveRecord
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
        parent::__construct($databaseConnection, 'stations');
        $this->primaryKey = 'station_id';
    }
}
