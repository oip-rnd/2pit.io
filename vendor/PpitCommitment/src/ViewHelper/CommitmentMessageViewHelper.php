<?php
namespace PpitCommitment\ViewHelper;

use Zend\View\Model\ViewModel;
use PpitCommitment\Model\Commitment;
use PpitCommitment\Model\Term;
use PpitCore\Model\Context;
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;
use PpitCore\Model\Product;
use PpitCore\Model\ProductOption;

require_once('vendor/TCPDF-master/tcpdf.php');

class CommitmentMessageViewHelper
{	
    public static function render($message, $place)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$html = '';

    	if (array_key_exists('header', $message['template'])) foreach ($message['template']['header']['paragraphs'] as $paragraph) {
    		$arguments = [];
    		if (array_key_exists('params', $paragraph)) {
    			foreach ($paragraph['params'] as $propertyId) $arguments[$propertyId] = $message['data'][$propertyId];
    		}
    		if ($paragraph['type'] == 'br') $html .= sprintf($format, $content)."\n";
    		else {
    			$format = '<' . $paragraph['type'] . '>%s</' . $paragraph['type'] . '>';
	    		if (array_key_exists('label', $paragraph)) $content = vsprintf($context->localize($paragraph['label']), $arguments);
	    		else $content = null;
	    		if ($content) $html .= sprintf($format, $content)."\n";
    		}
    	}

    	if (array_key_exists('body', $message['template'])) foreach ($message['template']['body']['paragraphs'] as $paragraph) {
    		$arguments = [];
    		if (array_key_exists('params', $paragraph)) {
    			foreach ($paragraph['params'] as $propertyId) $arguments[$propertyId] = $message['data'][$propertyId];
    		}
    	    if ($paragraph['type'] == 'br') $html .= sprintf($format, $content)."\n";
    		else {
    			$format = '<' . $paragraph['type'] . '>%s</' . $paragraph['type'] . '>';
	    		if (array_key_exists('label', $paragraph)) $content = vsprintf($context->localize($paragraph['label']), $arguments);
	    		else $content = null;
	    		if ($content) $html .= sprintf($format, $content)."\n";
    		}
    	}

    	if (array_key_exists('footer', $message['template'])) foreach ($message['template']['footer']['paragraphs'] as $paragraph) {
    		$arguments = [];
    		if (array_key_exists('params', $paragraph)) {
    			foreach ($paragraph['params'] as $propertyId) $arguments[$propertyId] = $message['data'][$propertyId];
    		}
    	    if ($paragraph['type'] == 'br') $html .= sprintf($format, $content)."\n";
    		else {
    			$format = '<' . $paragraph['type'] . '>%s</' . $paragraph['type'] . '>';
	    		if (array_key_exists('label', $paragraph)) $content = vsprintf($context->localize($paragraph['label']), $arguments);
	    		else $content = null;
	    		if ($content) $html .= sprintf($format, $content)."\n";
    		}
    	}
    	 
    	return $html;
    }
}
