<?php
namespace PpitCore\Model;

use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Document
{
	/**
	 * This section is the business logic part of documents. It is functional oriented and has unit-testing associated
	 */
	
	public static $model = [
		'entities' => [
			'core_document' => 	['table' => 'core_document'],
		],
		'properties' => [
			'status' => 				['entity' => 'core_document', 'column' => 'status'],
			'type' => 					['entity' => 'core_document', 'column' => 'type'],
			'place_id' => 				['entity' => 'core_document', 'column' => 'place_id'],
			'folder' => 				['entity' => 'core_document', 'column' => 'folder'],
			'identifier' => 			['entity' => 'core_document', 'column' => 'identifier'],
			'name' => 					['entity' => 'core_document', 'column' => 'name'],
			'acl' => 					['entity' => 'core_document', 'column' => 'acl'],
			'mime' => 					['entity' => 'core_document', 'column' => 'mime'],
			'content' => 				['entity' => 'core_document', 'column' => 'content'],
			'binary_content' => 		['entity' => 'core_document', 'column' => 'binary_content'],
		],
	];

	/**
	 * Returns a dictionary of each property associated with its description contextual to the current instance config.
	 */
	public static function getConfig($type)
	{
		// Retrieve the context
		$context = Context::getCurrent();

		// Retrieve the properties description defined in the current instance config for the given account type
		$description = $context->getConfig('document/'.$type);
		
		// If no description is found for the given type retrieve the properties description for the generic type
		if (!$description) $description = $context->getConfig('document/generic');
		
		// Construct the resulting dictionary for each defined property
		$properties = array();
		foreach($description['properties'] as $propertyId) {
		
			// Retrieve the property description according to the given type, defaulting to the generic type
			$property = $context->getConfig('document/'.$type.'/property/'.$propertyId);
			if (!$property) $property = $context->getConfig('document/generic/property/'.$propertyId);
		
			// Overwrite the description with the referred description for non-inline property definition
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
		
			if (!array_key_exists('private', $property)) $property['private'] = false;
				
			// Cache the place list retrieved from the database for the current instance in the place_id property description
			if ($propertyId == 'place_id') {
				$property['modalities'] = array();
				foreach (Place::getList(array()) as $place) $property['modalities'][$place->id] = $place->caption;
			}
			
			$properties[$propertyId] = $property;
		}
		return $properties;
	}
	
	/**
	 * Returns the restricted dictionary expected by the search engine
	 */
	public static function getConfigSearch($type, $configProperties)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the search properties defined in the current instance config for the given account type
		$configSearch = $context->getConfig('document/search/' . $type);

		// If no search description is found for the given type retrieve the search properties for the generic type
		if (!$configSearch) $configSearch = $context->getConfig('document/search/generic');
		
		// Construct the resulting dictionary for each search property
		$properties = array();
		foreach ($configSearch['properties'] as $propertyId => $options) {
	
			// Retrieve the property description from the whole properties dictionary and merge it with the search options for this property
			$property = $configProperties[$propertyId];
			$properties[$propertyId] = $property;
			$properties[$propertyId]['options'] = $options;
		}
	
		$configSearch['properties'] = $properties;
	
		// Return the search restricted dictionary
		return $configSearch;
	}
	
	/**
	 * Returns the restricted dictionary to display on a list view
	 */
	public static function getConfigList($type, $configProperties)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the list properties defined in the current instance config for the given account type
		$configList = $context->getConfig('document/list/' . $type);

		// If no list description is found for the given type retrieve the list properties for the generic type
		if (!$configList) $configList = $context->getConfig('document/list/generic');
		
		// Construct the resulting dictionary for each list property
		$properties = array();
		foreach ($configList['properties'] as $propertyId => $options) {
	
			// Retrieve the property description from the whole properties dictionary and merge it with the list options for this property
			$property = $configProperties[$propertyId];
			$properties[$propertyId] = $property;
			$properties[$propertyId]['options'] = $options;
		}
		$configList['properties'] = $properties;
	
		// Return the search restricted dictionary
		return $configList;
	}
	
	/**
	 * Returns the restricted dictionary to diplay on a detailed view
	 */
	public static function getConfigDetail($type, $configProperties)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the updatable properties defined in the current instance config for the given account type
		$configDetail = $context->getConfig('document/detail/' . $type);

		// If no update description is found for the given type retrieve the updatable properties for the generic type
		if (!$configDetail) $configDetail = $context->getConfig('document/detail/generic');

		// Construct the resulting dictionary for each updatable property
		$properties = array();
		foreach ($configDetail['properties'] as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
	
				// Retrieve the property description from the whole properties dictionary and merge it with the update options for this property
				$property = $configProperties[$propertyId];
				$property['options'] = $options;
				$properties[$propertyId] = $property;
			}
		}
		$configDetail['properties'] = $properties;
	
		// Return the updatable restricted dictionary
		return $configDetail;
	}
	
	/**
	 * Returns the restricted dictionary of the properties that can be updated in group
	 */
	public static function getConfigGroupUpdate($type, $configProperties)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the group-updatable properties defined in the current instance config for the given account type
		$configGroupUpdate = $context->getConfig('document/groupUpdate/' . $type);

		// If no update description is found for the given type retrieve the group-updatable properties for the generic type
		if (!$configGroupUpdate) $configGroupUpdate = $context->getConfig('document/groupUpdate/generic');
		
		// Construct the resulting dictionary for each group-updatable property
		$properties = array();
		foreach ($configGroupUpdate['properties'] as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
	
				// Retrieve the property description from the whole properties dictionary and merge it with the group-update options for this property
				$property = $configProperties[$propertyId];
				$property['options'] = $options;
				$properties[$propertyId] = $property;
			}
		}
		$configGroupUpdate['properties'] = $properties;
	
		// Return the group-updatable restricted dictionary
		return $configGroupUpdate;
	}
	
	/**
	 * Returns the restricted dictionary of the properties that can be exported
	 */
	public static function getConfigExport($type, $configProperties)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Retrieve the exportable properties defined in the current instance config for the given account type
		$configExport = $context->getConfig('document/export/' . $type);

		// If no export description is found for the given type retrieve the exportable properties for the generic type
		if (!$configExport) $configExport = $context->getConfig('document/export/generic');

		// Construct the resulting dictionary for each exportable property
		$properties = array();
		foreach ($configExport as $propertyId => $options) {
			if (array_key_exists($propertyId, $configProperties)) {
	
				// Retrieve the property description from the whole properties dictionary and merge it with the export options for this property
				$property = $configProperties[$propertyId];
				$properties[$propertyId] = $property;
				$properties[$propertyId]['options'] = $options;
			}
		}
		$configExport['properties'] = $properties;
	
		// Return the exportable restricted dictionary
		return $configExport;
	}
	
	/**
	 * Returns the dictionary of the properties along with global options and
	 * along with the restricted dictionary for search, list, detail, group-update and export
	 */
	public static function getDescription($type)
	{
		// Retrieve the context
		$context = Context::getCurrent();
	
		// Construct the whole description by aggregating all the dictionnaries and options
		$description = array();
		$description['properties'] = Document::getConfig($type);
		$description['search'] = Document::getConfigSearch($type, $description['properties']);
		$description['list'] = Document::getConfigList($type, $description['properties']);
		$description['detail'] = Document::getConfigDetail($type, $description['properties']);
		$description['groupUpdate'] = Document::getConfigGroupUpdate($type, $description['properties']);
		$description['export'] = Document::getConfigExport($type, $description['properties']);
	
		// Return the whole description
		return $description;
	}
	
	public static function getSelect($type, $columns = [], $filters = [], $order = ['name'], $limit = null)
	{
		// Retrieve the context
		$context = Context::getCurrent();
		$config = Document::getConfig($type);
		
		// Construct the select object and define the joins
		$select = Document::getTable()->getSelect();
	
		// Specify the columns to retrieve (* if none)
		if ($columns) $select->columns(array_merge(['id'], $columns));
	
		// Normalize the order by replacing the preceding '+' or '-' by trailing 'ASC' or 'DESC' and specify the order clause
		foreach ($order as &$criterion) {
			if (substr($criterion, 0, 1) == '-') $criterion = substr($criterion, 1) . ' DESC';
			else $criterion .= ' ASC';
		}
		$select->order($order);
	
		$where = new Where;
		$where->notEqualTo('core_document.status', 'deleted');
	
		// Set the filters
		foreach ($filters as $propertyId => $predicate) {
			$operator = $predicate[0];
			$value = $predicate[1];
			$property = $config[$propertyId];
			$entity = Document::$model['properties'][$propertyId]['entity'];
			$column = Document::$model['properties'][$propertyId]['column'];
	
/*			if ($property['type'] == 'select') {
				if (array_key_exists('multiple', $property) && $property['multiple']) $where->like($entity . '.' . $column, '%' . $value . '%');
				else $where->equalTo($entity.'.'.$column, $value);
			}
			elseif ($property['type'] == 'multiselect') $where->like($entity . '.' . $column, '%' . $value . '%');
			else*/if (in_array($property['type'], ['date', 'datetime']) && !$value) $where->isNull($entity . '.' . $propertyId);
			elseif ($operator == 'eq') $where->equalTo($entity . '.' . $column, $value);
			elseif ($operator == 'ne') $where->notEqualTo($entity . '.' . $column, $value);
			elseif ($operator == 'gt') $where->greaterThan($entity . '.' . $propertyId, $value);
			elseif ($operator == 'ge') $where->greaterThanOrEqualTo($entity . '.' . $propertyId, $value);
			elseif ($operator == 'lt') $where->lessThan($entity . '.' . $propertyId, $value);
			elseif ($operator == 'le') $where->lessThanOrEqualTo($entity . '.' . $propertyId, $value);
			elseif ($operator == 'in') {
				$values = array_slice($predicate, 1);
				$where->in($entity . '.' . $column, $values);
			}
			elseif ($operator == 'between') {
				$value = $predicate[2];
				$where->between($entity . '.' . $column, $value, $value2);
			}
			elseif ($operator == 'like') $where->like($entity . '.' . $column, '%' . $value . '%');
			elseif ($operator == 'null') $where->isNull($entity . '.' . $column);
			elseif ($operator == 'not_null') $where->isNotNull($entity . '.' . $column);
		}
	
		// Filter on authorized perimeter
		if (array_key_exists('p-pit-admin', $context->getPerimeters())) {
			foreach ($context->getPerimeters()['p-pit-admin'] as $propertyId => $values) {
				if (array_key_exists($propertyId, Document::$model['properties'])) {
					$entity = Document::$model['properties'][$propertyId]['entity'];
					$column = Document::$model['properties'][$propertyId]['column'];
					$where->in($entity . '.' . $column, $values);
				}
			}
		}

		$select->where($where);
		
		// Set the limit or no-limit
		if ($limit) $select->limit((int) $limit);
		 
		// Return the SQL select
		return $select;
	}
	
	/**
	 * This section is intended to be progressively deprecated in 2pit2 while the whole framework is to be simplified and
	 * less and less dependant of the Zend historical foundation
	 */
	
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
    public $is_deletable;
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

//		$this->saveContent();
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
