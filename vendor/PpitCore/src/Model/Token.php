<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Db\Sql\Expression;
use Zend\Crypt\Password\Bcrypt;
use Zend\Session\Container;

class Token extends Context
{
	public $id;
    public $instance_id;
    public $vcard_id;
    private $value;
    private $validity;
    public $authorized_route;
	public $authorized_param;
	public $authorized_id;
    public $locale;
	
    protected $inputFilter;
    protected $delegationInputFilter;

    // Static fields
    private static $table;
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->vcard_id = (isset($data['vcard_id'])) ? $data['vcard_id'] : null;
 	   	$this->value = (isset($data['value'])) ? $data['value'] : null;
 	   	$this->validity = (isset($data['validity'])) ? $data['validity'] : null;
 	   	$this->authorized_route = (isset($data['authorized_route'])) ? $data['authorized_route'] : null;
 	   	$this->authorized_param = (isset($data['authorized_param'])) ? $data['authorized_param'] : null;
 	   	$this->authorized_id = (isset($data['authorized_id'])) ? $data['authorized_id'] : null;
 	   	$this->locale = (isset($data['locale'])) ? $data['locale'] : null;
    }

    public function toArray()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['vcard_id'] = (int) $this->vcard_id;
    	$data['value'] = $this->value;
    	$data['validity'] = ($this->validity) ? $this->validity : null;
    	$data['authorized_route'] = $this->authorized_route;
    	$data['authorized_param'] = $this->authorized_param;
    	$data['authorized_id'] = (int) $this->authorized_id;
    	$data['locale'] = $this->locale;
    	return $data;
    }

    public function getValue() { return $this->value; }

    public function getValidity() { return $this->validity; }

    public static function getNew($acl) {
	    $token = new Token;
    	$token->value = md5(uniqid(rand(), true));
    	foreach ($acl as $ac) {
    		$token->id = null;
	    	$token->vcard_id = $ac['vcard_id'];
	    	$token->authorized_route = $ac['authorized_route'];
	    	$token->authorized_param = $ac['authorized_param'];
	    	$token->authorized_id = $ac['authorized_id'];
	    	if ($ac['validity']) $token->validity = date('Y-m-d H:i:s', strtotime(date('Y-m-d H:i:s').' + '.$ac['validity']));
	    	$token->locale = 'fr_FR';
	    	Token::getTable()->save($token);
    	}
    }

    public static function authenticate($authorized_route, $routeMatch) {
    	$context = Context::getCurrent();
    	
    	// Retrieve the taken value from the session
    	$container = new Container('Zend_Auth');
    	$value = $container->token_value;

		// Retrieve the token
    	$select = Token::getTable()->getSelect()
    		->where(array('authorized_route' => $authorized_route, 'value' => $value, 'validity >= ?' => Date('Y-m-d')));
    	$cursor = Token::getTable()->transSelectWith($select);
    	$authorized = false;
    	foreach ($cursor as $token) 
    	{
    		if ($token->authorized_id == $routeMatch->getParams()[$token->authorized_param]) {
    			$authorized = true;
    			break;
    		}
    	}
    	if (!$authorized) return null;

    	// Update the context properties
    	Context::$static_locale = $token->locale;
    	$contact = Vcard::getTable()->get($token->vcard_id);
    	Context::$static_n_fn = $contact->n_fn;

    	// Retrieve the instance data
    	Context::$instance = Instance::getTable()->get($contact->instance_id);

		// Keep the token value in session for subsequent request
    	$container = new Container('Zend_Auth');
    	$container->token_value = $value;
    	 
    	return $token;
    }

    public static function getTable()
    {
    	if (!Token::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Token::$table = $sm->get(TokenTable::class);
    	}
    	return Token::$table;
    }
}
