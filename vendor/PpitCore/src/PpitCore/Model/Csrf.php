<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 20016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Model;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

/**
 * Provides a CSRF input for the view level with a specific name, for isolation purpose in case of several forms exists in one html page
 */
class Csrf implements InputFilterAwareInterface
{
	/** @var string */ public $csrfName;

    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;

	/**
	 * @param string $name
	 */
	public function __construct($name)
	{
		$this->csrfName = $name;
	}
	
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
	
    /**
     * 
     * {@inheritDoc}
     * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
     */
    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();

            $inputFilter->add($factory->createInput(array(
            		'name'     => $this->csrfName,
            		'required' => false,
            )));
                        
            $this->inputFilter = $inputFilter;
        }
        return $this->inputFilter;
    }
}
