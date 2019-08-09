<?php
namespace PpitCore\Model;

use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Document
{
    public $id;
    public $instance_id;
    public $status;
    public $type;
    public $place_id;
    public $folder;
    public $identifier;
    public $parent_id;
    public $name;
    public $acl;
    public $mime;
    public $content;
    public $binary_content;
    public $audit;
    public $update_time;
    
    // Transient properties
	public $authorization;
	public $parents;
    public $parts;
    public $files;
    public $destinationPath;
    
    // Deprecated
    public $locale_1;
    public $locale_2;
    public $summary;
    public $summary_locale_1;
    public $summary_locale_2;
    public $image;
    public $image_locale_1;
    public $image_locale_2;
    public $first_part_id;
    public $community_id;
    public $lock;
    public $properties_en_us = array();
    public $properties_fr_fr = array();
    public $url;
    
    protected $inputFilter;
    
    // Static fields
    private static $table;

	public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->instance_id = (isset($data['instance_id'])) ? $data['instance_id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->place_id = (isset($data['place_id'])) ? $data['place_id'] : null;
        $this->folder = (isset($data['folder'])) ? $data['folder'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->parent_id = (isset($data['parent_id'])) ? $data['parent_id'] : null;
        $this->name = (isset($data['name'])) ? $data['name'] : null;
        $this->acl = (isset($data['acl'])) ? json_decode($data['acl'],true) : null;
        $this->mime = (isset($data['mime'])) ? $data['mime'] : null;
        $this->content = (isset($data['content'])) ? json_decode($data['content'], true) : array();
        $this->binary_content = (isset($data['binary_content'])) ? $data['binary_content'] : null;
        $this->audit = (isset($data['audit'])) ? json_decode($data['audit'],true) : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : null;

	    // Deprecated
        $this->locale_1 = (isset($data['locale_1'])) ? $data['locale_1'] : null;
        $this->locale_2 = (isset($data['locale_2'])) ? $data['locale_2'] : null;
        $this->summary = (isset($data['summary'])) ? $data['summary'] : null;
        $this->summary_locale_1 = (isset($data['summary_locale_1'])) ? $data['summary_locale_1'] : null;
        $this->summary_locale_2 = (isset($data['summary_locale_2'])) ? $data['summary_locale_2'] : null;
        $this->image = (isset($data['image'])) ? json_decode($data['image'], true) : array();
        $this->image_locale_1 = (isset($data['image_locale_1'])) ? json_decode($data['image_locale_1'], true) : array();
        $this->image_locale_2 = (isset($data['image_locale_2'])) ? json_decode($data['image_locale_2'], true) : array();
        $this->first_part_id = (isset($data['first_part_id'])) ? $data['first_part_id'] : null;
        $this->url = (isset($data['url'])) ? $data['url'] : null;
        $this->properties = (isset($data['properties'])) ? json_decode($data['properties'], true) : null;
        $this->properties_locale_1 = (isset($data['properties_locale_1'])) ? json_decode($data['properties_locale_1'], true) : null;
        $this->properties_locale_2 = (isset($data['properties_locale_2'])) ? json_decode($data['properties_locale_2'], true) : null;
        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
        $this->lock = (isset($data['lock'])) ? $data['lock'] : null;
        $this->properties_en_us = (isset($data['properties_en_us'])) ? json_decode($data['properties_en_us'], true) : null;
        $this->properties_fr_fr = (isset($data['properties_fr_fr'])) ? json_decode($data['properties_fr_fr'], true) : null;
    }

    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['instance_id'] = (int) $this->instance_id;
    	$data['status'] = $this->status;
    	$data['type'] = $this->type;
    	$data['place_id'] = (int) $this->place_id;
    	$data['folder'] = $this->folder;
    	$data['identifier'] = $this->identifier;
    	$data['parent_id'] = (int) $this->parent_id;
    	$data['name'] = $this->name;
    	$data['acl'] = $this->acl;
    	$data['mime'] = $this->mime;
    	$data['content'] = $this->content;
    	$data['binary_content'] = $this->binary_content;
    	$data['audit'] = $this->audit;

    	// Deprecated
    	$data['locale_1'] = $this->locale_1;
    	$data['locale_2'] = $this->locale_2;
    	$data['summary'] = $this->summary;
    	$data['summary_locale_1'] = $this->summary_locale_1;
    	$data['summary_locale_2'] = $this->summary_locale_2;
    	$data['image'] = $this->image;
    	$data['image_locale_1'] = $this->image_locale_1;
    	$data['image_locale_2'] = $this->image_locale_2;
    	$data['first_part_id'] = (int) $this->first_part_id;
    	$data['url'] = $this->url;
    	$data['properties'] = $this->properties;
    	$data['properties_locale_1'] = $this->properties_locale_1;
    	$data['properties_locale_2'] = $this->properties_locale_2;
    	 
    	return $data;
    }
    
    public function toArray()
    {
    	$data = $this->getProperties();
    	$data['acl'] = json_encode($this->acl);
    	$data['content'] = json_encode($this->content, JSON_PRETTY_PRINT);
    	$data['audit'] = json_encode($this->audit);

	    // Deprecated
    	$data['image'] = json_encode($this->image);
    	$data['image_locale_1'] = json_encode($this->image_locale_1);
    	$data['image_locale_2'] = json_encode($this->image_locale_2);
    	$data['properties'] = json_encode($this->properties);
    	$data['properties_locale_1'] = json_encode($this->properties_locale_1);
    	$data['properties_locale_2'] = json_encode($this->properties_locale_2);
    	$data['community_id'] = (int) $this->community_id;
    	$data['properties'] = json_encode($this->properties);
    	$data['lock'] = (boolean) $this->lock;
    	$data['properties_en_us'] = json_encode($this->properties_en_us);
    	$data['properties_fr_fr'] = json_encode($this->properties_fr_fr);
    
    	return $data;
    }
    
    protected function getParents($document, &$result, $root_id = 0) {
    	$context = Context::getCurrent();
    	if ($document) {
    		if ($document->id != $root_id) {
    			$parent = Document::getTable()->get($document->parent_id);
    			$this->getParents($parent, $result, $root_id);
    		}
    		$result[] = $document;
    	}
    }
    
	public function retrieveAuthorization()
	{
    	$context = Context::getCurrent();
    	$allAccess = null;
    	$vcardAccess = null;
    	$communityAccess = null;
    	if (array_key_exists('all', $this->acl)) $allAccess = $this->acl['all'];
    	if (array_key_exists('vcards', $this->acl)) {
    		if (array_key_exists($context->getContactId(), $this->acl['vcards'])) {
    			$vcardAccess = $this->acl['vcards'][$context->getContactId()];
    		}
    	}
    	if (array_key_exists('community', $this->acl)) {
    		if (array_key_exists($context->getCommunityId(), $this->acl['communities'])) {
	    		$communityAccess = $this->acl['communities'][$context->getCommunityId()];
    		}
    	}

    	// Give the highest-level right between contact and community
    	if ($allAccess == 'admin' || $communityAccess == 'admin' || $vcardAccess == 'admin') $this->authorization = 'admin';
    	elseif ($allAccess == 'write' || $communityAccess == 'write' || $vcardAccess == 'write') $this->authorization = 'write';
    	elseif ($allAccess == 'read' || $communityAccess == 'read' || $vcardAccess == 'read') $this->authorization = 'read';
    	else $this->authorization = null;
    	return $this->authorization;
    }

    public static function getList($type, $params, $order = '+name')
    {
    	
    	$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');

    	$select = Document::getTable()->getSelect()
			->order($order);

		$where = new Where;
		$where->notEqualTo('core_document.status', 'deleted');
		
		// Set the filters
		foreach ($params as $propertyId => $property) {
			$where->like('core_document.'.$propertyId, '%'.$property.'%');
		}

		$select->where($where);
		$cursor = Document::getTable()->selectWith($select);
		$documents = array();
		foreach ($cursor as $document) {
/*
			$document->retrieveAuthorization();

    		// Set the inherited authorization where unspecified
    		if (!$parent) $documents[$document->id] = $document;
    		else {
	    		if (!$document->authorization) $document->authorization = $parent->authorization;
				if ($document->authorization)*/ $documents[$document->id] = $document;
//    		}
		}

		return $documents;
    }

    public static function get($id, $column = 'id', $id2 = false, $column2 = false, $id3 = false, $column3 = false, $id4 = false, $column4 = false)
    {
    	$context = Context::getCurrent();
		$document = Document::getTable()->get($id, $column, $id2, $column2, $id3, $column3, $id4, $column4);
/*
	    // Recursively retrieve the parents
	    $document->parents = array();
	    $document->getParents($document, $document->parents, $root_id);

	    // Retrieve the most specific access right for this community or user on the parent resource
	    for ($i = count($document->parents)-1; $i >= 0; $i--) {
	    	$parent = Document::getTable()->get($document->parents[$i]->id);
	    	$authorization = $parent->retrieveAuthorization();
	    	if ($authorization) {
	    		$document->authorization = $authorization;
	    		break;
	    	}
	    }*/

    	return $document;
    }
    
    public static function getWithPath($path)
    {
    	$path = explode('/', $path);
    	$parent_id = 0;
    	foreach ($path as $link) {
    		if ($link) {
	    		$select = Document::getTable()->getSelect()->where(array('status != ?' => 'deleted', 'parent_id' => $parent_id, 'name' => $link));
	    		$current = Document::getTable()->selectWith($select)->current();
	    		if (!$current) return null;
	    		$parent_id = $current->id;
    		}
    	}
    	return $current;
    }

    public static function instanciate($parent_id = null)
    {
    	$document = new Document;
		$document->status = 'new';
    	$document->parent_id = $parent_id;
    	$document->acl = array();
    	$document->content = array();
    	$document->audit = array();
    	 
    	// Deprecated
    	$document->image = array();
    	$document->image_locale_1 = array();
    	$document->image_locale_2 = array();
    	$document->properties = array();
    	$document->properties_locale_1 = array();
    	$document->properties_locale_2 = array();
    	$document->parts = array();

    	return $document;
    }
/*    
    public function retrieveContent()
    {
    	$part_id = $this->first_part_id;
    	while ($part_id) {
    		$part = DocumentPart::getTable()->transGet($part_id);
    		$part_id = $part->next_part_id;
    		$this->parts[] = $part;
    	}
    }*/

	public function loadData($data)
	{
		$context = Context::getCurrent();
		$auditRow = array(
				'time' => Date('Y-m-d G:i:s'),
				'n_fn' => $context->getFormatedName(),
		);
		if (array_key_exists('status', $data)) {
			$status = trim(strip_tags($data['status']));
			if ($status == '' || strlen($status) > 255) return 'Integrity';
			if ($this->status != $status) $auditRow['status'] = $this->status = $status;
		}
		if (array_key_exists('type', $data)) {
			$type = trim(strip_tags($data['type']));
			if ($type == '' || strlen($type) > 255) return 'Integrity';
			if ($this->type != $type) $auditRow['type'] = $this->type = $type;
		}
		if (array_key_exists('place_id', $data)) {
			$place_id = $data['place_id'];
			if ($this->place_id != $place_id) $auditRow['place_id'] = $this->place_id = $place_id;
		}
		if (array_key_exists('folder', $data)) {
			$folder = trim(strip_tags($data['folder']));
			if ($folder == '' || strlen($folder) > 255) return 'Integrity';
			if ($this->folder != $folder) $auditRow['folder'] = $this->folder = $folder;
		}
		if (array_key_exists('identifier', $data)) {
			$identifier = trim(strip_tags($data['identifier']));
			if ($identifier == '' || strlen($identifier) > 255) return 'Integrity';
			if ($this->identifier != $identifier) $auditRow['identifier'] = $this->identifier = $identifier;
		}
		if (array_key_exists('parent_id', $data)) {
			$parent_id = $data['parent_id'];
			if ($this->parent_id != $parent_id) $auditRow['parent_id'] = $this->parent_id = $parent_id;
		}
		if (array_key_exists('name', $data)) {
			$name = trim(strip_tags($data['name']));
			if ($name == '' || strlen($name) > 255) return 'Integrity';
			if ($this->name != $name) $auditRow['name'] = $this->name = $name;
		}
		if (array_key_exists('mime', $data)) {
			$mime = trim(strip_tags($data['mime']));
			if ($mime == '' || strlen($mime) > 255) return 'Integrity';
			if ($this->mime != $mime) $auditRow['mime'] = $this->mime = $mime;
		}
		
		if (array_key_exists('binary_content', $data)) {
			$filename = $data['binary_content']['tmp_name'];
			if (!is_uploaded_file($filename)) return 'Integrity';
			if ($data['binary_content']['size'] > 1024000) return 'Integrity';
			$handle = fopen($filename, "r");
			$this->binary_content = fread($handle, filesize($filename));
			fclose($handle);
		}

		if (array_key_exists('acl', $data)) {
			$acl = $data['acl'];
			if ($this->acl != $acl) $auditRow['acl'] = $this->acl = $acl;
		}
		if (array_key_exists('content', $data)) {
			if (!is_array($data['content'])) return 'Integrity';
			foreach ($data['content'] as $key => $value) {
				$this->content[$key] = $value;
			}
		}
		
		// Deprecated
		if (array_key_exists('locale_1', $data)) {
			$locale_1 = trim(strip_tags($data['locale_1']));
			if ($locale_1 == '' || strlen($locale_1) > 255) return 'Integrity';
			if ($this->locale_1 != $locale_1) $auditRow['locale_1'] = $this->locale_1 = $locale_1;
		}
		if (array_key_exists('locale_2', $data)) {
			$locale_2 = trim(strip_tags($data['locale_2']));
			if ($locale_2 == '' || strlen($locale_2) > 255) return 'Integrity';
			if ($this->locale_2 != $locale_2) $auditRow['locale_2'] = $this->locale_2 = $locale_2;
		}
		if (array_key_exists('summary', $data)) {
			$summary = $data['summary'];
			if ($this->summary != $summary) $auditRow['summary'] = $this->summary = $summary;
		}
		if (array_key_exists('summary_locale_1', $data)) {
			$summary_locale_1 = $data['summary_locale_1'];
			if ($this->summary_locale_1 != $summary_locale_1) $auditRow['summary_locale_1'] = $this->summary_locale_1 = $summary_locale_1;
		}
		if (array_key_exists('summary_locale_2', $data)) {
			$summary_locale_2 = $data['summary_locale_2'];
			if ($this->summary_locale_2 != $summary_locale_2) $auditRow['summary_locale_2'] = $this->summary_locale_2 = $summary_locale_2;
		}
		if (array_key_exists('image', $data)) {
			$image = $data['image'];
			if ($this->image != $image) $auditRow['image'] = $this->image = $image;
		}
		if (array_key_exists('image_locale_1', $data)) {
			$image_locale_1 = $data['image_locale_1'];
			if ($this->image_locale_1 != $image_locale_1) $auditRow['image_locale_1'] = $this->image_locale_1 = $image_locale_1;
		}
		if (array_key_exists('image_locale_2', $data)) {
			$image_locale_2 = $data['image_locale_2'];
			if ($this->image_locale_2 != $image_locale_2) $auditRow['image_locale_2'] = $this->image_locale_2 = $image_locale_2;
		}
		if (array_key_exists('first_part_id', $data)) {
			$first_part_id = $data['first_part_id'];
			if ($this->first_part_id != $first_part_id) $auditRow['first_part_id'] = $this->first_part_id = $first_part_id;
		}
		if (array_key_exists('url', $data)) {
			$url = trim(strip_tags($data['url']));
			if ($url == '' || strlen($url) > 255) return 'Integrity';
			if ($this->url != $url) $auditRow['url'] = $this->url = $url;
		}
		if (array_key_exists('properties', $data)) {
			$properties = $data['properties'];
			if ($this->properties != $properties) $auditRow['properties'] = $this->properties = $properties;
		}
		if (array_key_exists('properties_locale_1', $data)) {
			$properties_locale_1 = $data['properties_locale_1'];
			if ($this->properties_locale_1 != $properties_locale_1) $auditRow['properties_locale_1'] = $this->properties_locale_1 = $properties_locale_1;
		}
		if (array_key_exists('properties_locale_2', $data)) {
			$properties_locale_2 = $data['properties_locale_2'];
			if ($this->properties_locale_2 != $properties_locale_2) $auditRow['properties_locale_2'] = $this->properties_locale_2 = $properties_locale_2;
		}
		$this->parts = array();
		if (array_key_exists('parts', $data)) {
			foreach ($data['parts'] as $part) {
				$documentPart = new DocumentPart;
				$documentPart->document_id = $this->id;
				$documentPart->loadData($part);
				$this->parts[] = $documentPart;
			}
		}
		$this->audit[] = $auditRow;
		return 'OK';
	}
/*
	public function saveContent()
	{
		DocumentPart::getTable()->multipleDelete(array('document_id' => $this->id));
		$next_part_id = 0;
		for ($i = count($this->parts) - 1; $i >= 0; $i--) {
			$this->parts[$i]->next_part_id = $next_part_id;
			$this->parts[$i]->id = 0;
			$this->parts[$i]->status = 'new';
			$this->parts[$i]->id = DocumentPart::getTable()->save($this->parts[$i]);
			$next_part_id = $this->parts[$i]->id;
		}
		$this->first_part_id = $next_part_id;
	}*/

	/**
	 * Adds a new row in the database.
	 * @return string
	 */
	public function add()
	{
		$this->id = 0;
		Document::getTable()->save($this);
/*		$this->saveContent();
		Document::getTable()->save($this);*/
		return 'OK';
	}
	
	/**
	 * Update an existing row in the database.
	 * If $update_time is provided, an isolation check is performed, such that the current update time in the database is not greater than the one given as an argument.
	 * In such a case the methods does not affect the database and returns 'Isolation', otherwise it returns 'OK'.
	 * @param string $update_time
	 * @return string
	 */
	public function update($update_time)
	{
		$context = Context::getCurrent();

		// Authorization check
//		if ($this->authorization != 'admin' && $this->authorization != 'write') return 'Unauthorized';
		
		// Isolation check
		$document = Document::get($this->id);
		if ($update_time && $document->update_time > $update_time) return 'Isolation';

		$this->saveContent();
		Document::getTable()->save($this);
		return 'OK';
	}
	
	public function saveFile($files, $compress = false/*, $dropbox = null*/) {
		$dropbox = null;
		$context = Context::getCurrent();
		$config = $context->getConfig();
		foreach ($files as $file) {
			if ($file['size'] > $context->getConfig()['maxUploadSize']) $error = 'Size';
			else {
				$name = $file['name'];
				$extension = substr($name, strpos($name, '.')+1);
				$type = $file['type'];

				// Write the link in the database
				if (!$this->destinationPath && $compress && ($extension == 'gif' || $extension == 'png')) {
					$this->mime = 'image/jpeg';
					$this->name = ((strpos($name, '.')) ? substr($name, 0, strpos($name, '.')) : $name).'.jpg';
				} else {
					$this->mime = $type;
					$this->name = $name;
				}

				$adapter = new \Zend\File\Transfer\Adapter\Http();
				if ($this->id) { // $link->id is 0 in demo mode
					// Create the file on the file system with $id as a name
					if ($config['isDemoAccountUpdatable'] || $context->getInstanceId() != 0) $adapter->addFilter('Rename', 'data/documents/'.$this->id);
					if ($adapter->receive($file['name'])) {

						if ($context->getConfig()['compressGifPngToJpg'] && ($extension == 'gif' || $extension == 'png')) {
							$src = 'data/documents/'.$this->id;
							$destination = 'data/documents/'.$this->id.'.jpg';
		
							// Compress the image
							$info = getimagesize($src);
							if ($info['mime'] == 'image/gif')
							{
								$image = imagecreatefromgif($src);
							}
							elseif ($info['mime'] == 'image/png')
							{
								$image = imageCreateFromPng($src);
							}
							//compress and save file to jpg
							imagejpeg($image, $destination, 75);
							unlink('data/documents/'.$this->id);
							rename('data/documents/'.$this->id.'.jpg', 'data/documents/'.$this->id);
						}
/*					
						if ($dropbox) {
    						require_once "vendor/dropbox/dropbox-sdk/lib/Dropbox/autoload.php";
				    		$dropboxSpecs = $context->getConfig('ppitDocument')['dropbox'];
				    		$dbxClient = new \Dropbox\Client($dropboxSpecs['credential'], $dropboxSpecs['clientIdentifier']);
							$f = fopen('data/documents/'.$this->id, "rb");
							$result = $dbxClient->uploadFile($dropbox.$this->name, \Dropbox\WriteMode::add(), $f);
							fclose($f);
						}*/
					}
				}
			}
		}
		return $this->id;
	}

	/**
	 * @param Interaction $interaction
	 * @return string
	 */
/*	public static function processInteraction($data)
	{
		$context = Context::getCurrent();
		if ($data['action'] == 'update' || $data['action'] == 'delete') $document = Document::getWithPath($data['path'].$data['name']);
		elseif ($data['action'] == 'add') $document = Document::instanciate();

		if (array_key_exists('path', $data)) {
			$parent = Document::getWithPath($data['path']);
			if ($parent) $data['parent_id'] = $parent->id;
			else {
				$connection->rollback();
				return 'Consistency';
			}
		}
		$previous = Document::getWithPath($data['path'].$data['name']);
		if ($data['action'] == 'add') {
			if ($previous) return 'Duplicate';
		}
		else {
			if (!$previous) return 'Consistency';
			$document = $previous;
		}

		if ($data['action'] == 'delete') {
			$rc = $document->delete(null);
		}
		else {
			if ($document->loadData($data) != 'OK') throw new \Exception('View error');
			if (!$document->id) $rc = $document->add();
			else $rc = $document->update(null);
		}
		return $rc;
	}*/
	
	public function isUsed($object)
	{
		// Allow or not deleting a community
		if (get_class($object) == 'Model\Community') {
	    	$rootDoc = Document::getTable()->get($object->root_document_id);
	    	if (!$rootDoc) return false;
    		if (Generic::getTable()->cardinality('core_document', array('parent_id' => $rootDoc->id)) > 0) return true;
		}
		return false;
	}
	
	public function isDeletable()
	{
		$context = Context::getCurrent();
	
		// Not deletable if the document is parent of other documents (a directory)
		if (Generic::getTable()->cardinality('core_document', array('status != ?' => 'deleted', 'parent_id' => $this->id)) > 0) return false;
	
		// Check other dependencies
		$config = $context->getConfig();
		foreach($config['ppitCoreDependencies'] as $dependency) {
			if ($dependency->isUsed($this)) return false;
		}

		return true;
	}
	
	public function delete($update_time)
	{
		$context = Context::getCurrent();
	
		// Authorization check
//		if ($this->authorization != 'admin' && $this->authorization != 'write') return 'Unauthorized';
	
		// Isolation check
		$document = Document::get($this->id);
		if ($update_time && $document->update_time > $update_time) return 'Isolation';
		if (!$document->isDeletable()) return 'Consistency';
    	$this->status = 'deleted';
    	Document::getTable()->save($this);
		
		if ($this->type == 'uploaded') {
			// Delete the file on the file system
			unlink($file = 'data/documents/'.$id);
		}
			
		return 'OK';
	}
	
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        throw new \Exception("Not used");
    }

    public static function getTable()
    {
    	if (!Document::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Document::$table = $sm->get(DocumentTable::class);
    	}
    	return Document::$table;
    }
}
