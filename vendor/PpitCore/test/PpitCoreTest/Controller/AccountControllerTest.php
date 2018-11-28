<?php

namespace PpitCoreTest\Controller;

use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;

class AccountControllerTest extends AbstractHttpControllerTestCase
{
	public function setUp()
	{
		$this->setApplicationConfig(
			include '/Users/bruno/Sites/2pit.io/config/application.config.php'
		);
		parent::setUp();
	}

	public function testIndexActionCanBeAccessed()
	{
	    $this->dispatch('/account');
	    $this->assertResponseStatusCode(200);
	
	    $this->assertModuleName('Account');
	    $this->assertControllerName('PpitCore\Controller\Account');
	    $this->assertControllerClass('AccountController');
	    $this->assertMatchedRouteName('account');
	}
}