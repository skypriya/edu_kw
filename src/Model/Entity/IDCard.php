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
class IDCard extends Entity
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
        'fk_users_id'       => true,
        'idNo'              => true,      
        'idCardExpiyDate'   => true,       
        'AkcessID'          => true,
        'documentHash'      => true,
        'signatureHash'     => true,
        'Title'             => true,
        'transactionID'     => true,
        'timeStamp'         => true,
        'channelName'       => true,
        'documentId'        => true,
        'fileUrl'           => true,
        'fileName'          => true,        
        'status'            => true,        
        'created'           => true,
        'modified'          => true,
        'image_fileName'    => true,
    ];
}
