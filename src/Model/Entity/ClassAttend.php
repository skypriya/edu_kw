<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * ClassAttend Entity
 *
 * @property int $id
 * @property int $classId
 * @property int $userId
 * @property int $attends
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class ClassAttend extends Entity
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
        'classId' => true,
        'userId' => true,
        'attends' => true,
        'created' => true,
        'modified' => true
    ];
}
