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
    
    private function RoundIt($ee){
        return round($ee, 2);
    }
    
    private function toKM( $a) {
        return  $this->RoundIt( floatval($a)*1.60934);
    }
    
    private function toC( $a) {
        return $this->RoundIt(  (floatval($a)-32) * (5/9) );
    }
    
    private function toMM( $a) {
        return $this->RoundIt( floatval($a)*25.4);
    }
    
    private function toHPA( $a) {
        return $this->RoundIt((floatval($a)*33.8639));
    }
    
    private function wind_cardinal( $degree ) {
        switch( $degree ) {
            case ( $degree >= 348.75 && $degree <= 360 ):
                $cardinal = "N";
                break;
            case ( $degree >= 0 && $degree <= 11.249 ):
                $cardinal = "N";
                break;
            case ( $degree >= 11.25 && $degree <= 33.749 ):
                $cardinal = "NNE";
                break;
            case ( $degree >= 33.75 && $degree <= 56.249 ):
                $cardinal = "NE";
                break;
            case ( $degree >= 56.25 && $degree <= 78.749 ):
                $cardinal = "ENE";
                break;
            case ( $degree >= 78.75 && $degree <= 101.249 ):
                $cardinal = "E";
                break;
            case ( $degree >= 101.25 && $degree <= 123.749 ):
                $cardinal = "ESE";
                break;
            case ( $degree >= 123.75 && $degree <= 146.249 ):
                $cardinal = "SE";
                break;
            case ( $degree >= 146.25 && $degree <= 168.749 ):
                $cardinal = "SSE";
                break;
            case ( $degree >= 168.75 && $degree <= 191.249 ):
                $cardinal = "S";
                break;
            case ( $degree >= 191.25 && $degree <= 213.749 ):
                $cardinal = "SSO";
                break;
            case ( $degree >= 213.75 && $degree <= 236.249 ):
                $cardinal = "SO";
                break;
            case ( $degree >= 236.25 && $degree <= 258.749 ):
                $cardinal = "OSO";
                break;
            case ( $degree >= 258.75 && $degree <= 281.249 ):
                $cardinal = "O";
                break;
            case ( $degree >= 281.25 && $degree <= 303.749 ):
                $cardinal = "ONO";
                break;
            case ( $degree >= 303.75 && $degree <= 326.249 ):
                $cardinal = "NO";
                break;
            case ( $degree >= 326.25 && $degree <= 348.749 ):
                $cardinal = "NNO";
                break;
            default:
                $cardinal = null;
        }
        return $cardinal;
    }
    
    
    protected function afterFind(self $self)
    {
        $this->barohpa = $this->toHPA($self->baromin);
        $this->tempc = $this->toC($this->tempf);
        $this->dewptc = $this->toC($this->dewptf);
        $this->windspeedkmh = $this->toKM($this->windspeedmph);
        $this->windgustkmh = $this->toKM($this->windgustmph);
        $this->windcardinal = $this->wind_cardinal($this->winddir);
        $this->rainmm = $this->toMM($this->rainin);
        $this->dailyrainmm = $this->toMM($this->dailyrainin);
        $this->indoortempc = $this->toC($this->indoortempf);
    }
}
