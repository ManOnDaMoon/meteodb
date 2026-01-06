<?php

declare(strict_types=1);

namespace app\records;

/**
 * ActiveRecord class for the users table.
 * @link https://docs.flightphp.com/awesome-plugins/active-record
 *
 * @property int $id
 * @property string $username
 * @property string $password
 */
class UserRecord extends \flight\ActiveRecord
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
        parent::__construct($databaseConnection, 'users');
    }
    
    protected function beforeDelete(self $self) {
        if ($this->id = 1) {
            // Do not delete admin user
            $this->app->halt('405', 'Do not delete the admin account');
        }
    } 
}
