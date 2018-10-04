<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * The Generic class is a generic model-level class used to count rows in any table according to SQL WHERE conditions
 */
class Generic implements InputFilterAwareInterface
{
    /** @var string */ public $group;
	/** @var string */ public $count;
    
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

    // Static fields
    /** @var \PpitCore\Model\Generic */ private static $table;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

	/**
	 * Used for relational (database) to object (php) mapping.
	 * @param array data
	 */
    public function exchangeArray($data)
    {
        $this->group = (isset($data['group'])) ? $data['group'] : null;
    	$this->count = (isset($data['count'])) ? $data['count'] : null;
    }

	/**
	 * Not used in P-Pit
	 * {@inheritDoc}
	 * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
	 */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Not used in P-Pit
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
     */
    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    /**
     * Returns the generic object to relational manager
     */
    public static function getTable()
    {
    	if (!Generic::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Generic::$table = $sm->get('PpitCore\Model\GenericTable');
    	}
    	return Generic::$table;
    }
}
