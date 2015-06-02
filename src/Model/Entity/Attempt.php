<?php

namespace LoginAttempts\Model\Entity;

use Cake\ORM\Entity;

/**
 * Attempt Entity.
 *
 * @property string $ip
 * @property string $action
 * @property \Carbon\Carbon $expires
 * @property \Carbon\Carbon $created_at
 */
class Attempt extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'ip' => true,
        'action' => true,
        'expires' => true,
        'created_at' => true,
    ];

}
