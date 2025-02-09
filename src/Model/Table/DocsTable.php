<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Docs Model
 *
 * @method \App\Model\Entity\Doc get($primaryKey, $options = [])
 * @method \App\Model\Entity\Doc newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Doc[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Doc|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Doc saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Doc patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Doc[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Doc findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class DocsTable extends Table
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

        $this->setTable('docs');
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
            ->scalar('attachs')
            ->maxLength('attachs', 255)
            ->requirePresence('attachs', 'create')
            ->notEmptyString('attachs');

        $validator
            ->integer('size')
            ->allowEmptyString('size');

        $validator
            ->integer('userId')
            ->requirePresence('userId', 'create')
            ->notEmptyString('userId');

        return $validator;
    }
}
