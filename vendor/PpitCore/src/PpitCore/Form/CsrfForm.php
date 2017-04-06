<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\Form;

use Zend\Form\Form;

class CsrfForm extends Form
{
    public function __construct($name = null)
    {
        // we want to ignore the name passed
        parent::__construct($name);
        $this->setAttribute('method', 'post');
    }
    
    public function addCsrfElement($csrfName, $settings = null) {
        
        $this->add(
            array(
                'name' => $csrfName,
            	'type' => 'Csrf',
	 			'attributes' => array(
					'id' => $csrfName,
				),
            	'options' => array(
                    'csrf_options' => array(
                        'timeout' => ($settings) ? $settings['ppitCoreSettings']['formExpiration'] : 600
                    )
                )
            )
        );
    }
}
