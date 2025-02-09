<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ClassAttends Model
 *
 * @method \App\Model\Entity\ClassAttend get($primaryKey, $options = [])
 * @method \App\Model\Entity\ClassAttend newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\ClassAttend[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\ClassAttend|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassAttend saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\ClassAttend patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\ClassAttend[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\ClassAttend findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ClassAttendsTable extends Table
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

        $this->setTable('class_attends');
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
            ->integer('classId')
            ->requirePresence('classId', 'create')
            ->notEmptyString('classId');

        $validator
            ->integer('userId')
            ->requirePresence('userId', 'create')
            ->notEmptyString('userId');

        $validator
            ->integer('attends')
            ->notEmptyString('attends');

        return $validator;
    }
}
