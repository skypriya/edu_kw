<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Doc Entity
 *
 * @property int $id
 * @property string $name
 * @property string $attachs
 * @property int|null $size
 * @property int $userId
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Doc extends Entity
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
        'attachs' => true,
        'size' => true,
        'fk_documenttype_id' => true,
        'idCardExpiyDate' => true,
        'fileUrl' => true,
        'fileName' => true,
        'userId' => true,
        'created' => true,
        'modified' => true
    ];
}
