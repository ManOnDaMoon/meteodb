<?php

declare(strict_types=1);

namespace app\records;

/**
 * ActiveRecord class for the auth_tokens table.
 * @link https://docs.flightphp.com/awesome-plugins/active-record
 *
 * @property int $id
 * @property string $selector
 * @property string $token
 * @property int $userid
 * @property string $expires
 */
class AuthTokenRecord extends \flight\ActiveRecord
{
    /**
     * @var array $relations Set the relationships for the model
     *   https://docs.flightphp.com/awesome-plugins/active-record#relationships
     */
    protected array $relations = [
        'user' => [
            self::BELONGS_TO,
            UserRecord::class,
            'userid'
        ]
    ];

    /**
     * Constructor
     * @param mixed $databaseConnection The connection to the database
     */
    public function __construct($databaseConnection)
    {
        parent::__construct($databaseConnection, 'auth_tokens');
    }
}
