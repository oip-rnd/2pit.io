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
    public static function renderHtml($message, $place)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
    	
    	$html = '';
    	
    	if (array_key_exists('style', $message['template'])) $html .= $context->localize($message['template']['style']);

    	foreach ($message['template']['sections'] as $section) {

    		if (array_key_exists('class', $section) && $section['class'] == 'table') {

    			$html .= "<table>\n";

    			$html .= "  <tr>\n";
    			 
    			foreach ($section['columns'] as $column) {

    				// Format the markup possibly with a class attribute
    				$format = '    <th';
    				if (array_key_exists('class', $column)) $format .= ' class="' . $column['class'] . '"';
    				if (array_key_exists('style', $column)) $format .= ' style="' . $column['style'] . '"';
    				$format .= '>';
    				$format .= '%s';
    				$format .= "</th>\n";
    					
    				$html .= sprintf($format, $context->localize($column['header']));
    			}

    			$html .= "  </tr>\n";

    			for ($i = 0; $i < $message['data']['occurrence_number']; $i++) {
    				
    				$html .= "  <tr>\n";

    				foreach ($section['columns'] as $column) {
			    		$arguments = [];
			    		if (array_key_exists('params', $column)) {
			    			foreach ($column['params'] as $prefixedPropertyId) {
			    				if (strpos($prefixedPropertyId, ':')) {
			    					$arrayPropertyId = explode(':', $prefixedPropertyId);
			    					$prefix = $arrayPropertyId[0];
			    					$propertyId = $arrayPropertyId[1];
			    				}
			    				else {
			    					$prefix = null;
			    					$propertyId = $prefixedPropertyId;
			    				}
			    				if ($prefix) $arguments[$propertyId] = $message['data'][$prefixedPropertyId . '_' . $i];
			    				else $arguments[$propertyId] = $message['data'][$propertyId];
			    			}
			    		}
			    		
		    			// Format the markup possibly with a class attribute 
			    		$format = '    <td';
			    		if (array_key_exists('class', $column)) $format .= ' class="' . $column['class'] . '"';
			    		if (array_key_exists('style', $column)) $format .= ' style="' . $column['style'] . '"';
			    		$format .= '>';
						$format .= '%s';
			    		$format .= "</td>\n";
			
		    			if (array_key_exists('label', $column)) $content = trim(vsprintf($context->localize($column['label']), $arguments));
			    		else $content = null;
			    		if ($content != '') $html .= sprintf($format, $content);
		    		}

		    		$html .= "  </tr>\n";
    			}
    				
    			$html .= "</table>\n";
    		}
    		
    		else {
	    		
	    		if (array_key_exists('class', $section) && $section['class'] == 'box-title') $html .= '<div style="border: 1px solid black; ">';
	    		
	    		foreach ($section['paragraphs'] as $paragraph) {
		    		$arguments = [];
		    		if (array_key_exists('params', $paragraph)) {
		    			foreach ($paragraph['params'] as $propertyId) $arguments[$propertyId] = $message['data'][$propertyId];
		    		}
		    		
		    		if (array_key_exists('class', $paragraph) && $paragraph['class'] == 'addressee') {
		    			$format = '<table class="addressee"><tr><td width="60%%"></td><td width="40%%">%s</td></tr></table>';
		    		}
		    		else {
			    		// Format the markup possibly with a class attribute 
			    		$format = '<' . $paragraph['type'];
			    		if (array_key_exists('class', $paragraph)) $format .= ' class="' . $paragraph['class'] . '"';
			    		if (array_key_exists('style', $paragraph)) $format .= ' style="' . $paragraph['style'] . '"';
			    		$format .= '>';
						$format .= '%s';
			    		$format .= '</' . $paragraph['type'] . '>';
		    		}
		
		    		if ($paragraph['type'] == 'br') $html .= '<p></p>'."\n";
		    		else {
			    		if (array_key_exists('label', $paragraph)) $content = trim(vsprintf($context->localize($paragraph['label']), $arguments));
			    		else $content = null;
			    		if ($content != '') $html .= sprintf($format, $content)."\n";
		    		}
		    	}
	    	    		
	    		if (array_key_exists('class', $section) && $section['class'] == 'box-title') $html .= '</div>';
    		}
    	}
    	 
    	return $html;
    }
    
    public static function renderPdf($pdf, $message, $place)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();

    	// Render the HTML content to incorporate in the PDF document
    	$html = CommitmentMessageViewHelper::renderHtml($message, $place);

    	// create new PDF document
    	$pdf->footer = ($place->legal_footer) ? $place->legal_footer : $context->getConfig('headerParams')['footer']['value'];
    	$pdf->footer_2 = ($place->legal_footer_2) ? $place->legal_footer_2 : ((array_key_exists('footer_2', $context->getConfig('headerParams'))) ? $context->getConfig('headerParams')['footer_2']['value'] : null);
    	
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-Pit');
    	$pdf->SetTitle($message['identifier']);
    	$pdf->SetKeywords('P-Pit, PDF');
    	
    	// set default header data
    	if ($place && $place->banner_src) $pdf->SetHeaderData($place->banner_src, ($place->banner_width) ? $place->banner_width : $context->getConfig('corePlace')['properties']['banner_width']['maxValue']);
    	elseif (array_key_exists('advert', $context->getConfig('headerParams'))) {
    		$pdf->SetHeaderData('logos/'.$context->getInstance()->caption.'/'.$context->getConfig('headerParams')['advert'], $context->getConfig('headerParams')['advert-width']);
    	}
    	
    	// set header and footer fonts
    	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    	
    	// set default monospaced font
    	$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
    	 
    	// set margins
    	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    	
    	// set auto page breaks
    	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    	 
    	// set image scale factor
    	$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    	 
    	// set some language-dependent strings (optional)
    	if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    		require_once(dirname(__FILE__).'/lang/eng.php');
    		$pdf->setLanguageArray($l);
    	}
    	 
    	// set additional information
    	$info = array(
    		'Name' => $message['identifier'],
    		'Location' => 'Office',
    		'ContactInfo' => 'https://www.p-pit.fr',
    	);
    	 
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	 
    	// add a page
    	$pdf->AddPage();
    	
    	// Invoice header
    	if (array_key_exists('header', $message)) {
    		$pdf->SetFont('', 'B', 8);
    		$pdf->writeHTML($message['header'], true, 0, true, 0);
    	}
    	 
    	$pdf->SetFont('', '', 12);
    	$pdf->writeHTML($html, true, 0, true, 0);

    	return $pdf;
    }
}
