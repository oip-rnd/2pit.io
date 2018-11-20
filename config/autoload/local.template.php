<?php
/**
 * Local Configuration Override
*
* This configuration override file is for overriding environment-specific and
* security-sensitive configuration information. Copy this file without the
* .dist extension at the end and populate values as needed.
*
* @NOTE: This file is ignored from Git by default with the .gitignore included
* in ZendSkeletonApplication. This is a good practice, as it prevents sensitive
* credentials from accidentally being committed into version control.
*/

return array(
    'db' => array(
        'driver'         => 'Pdo',
        'dsn'            => 'mysql:dbname=p_pit;host=localhost',
        'driver_options' => array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ),
        'username' => 'root',
        'password' => 'root',
    ),
	'ppitUserSettings' => array(
		'securityAgent' => new \PpitUser\Model\SecurityAgent,
		'strongPassword' => false,
		'tokenValidity' => 5,
		'checkAcl' => true,
		'safe' => array(
		),
	),
	'specificationMode' => 'config', // 'config' vs 'database'
	'mailProtocol' => 'Smtp', // Should be 'SendMail' or 'Smtp'. No email sent if null
	'mailAdmin' => 'no-reply@2pit.io',
	'nameAdmin' => 'Pro bono corpo',
);
