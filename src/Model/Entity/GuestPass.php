<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * GuestPass Entity
 *
 * @property int $id
 * @property string $akcessId
 * @property string $first_name
 * @property string $last_name
 * @property string $invitee_name
 * @property string $institution_name
 * @property string $mobile
 * @property string $email
 * @property string $country_code
 * @property int $location
 * @property string|null $purpose
 * @property string|null $note
 * @property string|null $documentId
 * @property string|null $fileUrl
 * @property string|null $fileName
 * @property \Cake\I18n\FrozenTime $guest_pass_date
 * @property string $guest_pass_time
 * @property int $soft_delete
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class GuestPass extends Entity
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
        'first_name' => true,
        'last_name' => true,
        'invitee_name' => true,
        'institution_name' => true,
        'mobile' => true,
        'email' => true,
        'country_code' => true,
        'location' => true,
        'purpose' => true,
        'guest_pass_date' => true,
        'guest_pass_time' => true,
        'note' => true,
        'documentId' => true,
        'fileUrl' => true,
        'fileName' => true,
        'created' => true,
        'modified' => true,
        'soft_delete' => true
    ];
}
