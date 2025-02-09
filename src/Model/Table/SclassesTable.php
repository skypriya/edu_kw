<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Sclasses Model
 *
 * @method \App\Model\Entity\Sclass get($primaryKey, $options = [])
 * @method \App\Model\Entity\Sclass newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Sclass[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Sclass|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sclass saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Sclass patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Sclass[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Sclass findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SclassesTable extends Table
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

        $this->setTable('sclasses');
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
            ->scalar('details')
            ->allowEmptyString('details');

        $validator
            ->scalar('location')
            ->notEmptyString('location');

        $validator
            ->integer('qrno');

        $validator
            ->scalar('openFrom')
            ->notEmptyTime('openFrom');

        $validator
            ->scalar('openTo')
            ->notEmptyTime('openTo');

        $validator
            ->integer('userallow')
            ->notEmptyString('userallow');

        $validator
            ->integer('days')
            ->notEmptyString('days');

        return $validator;
    }
}
