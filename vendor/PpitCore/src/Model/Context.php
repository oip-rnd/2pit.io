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
use Zend\InputFilter\InputFilterInterface;
use Zend\Log\Logger;
use Zend\Log\Writer;
use Zend\Mail;
use Zend\Mail\Message;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;
use Zend\Mvc\MvcEvent;
use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
use Zend\Session\Container;

/**
 * The Context class encapsulate the properties and preferences related to the current user and makes them available for the
 * the three layers, Model, View and Controller, processing the current request.
 * The 2pit User class manages only properties related to authentification. Most of the context properties and preferences are
 * managed by the current contact (Vcard class) and, via the contact, the current instance and community.
 * A user can be related to one or more contact infos owned by different P-Pit instances. It is possible inside a user session 
 * to switch to another contact from this list. At a given time, there is one active contact which properties are contextual.
 * The context give acces to the following properties:
 * - The current contact properties: formated name, email, phones and preferences (locale, demo mode active);
 * - The current community to which the current contact is linked
 * - The current instance data related to the current contact: FQDN (fully qualified domain name), caption, home page, legal notices;
 * - The specifications for the current instance, which are a set of parameters overriding the parameter values for customization purposes. Context manages transparently the appropriate value, either overriden or standard;
 * - The accessible P-Pit applications, depending on which the instance administrator gave to the current user
 * - The authorizations for the current contact: attributed roles and perimeters;
 * The context is created in the ZF2 initialization process for the request, inside the bootstrap function
 */
class Context
{
	/** @var \Model\Context */ protected static $exemplary;
    /** @var int */ public static $static_user_id;
	/** @var \Model\Instance */ public static $instance;
    /** @var array */ protected static $static_applications;
	/** @var int */ protected static $place_id;
	/** @var \Model\Place */ public static $place;
	/** @var string */ protected static $static_formated_name;
	/** @var int */ protected static $static_community_id;
	/** @var int */ protected static $static_vcard_id;
	/** @var \Model\Vcard */ public static $vcard;
	/** @var array */ protected static $static_roles;
	/** @var array */ protected static $static_perimeters;
	/** @var string */ protected static $static_locale;
	/** @var \Model\Account */ public static $profile;
	/** @var boolean */ protected static $static_is_demo_mode_active;

	/** @var array */ private static $config;
	/** @var \Zend\ServiceManager */ private static $serviceManager;
	
    /** 
     * Ignored 
     * @var string 
     */ 
	protected $inputFilter;
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Returns the current Context
     * @return Context
     */
	public static function getCurrent(/*$app = null*/) {
//		if ($app) Context::$static_app = $app;
		return Context::$exemplary;
	}

	/**
	 * Returns the security agent from the security layer (PpitUser module), which acts depending on the security strategy (explicit authentication vs SAML) for the global P-Pit application.
	 * @return \PpitUser\Model\SecurityAgent
	 */
	public function getSecurityAgent() {
		$sm = Context::getCurrent()->getServiceManager();
		return $sm->get(\PpitUser\Model\SecurityAgent::class);
//		return $this->getConfig()['ppitUserSettings']['securityAgent']; 
	}
	
	/**
	 * Returns the id (primary key) of the current instance (\Model\Instance).
	 * @return int
	 */
	public function getInstanceId() { return Context::$instance->id; }
	
	/**
	 * The getInstance() method returns the current instance (\Model\Instance).
	 * @return \Model\instance|NULL
	 */
    public function getInstance() { return Context::$instance; }
    
	/**
	 * Returns the route of the home page for the current instance. It defaults to 'index'.
	 * @return string|NULL
	 */
   	public function getHomePage() { return Context::$instance->home_page; }

    /**
     * Returns an array indexed by the current accessible application names. The item of the list with is TRUE is the default application
     * @return boolean[]
     */
    public function getApplications() {
    	$applications = array();
    	foreach (Context::$static_applications as $applicationName => $isCurrent) {
    		$applications[$applicationName] = Context::$config['ppitApplications'][$applicationName];
    		if ($isCurrent) $applications[$applicationName]['isCurrent'] = true;
    		else $applications[$applicationName]['isCurrent'] = false;
    	}
    	return $applications; 
    }

    /**
     * The current session is authenticated if either a registered user is authenticated (via the current SecurityAgent) or a user has provided a valid token for a temporary access (typically obtained inside an email notification).
     * @return boolean
     */
    public function isAuthenticated() {
    	$container = new Container('Zend_Auth');
    	if ($container->token_value) return true;
    	if ($container->user_id) return true;
    	return false;
    }
    
    /**
     * Returns the user_id (primary key on \Model\User) of the current connected user. Returns null in the case of a guest session
     * @return int|NULL
     */
    public function getUserId() { return Context::$static_user_id; }
    
    /**
     * Returns the contact list (array of \Model\Vcard) for the current connected user. Returns an empty array in the case of a guest session
     * @return \Model\Vcard[]
     */
    public function getContacts()
    {
    	if (!$this->getUserId()) return array();
    	else {
    		$select = UserContact::getTable()->getSelect()->where(array('user_id' => $this->getUserId()));
    		$cursor = UserContact::getTable()->transSelectWith($select);
    		$contacts = UserContact::getList();
    		return $contacts;
    	}
    }
    
    /**
     * Returns the formated Name for the current contact (\Model\Vcard)
     * @return string|NULL
     */
   	public function getFormatedName() { return Context::$static_formated_name; }
   	
   	/**
   	 * Returns the id (primary key on \Model\Community) of the current contact's community
   	 * @return int|NULL
   	 */
    public function getCommunityId() { return Context::$static_community_id; }
    
    /**
     * Returns the current id (primary key on \Model\Vcard) of the contact associated to the current connected user
     * @return int|NULL
     */
    public function getContactId() { return Context::$static_vcard_id; }

    /**
     * The getContact() method returns the current contact (\Model\Vcard).
     * @return \Model\Vcard|NULL
     */
    public function getContact() { return Context::$contact; }
    
    /**
     * The getProfile() method returns the current profile (\Model\Account) for the current connected user.
     * @return \Model\Account|NULL
     */
    public function getProfile() { return Context::$profile; }
       
    /**
     * Returns the id (primary key on \Model\Place) of the place for the current connected user
     * @return int|NULL
     */
    public function getPlaceId() { return Context::$place_id; }

    /**
     * The getPlace() method returns the current place (\Model\Place).
     * @return \Model\Place|NULL
     */
    public function getPlace() { return Context::$place; }
    
    /**
     * Returns the roles for which the current connected user is authorized.
     * Each session, connected user or not has the 'guest' role.
     * Each connected user has the 'user' role.
     * The other roles are extracted from the current vcard for the connected user.
     * @return string[]|NULL
     */
    public function getRoles() { return Context::$static_roles; }
    
    /**
     * Returns TRUE if and only if the connected user is authorized to the specified role.
     * @param string $role
     * @return boolean
     */
    public function hasRole($role) {
    	return array_key_exists($role, $this->getRoles());
    }

    /**
     * Returns TRUE if and only if the connected user is authorized to the specified route.
     * @param string $route
     * @return boolean
     */
    public function isAllowed($route)
    {
    	// Check ACL for the user roles
    	foreach($this->getRoles() as $userRole) {
    		if ($this->getSecurityAgent()->isAllowed($userRole, $route)) return true;
    	}
    	return false;
    }

    /**
     * Returns the authorized perimeters list (array of string) for the current connected user.
     * Returns an empty array in the case of a guest session.
     * @return string[]
     */
    public function getPerimeters() { return Context::$static_perimeters; }
    
    /**
     * Returns the locale (for example 'en_US') of the contact associated to the current connected user.
     * Returns the locale associated to the current instance in the case of a guest session.
     * @return string|NULL
     */
    public function getLocale() { return Context::$static_locale; }
    
    /**
     * Returns the Zend current service manager
     * @return ServiceManager
     */
    public function getServiceManager() { return Context::$serviceManager; }
    
    /**
     * Returns TRUE if the demo mode is active for the current vcard of the connected user
     * @return boolean
     */
	public function isDemoModeActive() { return Context::$static_is_demo_mode_active; }

    /**
     * Returns the current Zend config if $key is not given
     * If $key is given:
     * - either this key belongs to the instance specification, in which case the method returns the corresponding value
     * - or it does not belong to the instance specification, in which case the method returns the value for this key in the Zend config.
     * @param string $key
     * @return boolean
     */
	public function getConfig($key = null) {
    	if ($key) {
    		if (array_key_exists($key, $this->getInstance()->specifications)) return $this->getInstance()->specifications[$key];
    		elseif (array_key_exists($key, Context::$config)) return Context::$config[$key];
    		else return null;
    	}
    	else return Context::$config; 
    }

    public function getCurrentPeriod($periods) {
    	foreach ($periods['end_dates'] as $periodId => $date) if ($date >= date('Y-m-d')) return $periodId;
    	foreach (Context::getCurrent()->getConfig('place_config/default')['school_periods']['end_dates'] as $periodId => $date) if ($date >= date('Y-m-d')) return $periodId;
    	return null;
    }

	public function localize($label, $locale = null) {
		if (!$locale) $locale = $this->getLocale();
		if (!$label) return '';
		if (array_key_exists($locale, $label)) return $label[$locale];
		elseif (array_key_exists('en_US', $label)) return $label['en_US'];
		else return $label['default'];
	}

	public function getLocaleFor($label, $locale = null) {
		if (!$locale) $locale = $this->getLocale();
		if (!$label) return '';
		if (array_key_exists($locale, $label)) return $locale;
		elseif (array_key_exists('en_US', $label)) return 'en_US';
		else return 'default';
	}
	
    /**
     * Format the given float according to the current locale.
     * @param float $float
     * @param int $nbDecimal
     * @return boolean
     */
    public function formatFloat($float, $nbDecimal) {
    	if (!$float) return '0';
    	if ($this->getLocale() == 'fr_FR') return number_format($float, $nbDecimal, ',', '');
    	else return number_format($float, $nbDecimal, '.', ',');
    }

    /**
     * Encode in YYYY-MM-DD format the given date encoded according to the given locale.
     * @param string $date
     * @param string $locale
     * @return string
     */
    public static function sEncodeDate($date, $locale)
    {
    	if (!$date) return null;
    	/*if ($locale == 'fr_FR')*/ return substr($date, 6, 4).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
//    	else return substr($date, 6, 4).'-'.substr($date, 0, 2).'-'.substr($date, 3, 2);
    }
    
    /**
     * Encode in YYYY-MM-DD format the given date encoded according to the current locale.
     * @param string $date
     * @return string
     */
    public function encodeDate($date) {
    	if (!$date) return null;
    	/*if ($this->getLocale() == 'fr_FR')*/ return substr($date, 6, 4).'-'.substr($date, 3, 2).'-'.substr($date, 0, 2);
//    	else return substr($date, 6, 4).'-'.substr($date, 0, 2).'-'.substr($date, 3, 2);
    }

    /**
     * Decode according to the given locale the given date in YYYY-MM-DD format.
     * @param string $date
     * @param string $locale
     * @return string
     */
    public static function sDecodeDate($date, $locale) {
    	if (!$date) return null;
    	/*if ($locale == 'fr_FR')*/ return substr($date, 8, 2).'/'.substr($date, 5, 2).'/'.substr($date, 0, 4);
//    	else return substr($date, 5, 2).'/'.substr($date, 8, 2).'/'.substr($date, 0, 4);
    }
    
    /**
     * Decode according to the current locale the given date in YYYY-MM-DD format.
     * @param string $date
     * @return string
     */
    public function decodeDate($date) {
    	if (!$date) return null;
    	/*if ($this->getLocale() == 'fr_FR')*/ return substr($date, 8, 2).'/'.substr($date, 5, 2).'/'.substr($date, 0, 4);
//    	else return substr($date, 5, 2).'/'.substr($date, 8, 2).'/'.substr($date, 0, 4);
    }
    
    /**
     * Returns an array given the respective positions of Year, Month and day, corresponding to the current locale.
     * Returns [2, 0, 1] for US 
     * Returns [2, 1, 0] in the other cases
     * @return string
     */
    public function dateFormat() {
/*		if ($this->getLocale() == 'en_US') return array(2, 0, 1);
		else */return array(2, 1, 0);
    }

    /**
     * Decode according to the current locale the given time in YYYY-MM-DD h:i:s format.
     * @param string $time
     * @return string
     */
    public function decodeTime($time) {
    	if (!$time) return null;
    	if ($this->getLocale() == 'fr_FR') $date = substr($time, 8, 2).'/'.substr($time, 5, 2).'/'.substr($time, 0, 4);
    	else $date = substr($time, 5, 2).'/'.substr($time, 8, 2).'/'.substr($time, 0, 4);
    	return $date.' '.substr($time, 11,8);
    }
    
    /**
     * Returns the decimal separator corresponding to the current locale.
     * Returns '.' for US 
     * Returns ',' in the other cases
     * @return string
     */
    public function decimalSeparator() {
    	if ($this->getLocale() == 'en_US') return '.';
    	else return ',';
    }

    /**
     * Returns the number of business days from monday to friday included, figuring in a given number of calendar days starting from a given date, and according to the current locale.
     * The 'legalHoliday' parameter from Zend config gives per locale the list of legal holidays not to be counted as business days
     * @param string $date
     * @param int $gap
     * @return int
     */
    public function computeBusinessDaysGap($date, $gap)
    {
    	$nb_jours=0;
    	$date=explode('-', $date);
    	$timestamp=mktime(0, 0, 0, $date[1], $date[2], $date[0]);
    	for ($i = 0; $i < $gap; ) {
    		$timestamp = mktime(0, 0, 0, date('m',$timestamp), (date('d',$timestamp)+1), date('Y', $timestamp));
    		$businessDay = true;
    		if (date('w', $timestamp) == 0) $businessDay = false;
    		elseif (date('w', $timestamp) == 6) $businessDay = false;
    		elseif (in_array(date('Y', $timestamp).'-'.date('m',$timestamp).'-'.(date('d',$timestamp)), $this->getConfig('legalHoliday')[$this->getLocale()])) $businessDay = false;
    		if ($businessDay) $i++;
    	}
    	return date('Y', $timestamp).'-'.date('m', $timestamp).'-'.date('d', $timestamp);
    }

    /**
     * Send an email notification to $email, copy to $cc, with $subject and $textContent as a subject and content.
     * The email is not sent in demo mode.
     * The email is sent to the 'mailTo' Zend config parameter if set, in place of the given email address (for test purposes)
     * The email protocol (Smtp or Sendmail) is extracted from 'mailProtocol' Zend config parameter.
     * @param string $email
     * @param string $textContent
     * @param string $subject
     * @param string[] $cc
     */
    public static function sendMail($emails, $textContent, $subject, $cc = null)
    {
    	$context = Context::getCurrent();
    	$settings = $context->getConfig();
    
    	if ($settings['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) { // instance 0 is for demo
    		$text = new MimePart($textContent);
//    		$text->type = "text/plain; charset = UTF-8";
    		$text->type = \Zend\Mime\Mime::TYPE_HTML;
    		$text->encoding = \Zend\Mime\Mime::ENCODING_QUOTEDPRINTABLE;

    		$body = new MimeMessage();
    		$body->setParts(array($text));
    
    		$mail = new Mail\Message();
    		$mail->setEncoding("UTF-8");
			$mail->getHeaders()->addHeaderLine('Content-Transfer-Encoding', 'quoted-printable');
    		$mail->setBody($body);
    		$mail->setFrom($settings['mailAdmin'], $settings['nameAdmin']);
    		$mail->setSubject($subject);
    
    		// Send the mail to a test mailbox if a 'mailTo' setting is set (test environment) otherwise in the given mail (production)
    		if ($settings['mailTo']) $mail->addTo($settings['mailTo'], $settings['mailTo']);
    		else {
    			foreach (explode(',', $emails) as $email) $mail->addTo($email, explode('@', $email)[0]);
    		}
    		if ($cc) foreach ($cc as $ccEmail => $ccName) $mail->addCc($ccEmail, ($ccName) ? $ccName : $ccEmail);
    		if ($settings['mailProtocol'] == 'Smtp') {
    			$transport = new Mail\Transport\Smtp();
    		}
    		elseif ($settings['mailProtocol'] == 'Sendmail') {
    			$transport = new Mail\Transport\SendMail();
    		}
    
    		if ($settings['mailProtocol']) $rc = $transport->send($mail);
    
    		if ($settings['isTraceActive']) {
    
    			// Write to the log
    			$writer = new Writer\Stream('data/log/mailing.txt');
    			$logger = new Logger();
    			$logger->addWriter($writer);
    			$logger->info('from: '.$settings['nameAdmin'].' ('.$settings['mailAdmin'].') - to: '.$emails.' - subject: '.$subject.' - body: '.$textContent.' - RC: '.$rc);
    		}
    	}
    }

    public static function updateFromInstanceId($instance_id)
    {
		// Retrieve the instance data
		Context::$instance = Instance::get($instance_id);
    }
    
    public static function updateFromUserId($config, $user_id)
    {
    	$user = User::getTable()->transGet($user_id);
    	if ($user) {
    		$contact = Vcard::getTable()->transget($user->vcard_id);
    		Context::$vcard = $contact;
    		
    		// Retrieve the instance data
    		Context::$instance = Instance::get($contact->instance_id);
    		
//    		$user->community = Community::get($contact->community_id);
    		Context::$static_applications = array();
    		foreach ($contact->applications as $applicationId => $default) {
    			if (array_key_exists($applicationId, Context::$exemplary->getConfig('ppitApplications'))) Context::$static_applications[$applicationId] = $default;
    		}
    		$contact->applications;
    		Context::$static_community_id = $contact->community_id;
    		Context::$static_vcard_id = $contact->id;
    		Context::$static_formated_name = $contact->n_fn;
    		Context::$static_is_demo_mode_active = $config['isDemoModeActive'] && $contact->is_demo_mode_active;
    		Context::$static_roles = array();
    		foreach ($contact->roles as $role) Context::$static_roles[$role] = $role;
    		Context::$static_roles['guest'] = 'guest';
    		Context::$static_roles['user'] = 'user';
    		Context::$static_perimeters = $contact->perimeters;
    		if (!Context::$static_locale) Context::$static_locale = $contact->locale;

    		// Retrieve the profile of the logged user
    		Context::$profile = Account::get($contact->id, 'contact_1_id');
    		
    		// Retrieve the place data
    		Context::$place_id = Context::$instance->default_place_id;
    		Context::$place = Place::get($place_id);
    	}
    }
    
    /**
     * Installs the context for the Zend request processing and retrieves or intializes the user, vcard and community properties.
     * This static method should not be called outside from the bootstrap method of the Zend module class.
     * @param \Zend\Mvc\MvcEvent $e
     */
    public static function retrieve($e) {

		Context::$exemplary = new Context;

    	// Retrieve the config
    	$app = $e->getApplication();
    	$sm = $app->getServiceManager();
    	Context::$serviceManager = $sm;
    	
    	$config = $sm->get('config');
    	Context::$config = $config;

    	$request = $sm->get('Request');

    	// Retrieve the locale from the query
    	$query = $request->getUri()->getQuery();
    	$pos = strpos($query, 'locale');
    	if ($pos !== false) $locale = substr($query, $pos + 7);
    	else $locale = null;
    	if ($locale) Context::$static_locale = $locale;

    	// Retrieve the currentUser
    	$user_id = Context::$exemplary->getSecurityAgent()->getUserId();
    	Context::$static_user_id = $user_id;
    	if (!$user_id) {
    		$fqdn = (method_exists($request, 'getUri')) ? $request->getUri()->getHost() : null;
    		if ($fqdn) Context::$instance = Instance::get($fqdn, 'fqdn');
    		if (!Context::$instance) Context::$instance = Instance::get($config['defaultInstanceId']);
    		if (Context::$instance->id == 0) {
    			Context::$exemplary->getSecurityAgent()->demoAuthenticate($config['demoAccount']);
    			Context::updateFromUserId($config, Context::$exemplary->getSecurityAgent()->getUserId());
    		}
    		else {
	    		Context::$static_applications = array();
	    		Context::$place_id = Context::$instance->default_place_id;
	    		Context::$place = Place::get(Context::$place_id);
	    		Context::$static_community_id = 0;
	    		Context::$static_vcard_id = 0;
	    		Context::$static_formated_name = 'Guest';
	    		Context::$static_is_demo_mode_active = false;
	    		Context::$static_roles = array('guest');
	    		Context::$static_perimeters = array();

	    		if (!$locale) {
		    		if (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) && substr(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2) == 'en') Context::$static_locale = 'en_US';
		    		elseif (array_key_exists('HTTP_ACCEPT_LANGUAGE', $_SERVER) && substr(locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']), 0, 2) == 'fr') Context::$static_locale = 'fr_FR';
		    		else Context::$static_locale = Context::$instance->default_locale;
	    		}
    		}
    	}
    	else {
    		Context::updateFromUserId($config, $user_id);
    	}
    }

    public static function wsAuthenticate($e)
    {
    	$app = $e->getApplication();
    	$sm = $app->getServiceManager();
    	return Context::$exemplary->getSecurityAgent()->wsAuthenticate($sm);
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
}
