<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Sclass Entity
 *
 * @property int $id
 * @property string $name
 * @property string|null $details
 * @property string|null $location
 * @property int $qrno
 * @property string|null $openFrom
 * @property string|null $openTo
 * @property int $userAllow
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Sclass extends Entity
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
        'name' => true,
        'details' => true,
        'location' => true,
        'qrno' => true,
        'openFrom' => true,
        'openTo' => true,
        'userAllow' => true,
        'created' => true,
        'modified' => true
    ];
}
