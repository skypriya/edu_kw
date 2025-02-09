<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;


/**
 * Notify Entity
 *
 * @property int $id
 * @property int $sentId
 * @property string $emp
 * @property string $agency
 * @property string $adm
 * @property string $acc
 * @property string $subj
 * @property string $msg
 * @property string $pagelink
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 */
class Notify extends Entity
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
        'emp' => true,
        'agency' => true,
        'adm' => true,
        'acc' => true,
        'subj' => true,
        'msg' => true,
        'pagelink' => true,
        'sentId' => true,
        'created' => true,
        'modified' => true
    ];

    
}
