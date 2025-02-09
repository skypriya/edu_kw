<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
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

        $this->setTable('users');
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
            ->scalar('companyName')
            ->maxLength('companyName', 255);
            //->requirePresence('companyName', 'create');
            //->notEmptyString('companyName');

        $validator
            ->scalar('address');
            

         $validator
            ->scalar('city')
            ->maxLength('city', 50)
             ->notEmptyString('city')
              ->requirePresence('city', 'create');

        $validator
            ->scalar('country')
            ->maxLength('country', 50);
            //->requirePresence('country', 'create')
            //->notEmptyString('country');

        $validator
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('mobileNumber')
            ->maxLength('mobileNumber', 50)
            ->requirePresence('mobileNumber', 'create')
            ->notEmptyString('mobileNumber');

        $validator
            ->scalar('usertype')
            ->maxLength('usertype', 50);

        
        $validator
            ->scalar('gender');
        $validator
            ->date('dob');       
             
        
        $validator
            ->scalar('photo')
            ->allowEmptyString('photo', null);

        $validator
            ->scalar('otherdetails');
        $validator
            ->scalar('status');
        
        $validator
            ->scalar('timeout');
        $validator
            ->scalar('passkey');

        $validator
            ->scalar('domainName')
            ->add('domainName', 'valid-url', ['rule' => 'url']);

        $validator
            ->scalar('administrationName');        
        $validator
            ->scalar('loginOpt');
        $validator
            ->scalar('siteStatus');

        $validator
            ->integer('isLogin');
        

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique([]));

        return $rules;
    }
}
