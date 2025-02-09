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
class Fields extends Entity
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
        'fk_eform_id'           => true,
        'labelname'             => true,      
        'keyfields'             => true,       
        'keytype'               => true,
        'signature_required'    => true,
        'file_verified'         => true,
        'options'               => true,
        'verification_grade'    => true,
        'section_id'            => true,
        'section_color'         => true,
        'sectionfields'         => true,
        'instructions'          => true,
        'is_mandatory'          => true,
        'status'                => true,       
        'created'               => true,
        'modified'              => true,
        'soft_delete'           => true
    ];
}
