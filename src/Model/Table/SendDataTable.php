<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * IDCards Model
 *
 * @method \App\Model\Entity\IDCard get($primaryKey, $options = [])
 * @method \App\Model\Entity\IDCard newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\IDCard[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\IDCard|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\IDCard saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\IDCard patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\IDCard[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\IDCard findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class SendDataTable extends Table
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

        $this->setTable('sendData');
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
            ->scalar('ackessID');
        
        $validator
            ->scalar('message')
            ->maxLength('message', 160);
        
       
        return $validator;
    }
}
