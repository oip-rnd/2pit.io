<?php
namespace PpitCore\Model;

use Zend\Db\Sql\Where;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterInterface;

class Product
{
    public $id;
    public $status;
    public $community_id;
    public $type;
    public $brand;
    public $identifier;
    public $caption;
    public $description;
    public $is_available;
    public $property_1;
    public $property_2;
    public $property_3;
    public $property_4;
    public $property_5;
    public $property_6;
    public $property_7;
    public $property_8;
    public $property_9;
    public $property_10;
    public $variants;
    public $tax_1_share;
    public $tax_2_share;
    public $tax_3_share;
    public $update_time;
    
    // Deprecated
//    public $product_category_id;
    public $properties;
    public $criteria;
    public $prices;
    
    // Additional fields
//    public $product_category;

    // Transient properties
    public $optionList;
    public $optionMatrix;
    
    protected $inputFilter;

    // Static fields
    private static $table;
    
    public function __construct()
    {
    	$config = Context::getCurrent()->getConfig();
    	$this->properties = array();
    	$this->prices = array();
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    public function exchangeArray($data)
    {
        $this->id = (isset($data['id'])) ? $data['id'] : null;
        $this->status = (isset($data['status'])) ? $data['status'] : null;
//        $this->community_id = (isset($data['community_id'])) ? $data['community_id'] : null;
        $this->type = (isset($data['type'])) ? $data['type'] : null;
        $this->brand = (isset($data['brand'])) ? $data['brand'] : null;
        $this->identifier = (isset($data['identifier'])) ? $data['identifier'] : null;
        $this->caption = (isset($data['caption'])) ? $data['caption'] : null;
        $this->description = (isset($data['description'])) ? $data['description'] : null;
        $this->is_available = (isset($data['is_available'])) ? $data['is_available'] : null;
        $this->property_1 = (isset($data['property_1'])) ? $data['property_1'] : null;
        $this->property_2 = (isset($data['property_2'])) ? $data['property_2'] : null;
        $this->property_3 = (isset($data['property_3'])) ? $data['property_3'] : null;
        $this->property_4 = (isset($data['property_4'])) ? $data['property_4'] : null;
        $this->property_5 = (isset($data['property_5'])) ? $data['property_5'] : null;
        $this->property_6 = (isset($data['property_6'])) ? $data['property_6'] : null;
        $this->property_7 = (isset($data['property_7'])) ? $data['property_7'] : null;
        $this->property_8 = (isset($data['property_8'])) ? $data['property_8'] : null;
        $this->property_9 = (isset($data['property_9'])) ? $data['property_9'] : null;
        $this->property_10 = (isset($data['property_10'])) ? $data['property_10'] : null;
        $this->variants = (isset($data['variants'])) ? json_decode($data['variants'], true) : array();
        $this->tax_1_share = (isset($data['tax_1_share'])) ? $data['tax_1_share'] : null;
        $this->tax_2_share = (isset($data['tax_2_share'])) ? $data['tax_2_share'] : null;
        $this->tax_3_share = (isset($data['tax_3_share'])) ? $data['tax_3_share'] : null;
        $this->update_time = (isset($data['update_time'])) ? $data['update_time'] : array();

        // Deprecated
//        $this->product_category_id = (isset($data['product_category_id'])) ? $data['product_category_id'] : null;
        $this->properties = (isset($data['properties'])) ? json_decode($data['properties'], true) : array();
        $this->criteria = (isset($data['criteria'])) ? $data['criteria'] : null;
        $this->prices = (isset($data['prices'])) ? json_decode($data['prices'], true) : array();
        
        // Additional fields
  //      $this->product_category = (isset($data['product_category'])) ? $data['product_category'] : null;
    }

    public function getProperties()
    {
    	$data = array();
    	$data['id'] = (int) $this->id;
    	$data['status'] = $this->status;
//    	$data['community_id'] = (int) $this->community_id;
    	$data['type'] = $this->type;
    	$data['brand'] = $this->brand;
    	$data['identifier'] = $this->identifier;
    	$data['caption'] = $this->caption;
    	$data['description'] = $this->description;
    	$data['is_available'] = $this->is_available;
    	$data['property_1'] = $this->property_1;
    	$data['property_2'] = $this->property_2;
    	$data['property_3'] = $this->property_3;
    	$data['property_4'] = $this->property_4;
    	$data['property_5'] = $this->property_5;
    	$data['property_6'] = $this->property_6;
    	$data['property_7'] = $this->property_7;
    	$data['property_8'] = $this->property_8;
    	$data['property_9'] = $this->property_9;
    	$data['property_10'] = $this->property_10;
	    $data['variants'] = json_encode($this->variants);
	    $data['tax_1_share'] = $this->tax_1_share;
	    $data['tax_2_share'] = $this->tax_2_share;
	    $data['tax_3_share'] = $this->tax_3_share;
	     
	    // Deprecated
//    	$data['product_category_id'] = (int) $this->product_category_id;
	    $data['properties'] = json_encode($this->properties);
	    $data['criteria'] = $this->criteria;
	    $data['prices'] = json_encode($this->prices);
	    $this->properties = $data;
	    return $data;
    }

    public function toArray()
    {
    	return $this->getProperties();
    }
    
    public static function getList($type, $params, $order = '+caption', $limit = 50, $columns = null, $pageNumber = false, $itemCountPerPage = false)
    {
    	$context = Context::getCurrent();
    	$order = explode(',', $order);
    	foreach ($order as &$criterion) $criterion = substr($criterion, 1).' '.((substr($criterion, 0, 1) == '-') ? 'DESC' : 'ASC');
    	$select = Product::getTable()->getSelect()
    		->order($order);
		if ($columns) $select->columns($columns);

		$where = new Where();
    	$where->notEqualTo('core_product.status', 'deleted');

    	// Filter on type
    	if ($type) $where->equalTo('type', $type);

		// Set the filters
    	if (isset($params['identifier'])) $where->like('identifier', '%'.$params['identifier'].'%');
		if (isset($params['caption'])) $where->like('caption', '%'.$params['caption'].'%');
		if (isset($params['min_price'])) $where->greaterThanOrEqualTo('prices', $params['min_price']);
		if (isset($params['max_price'])) $where->lessThanOrEqualTo('prices', $params['max_price']);
		for ($i = 1; $i < 20; $i++) {
			if (isset($params['property_'.$i])) $where->like('property_'.$i, '%'.$params['property_'.$i].'%');
			if (isset($params['min_property_'.$i])) $where->greaterThanOrEqualTo('property_'.$i, $params['min_property_'.$i]);
			if (isset($params['max_property_'.$i])) $where->lessThanOrEqualTo('property_'.$i, $params['max_property_'.$i]);
		}

		$select->where($where);
    	$cursor = Product::getTable()->selectWith($select, ($pageNumber) ? new Product : null);
    		if ($pageNumber) {
			$cursor->setCurrentPageNumber($pageNumber);
			$cursor->setItemCountPerPage($itemCountPerPage);
		}
    	
		$products = array();
    	foreach ($cursor as $product) {
    		$product->getProperties();
	    	$products[] = $product;
    	}
		return $products;
    }

    public static function instanciate()
    {
		$product = new Product;
		$product->id = 0;
		$product->status = 'new';
		$product->is_available = true;
		return $product;
    }
/*
    public static function getListForOrder($community_id, $productCategories)
    {
    	$context = Context::getCurrent();

    	// Construct the subquery of supplyers visible to this customer
    	$supplyers = Contract::getTable()->getSelect()
    		->columns(array('supplyer_community_id'))
    		->where(array('customer_community_id' => $context->getCommunityId(), 'function' => 'order'));
//    	$supplyers = Contract::getTable()->selectWith($select);
    	
		$select = Product::getTable()->getSelect();
		$where = new Where;
		$where->in('community_id', $supplyers);
		$where->equalTo('is_available', true);
		$select->where($where);
		$cursor = Product::getTable()->selectWith($select);
		$products = array();
		foreach ($cursor as $product) {
			if (array_key_exists($product->product_category_id, $productCategories)) {
				
				// Retrieve the available options for this product
				$select = ProductOption::getTable()->getSelect()->where(array('product_id' => $product->id))
					->where(array('is_available' => true))
					->order(array('caption'));
				$cursor2 = ProductOption::getTable()->selectWith($select);
				$product->optionList = array();
				foreach($cursor2 as $option) {
				
					$option->selected = false;
					$product->optionList[$option->id] = $option;
				}
	
				// Retrieve the option matrix
				$select = ProductOptionMatrix::getTable()->getSelect()
					->where(array('product_id' => $product->id));
				$cursor2 = ProductOptionMatrix::getTable()->selectWith($select);
				$product->optionMatrix = array();
				foreach($cursor2 as $cell) $product->optionMatrix[] = $cell;

				$products[] = $product;
			}
		}

		return $products;
    }*/

    public static function get($id, $column = 'id')
    {
    	$product = Product::getTable()->get($id, $column);
//    	$product->product_category = ProductCategory::get($product->product_category_id);
		
		// Retrieve the available options for this product
		if ($product) {
			$select = ProductOption::getTable()->getSelect()->where(array('product_id' => $product->id))
				->where(array('is_available' => true))
				->order(array('caption'));
			$cursor2 = ProductOption::getTable()->selectWith($select);
			$product->optionList = array();
			foreach($cursor2 as $option) {
			
				$option->selected = false;
				$product->optionList[$option->id] = $option;
			}
	
			// Retrieve the option matrix
/*			$select = ProductOptionMatrix::getTable()->getSelect()
				->where(array('product_id' => $product->id));
			$cursor2 = ProductOptionMatrix::getTable()->selectWith($select);
			$product->optionMatrix = array();
			foreach($cursor2 as $cell) $product->optionMatrix[] = $cell;*/
		}

    	return $product;
    }

    public function loadData($data)
    {
    	$config = Context::getCurrent()->getConfig();

    	// Retrieve the data from the request
    	if (array_key_exists('status', $data)) {
	    	$this->status = trim(strip_tags($data['status']));
    		if (!$this->status || strlen($this->status) > 255) return 'Integrity';
    	}
        if (array_key_exists('type', $data)) {
	    	$this->type = trim(strip_tags($data['type']));
    		if (!$this->type || strlen($this->type) > 255) return 'Integrity';
    	}
        if (array_key_exists('brand', $data)) {
    		$this->brand = trim(strip_tags($data['brand']));
        	if (strlen($this->brand) > 255) return 'Integrity';
    	}
        if (array_key_exists('identifier', $data)) {
		    $this->identifier = trim(strip_tags($data['identifier']));
            if (!$this->identifier || strlen($this->identifier) > 255) return 'Integrity';
    	}
        if (array_key_exists('caption', $data)) {
	    	$this->caption = trim(strip_tags($data['caption']));
        	if (!$this->caption || strlen($this->caption) > 255) return 'Integrity';
    	}
        if (array_key_exists('description', $data)) {
	    	$this->description = trim(strip_tags($data['description']));
        	if (strlen($this->description) > 2047) return 'Integrity';
    	}
        if (array_key_exists('is_available', $data)) $this->is_available = (int) $data['is_available'];
    
    	// Properties
        if (array_key_exists('property_1', $data)) $this->property_1 = $data['property_1'];
        if (array_key_exists('property_2', $data)) $this->property_2 = $data['property_2'];
        if (array_key_exists('property_3', $data)) $this->property_3 = $data['property_3'];
        if (array_key_exists('property_4', $data)) $this->property_4 = $data['property_4'];
        if (array_key_exists('property_5', $data)) $this->property_5 = $data['property_5'];
        if (array_key_exists('property_6', $data)) $this->property_6 = $data['property_6'];
        if (array_key_exists('property_7', $data)) $this->property_7 = $data['property_7'];
        if (array_key_exists('property_8', $data)) $this->property_8 = $data['property_8'];
        if (array_key_exists('property_9', $data)) $this->property_9 = $data['property_9'];
        if (array_key_exists('property_10', $data)) $this->property_10 = $data['property_10'];
        
    	// Variants
        if (array_key_exists('variants', $data)) $this->variants = $data['variants'];

        if (array_key_exists('tax_1_share', $data)) $this->tax_1_share = (float) $data['tax_1_share'];
        if (array_key_exists('tax_2_share', $data)) $this->tax_2_share = (float) $data['tax_2_share'];
        if (array_key_exists('tax_3_share', $data)) $this->tax_3_share = (float) $data['tax_3_share'];

        return 'OK';
    }

    public function add()
    {
		Product::getTable()->save($this);
		return 'OK';
    }

    /**
     * Load data from a structure to this Product and add it in the database
     */
    public function loadAndAdd($data)
    {
    	$context = Context::getCurrent();
    
    	// Load the data
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', 'product->load: '.$rc];
    
    	// Save the data
    	$this->add();
    	if ($rc != 'OK') return ['500', 'product->add: '.$rc];
    
    	return ['200', $this->id];
    }
    
    public function update($update_time)
    {
		$product = Product::getTable()->get($this->id);
		if ($update_time && $product->update_time > $update_time) return 'Isolation';
		Product::getTable()->save($this);
		return 'OK';
    }

    /**
     * Load data from a structure to this Product and update the database
     */
    public function loadAndUpdate($data)
    {
    	$context = Context::getCurrent();
    
    	// Load the data
    	$rc = $this->loadData($data);
    	if ($rc != 'OK') return ['500', $rc];
    	 
    	// Save the data
    	$this->update(null);
    	if ($rc != 'OK') return ['500', 'product->update: '.$rc];
    	 
    	return ['200', $this->identifier];
    }
    
    public function isDeletable()
    {
    	$config = Context::getCurrent()->getConfig();
    	foreach($config['ppitCoreDependencies'] as $dependency) {
    		if ($dependency->isUsed($this)) return false;
    	}
    	return true;
    }

    public function delete($update_time)
    {
    	$product = Product::getTable()->get($this->id);
    	if ($product->update_time > $update_time) return 'Isolation';
    	
    	// Delete options related to the product
    	ProductOption::getTable()->multipleDelete(array('product_id' => $this->id));
    	
    	// Delete the product
    	Product::getTable()->delete($this->id);

    	return 'OK';
    }

    // Add content to this method:
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
		return $this->inputFilter;
    }

    public static function getTable()
    {
    	if (!Product::$table) {
    		$sm = Context::getCurrent()->getServiceManager();
    		Product::$table = $sm->get(ProductTable::class);
    	}
    	return Product::$table;
    }
}
