<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * GateUsers Model
 *
 * @method \App\Model\Entity\GateUser get($primaryKey, $options = [])
 * @method \App\Model\Entity\GateUser newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\GateUser[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\GateUser|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GateUser saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\GateUser patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\GateUser[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\GateUser findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GateUsersTable extends Table
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

        $this->setTable('gate_users');
        $this->setDisplayField('id');
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
            ->integer('userId')
            ->requirePresence('userId', 'create')
            ->notEmptyString('userId');

        $validator
            ->integer('gateId')
            ->requirePresence('gateId', 'create')
            ->notEmptyString('gateId');

        return $validator;
    }
}
