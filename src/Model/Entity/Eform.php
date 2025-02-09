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
class Eform extends Entity
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
        'eformid'                   => true,
        'formName'                  => true,
        'description'               => true,      
        'instruction'               => true,       
        'formName'                  => true,
        'description'               => true,
        'instruction'               => true,
        'UserId'                    => true,
        'logo'                      => true,
        'name'                      => true,
        'status'                    => true,
        'date'                      => true,
        'removed_flg'               => true,
        'active_flg'                => true,
        'signature'                 => true,
        'facematch'                 => true,
        'pulldata'                  => true,
        'publish'                   => true,
        'isAdditionalNotification'  => true,
        'additionalNotificationTo'  => true,
        'storeinprofile'            => true,
        'isclientInvitationEform'   => true,
        'slug'                      => true,
        'send_invitation_eform'     => true,
        'created'                   => true,
        'modified'                  => true,        
        'soft_delete'               => true,
        'is_approval'               => true,
        'eform_response_process_pdf'               => true,
        'isProcesspdf'               => true
    ];
}
