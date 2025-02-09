<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Notify Model
 *
 * @method \App\Model\Entity\Notify get($primaryKey, $options = [])
 * @method \App\Model\Entity\Notify newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\Notify[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\Notify|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notify saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\Notify patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\Notify[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\Notify findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class NotifyTable extends Table
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

        $this->setTable('notify');
        $this->setDisplayField('subj');
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
            ->scalar('emp');

        $validator
            ->integer('sentId');
        $validator
            ->scalar('agency');

        $validator
            ->scalar('adm');
        $validator
            ->scalar('acc');

        $validator
            ->scalar('subj');

        $validator
            ->scalar('msg');

        $validator
            ->scalar('pagelink');

        return $validator;
    }

    
}
