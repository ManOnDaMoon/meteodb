<?php

declare(strict_types=1);

namespace app\\records;

/**
 * ActiveRecord model for revocable, read-only API credentials.
 *
 * @property int $id
 * @property int $userid
 * @property string $name
 * @property string $token_hash
 * @property string $created_at
 */
class ApiTokenRecord extends \\flight\\ActiveRecord
{
    public function __construct($databaseConnection)
    {
        parent::__construct($databaseConnection, 'api_tokens');
    }
}
