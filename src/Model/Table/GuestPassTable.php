<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GuestPass Model
 *
 * @method \App\Model\Entity\GuestPass get($primaryKey, $options = [])
 * @method \App\Model\Entity\GuestPass newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GuestPass[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GuestPass|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GuestPass saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GuestPass patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GuestPass[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GuestPass findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GuestPassTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('guest_pass');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('first_name')
            ->maxLength('first_name', 255)
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name');

        $validator
            ->scalar('last_name')
            ->maxLength('last_name', 255)
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name');

        $validator
            ->scalar('mobile')
            ->requirePresence('mobile', 'create');

        $validator
            ->scalar('email')
            ->requirePresence('email', 'create');

        $validator
            ->scalar('guest_pass_date')
            ->requirePresence('guest_pass_date', 'create');

        $validator
            ->scalar('guest_pass_time')
            ->requirePresence('guest_pass_time', 'create');

        $validator
            ->scalar('location')
            ->requirePresence('location', 'create');

        $validator
            ->scalar('purpose')
            ->allowEmptyString('purpose');

        $validator
            ->scalar('note')
            ->allowEmptyTime('note');


        return $validator;
    }
}
