<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity
 *
 * @property int $id
 * @property string $akcessId
 * @property string $name
 * @property string $companyName
 * @property string $address
 * @property string $city
 * @property string $country
 * @property string $email
 * @property string $password
 * @property string $mobileNumber
 * @property string $usertype
 * @property string $gender
 * @property  \Cake\I18n\FrozenTime  $dob
 * @property string $photo
 * @property string $otherdetails
 * @property string $status
 * @property string $passkey
 * @property string $timeout
 * @property string $domainName
 * @property string $administrationName
 * @property string $loginOpt
 * @property string $siteStatus
 * @property int $isLogin
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class User extends Entity
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
        'akcessId' => true,
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
        'status' => true,
        'timeout' => true,
        'passkey' => true,
        'domainName' => true,
        'administrationName' => true,
        'loginOpt' => true,
        'siteStatus' => true,
        'isLogin' => true,
        'active' => true,
        'faculty' => true,
        'courses' => true,
        'academic_personal_type' => true,
        'staff_type' => true,
        'adminssion_date' => true,
        'created' => true,
        'modified' => true,
        'soft_delete' => true
    ];

    /**
     * Fields that are excluded from JSON versions of the entity.
     *
     * @var array
     */
    protected $_hidden = [
        'password'
    ];

    // Add this method hash password in place of pain text
    protected function _setPassword($value)
    {
        if (strlen($value) > 0) {
            $hasher = new DefaultPasswordHasher();
            return $hasher->hash($value);
        }
    }
}
