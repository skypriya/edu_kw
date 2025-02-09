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
class EformResponse extends Entity
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
        'formName'                  => true,        
        'name'                      => true,
        'responseSendID'            => true,
        'eformId'                   => true,
        'description'               => true,      
        'device_token'              => true,       
        'akcessId'                  => true,
        'documentHash'              => true,
        'signatureHash'             => true,
        'filedata'                  => true,
        'filename'                  => true,
        'otp'                       => true,
        'bankId'                    => true,
        'api'                       => true,
        'signaturefile'             => true,
        'faceMatchPic'              => true,
        'profilepic'                => true,
        'eformasfile'               => true,
        'facialMatch'               => true,
        'date'                      => true,
        'approved'                  => true,
        'status'                    => true,
        'pulldata'                  => true,
        'facematch'                 => true,
        'mobile_local_id'           => true,
        'strict'                    => true,
        'created'                   => true,
        'modified'                  => true,
        'soft_delete'               => true
    ];
}
