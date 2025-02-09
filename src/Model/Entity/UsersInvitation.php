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
class UsersInvitationlist extends Entity
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
        'idcardno' => true,
        'name' => true,
        'companyName' => true,
        'address' => true,
        'city' => true,
        'country' => true,
        'email' => true,
        'password' => true,
        'mobileNumber' => true,
        'usertype' => true,
        'gender' => true,
        'dob' => true,
        'photo' => true,
        'otherdetails' => true,
        'passkey' => true,
        'timeout' => true,
        'domainName' => true,
        'administrationName' => true,
        'loginOpt' => true,
        'siteStatus' => true,
        'status' => true,        
        'active' => true,
        'faculty' => true,
        'courses' => true,
        'academic_personal_type' => true,
        'staff_type' => true,
        'adminssion_date' => true,
        'isLogin' => true,
        'created' => true,
        'modified' => true,
        'akcessId' => true,
        'users_by_akcess_id' => true,
        'soft_delete' => true
        
    ];
}
