<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Gates Model
 *
 * @method \App\Model\Entity\Gate get($primaryKey, $options = [])
 * @method \App\Model\Entity\Gate newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Gate[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Gate|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gate saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Gate patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Gate[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Gate findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class GatesTable extends Table
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

        $this->setTable('gates');
        $this->setDisplayField('name');
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
            ->scalar('name')
            ->maxLength('name', 255)
            ->requirePresence('name', 'create')
            ->notEmptyString('name');

        $validator
            ->scalar('openFrom')
            ->allowEmptyTime('openFrom');

        $validator
            ->scalar('openTo')
            ->allowEmptyTime('openTo');

        $validator
            ->integer('userAllow')
            ->notEmptyString('userAllow');

        $validator
            ->scalar('location')
            ->allowEmptyString('location');
        
        $validator
            ->integer('qrno');

        return $validator;
    }
}
