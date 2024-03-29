<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;
use Zend\View\Model\ViewModel;

require_once('vendor/TCPDF-master/tcpdf.php');

class PdfIndexCardViewHelper
{	
    public static function render($pdf, $place, $account)
    {
    	// Retrieve the context
    	$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get(\Zend\I18n\Translator\TranslatorInterface::class);
    	$cardSpec = $context->getConfig('core_account/indexCard'.(($account->type) ? '/'.$account->type : ''));
    	
    	// create new PDF document
    	$pdf->footer = ($place && $place->legal_footer) ? $place->legal_footer : $context->getConfig('headerParams')['footer']['value'];
    	$pdf->footer_2 = ($place->legal_footer_2) ? $place->legal_footer_2 : ((array_key_exists('footer_2', $context->getConfig('headerParams'))) ? $context->getConfig('headerParams')['footer_2']['value'] : null);
    	 
    	// set document information
    	$pdf->SetCreator(PDF_CREATOR);
    	$pdf->SetAuthor('P-PIT');
    	$pdf->SetTitle('Index Card');
    	$pdf->SetSubject('Index Card');
    	$pdf->SetKeywords('TCPDF, PDF, index card');

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
    	
    	// set font
    	$pdf->SetFont('helvetica', '', 12);
    	
    	// add a page
    	$pdf->AddPage();

    	// Title
    	$text = '<div>&nbsp;</div><div style="text-align: center"><strong>'.$context->localize($cardSpec['title']).' - '.$account->contact_1->n_fn.'</strong></div>';
    	$pdf->writeHTML($text, true, 0, true, 0);
    	$pdf->Ln(10);

    	// Report references
		$pdf->SetFillColor(255, 255, 255);
//    	$pdf->SetTextColor(255);
    	$pdf->SetDrawColor(255, 255, 255);
//    	$pdf->SetDrawColor(128, 0, 0);
    	$pdf->SetLineWidth(0.2);
    	$pdf->SetFont('', '', 9);
    	foreach($cardSpec['header'] as $propertyId => $unused) {
    		if ($propertyId == 'date') $value = $context->decodeDate(date('Y-m-d'));
    		else {
				$property = $context->getConfig('core_account/'.$account->type.'/property/'.$propertyId);
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if ($propertyId == 'place_id') $value = ($place) ? $place->caption : '';
				elseif ($propertyId == 'name') $value = $account->contact_1->n_fn;
	    		elseif ($property['type'] == 'date') $value = $context->decodeDate($account->properties[$propertyId]);
	    		elseif ($property['type'] == 'number') $value = $context->formatFloat($account->properties[$propertyId], 2);
	    		elseif ($property['type'] == 'select') {
	    			$value = array();
	    			foreach (explode(',', $account->properties[$propertyId]) as $propertyId) {
		    			if (array_key_exists($propertyId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$propertyId]);
	    			}
	    			$value = implode(', ', $value);
	    		}
	    		else $value = $account->properties[$propertyId];
    		}
	    	$pdf->MultiCell(30, 5, '<strong>'.$context->localize($property['labels']).'</strong>', 1, 'L', 1, 0, '', '', true, 0, true);
	    	$pdf->MultiCell(5, 5, ':', 1, 'L', 1, 0, '', '', true);
	    	$pdf->MultiCell(145, 5, $value, 1, 'L', 0, 1, '' ,'', true);
    	}

	    $pdf->SetFont('', '', 10);
	    $pdf->Ln();
	    $pdf->SetDrawColor(0, 0, 0);

	    $rows = '';
		$title = $context->getConfig('core_account/'.$account->type.'/property/'.$cardSpec['1st-column']['title']);
	    foreach($cardSpec['1st-column']['rows'] as $propertyId => $unused) {
			$property = $context->getConfig('core_account/'.$account->type.'/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if ($propertyId == 'name') $value = $account->name;
			elseif ($property['type'] == 'date') $value = $context->decodeDate($account->properties[$propertyId]);
			elseif ($property['type'] == 'number') $value = $context->formatFloat($account->properties[$propertyId], 2);
			elseif ($property['type'] == 'select') {
    			$value = array();
    			foreach (explode(', ', $account->properties[$propertyId]) as $propertyId) {
	    			if (array_key_exists($propertyId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$propertyId]);
    			}
    			$value = implode(',', $value);
			}
			else $value = $account->properties[$propertyId];

			$rows .= sprintf('<tr><td style="width: 40%%; font-weight: bold">%s</td><td style="width: 60%%;">%s</td></tr>', $context->localize($property['labels']), $value);
	    }
	    $table1 = sprintf('<table class="table note-report"><tr><th style="width: 100%%">%s</th></tr>%s</table>', $context->localize($title['labels']), $rows);
	    $rows = '';
	    $title = $context->getConfig('core_account/'.$account->type.'/property/'.$cardSpec['2nd-column']['title']);
	    foreach($cardSpec['2nd-column']['rows'] as $propertyId => $unused) {
	    	$property = $context->getConfig('core_account/'.$account->type.'/property/'.$propertyId);
	    	if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			if ($propertyId == 'name') $value = $account->name;
			elseif ($property['type'] == 'date') $value = $context->decodeDate($account->properties[$propertyId]);
			elseif ($property['type'] == 'number') $value = $context->formatFloat($account->properties[$propertyId], 2);
			elseif ($property['type'] == 'select') {
    			$value = array();
    			foreach (explode(',', $account->properties[$propertyId]) as $propertyId) {
	    			if (array_key_exists($propertyId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$propertyId]);
    			}
    			$value = implode(', ', $value);
			}
			else $value = $account->properties[$propertyId];

			$rows .= sprintf('<tr><td style="width: 40%%; font-weight: bold">%s</td><td style="width: 60%%;">%s</td></tr>', $context->localize($property['labels']), $value);
	    }
	    $table2 = sprintf('<table class="table note-report"><tr><th style="width: 100%%">%s</th></tr>%s</table>', $context->localize($title['labels']), $rows);
	     
//	    $text = $cardSpec['pdfDetailStyle'].sprintf('<table><tr><td style="width: 48%%">%s</td><td style="width: 4%%">&nbsp;</td><td style="width: 48%%">%s</td></tr></table>', $table1, $table2);
	    $text = $cardSpec['pdfDetailStyle'].sprintf('%s<br><br>%s', $table1, $table2);
	     
	    $pdf->writeHTML($text, true, 0, true, 0);
	    
	    $pdf->SetDrawColor(255, 255, 255);
		
    	// Close and output PDF document
    	// This method has several options, check the source code documentation for more information.
    	return $pdf;
    }
}
