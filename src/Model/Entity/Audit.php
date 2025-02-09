<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Gate Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $openFrom
 * @property string|null $openTo
 * @property int $userAllow
 * @property string|null $location
 * @property int $qrno
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Audit extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        'id' => true,
        'reference_id' => true,
        'user_id' => true,
        'table_name' => true,
        'action' => true,
        'before' => true,
        'after' => true,
        'ip' => true,
        'location' => true,
        'latlong' => true,
        'browser_ip' => true,
        'browser_latlong' => true,
        'browser_location' => true,
        'success' => true,
        'os' => true,
        'system_method' => true,
        'device_id' => true,
        'created_at' => true,
        'updated_at' => true,
        'created_on' => true,
        'updated_on' => true,
        'by_user_role' => true,
        'by_user_id' => true,
        'logout_reference_id' => true
    ];
}
