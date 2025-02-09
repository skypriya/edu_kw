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
class SendData extends Entity
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
        'fk_idcard_id'  => true,
        'phone_no'      => true,
        'country_code'        => true,        
        'email'              => true,
        'ackessID'      => true,
        'send_type'        => true,
        'recievedType' => true,
        'send_status'        => true,
        'soft_delete'        => true,
        'createdDate'       => true,       
        'modified'          => true,
        'document_id'       => true,
        'message'           => true,
        'group_type'           => true,
        'group_id'           => true
    ];
}
