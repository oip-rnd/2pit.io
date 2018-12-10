<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class SsmlCommitmentViewHelper
{
	public static function formatXls($description, $workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get(\Zend\I18n\Translator\TranslatorInterface::class);

		$title = (isset ($context->getConfig('commitment/search')['title']) ? $context->localize($context->getConfig('commitment/search')['title']) : $translator->translate('Accounts', 'ppit-commitment', $context->getLocale()));
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		foreach($description['export'] as $propertyId => $property) {
			$colName = $property['options'];
			$sheet->setCellValue($colName.'1', $context->localize($property['labels']));
		}
		
		$j = 1;
		foreach ($view->commitments as $commitment) {
			
	    	$taxExemptAmount = $commitment->amount - $commitment->taxable_1_amount - $commitment->taxable_2_amount - $commitment->taxable_3_amount;
			$vatExemptOptions = 0;
			$vat1Options = 0;
			$vat2Options = 0;
			$vat3Options = 0;
			if (is_array($commitment->options)) foreach ($commitment->options as $option) {
    			if ($option['vat_id'] == 0) $vatExemptOptions += $option['amount'];
    			elseif ($option['vat_id'] == 1) $vat1Options += $option['amount'];
    			elseif ($option['vat_id'] == 2) $vat2Options += $option['amount'];
    			elseif ($option['vat_id'] == 3) $vat3Options += $option['amount'];
	    	}
	    	
	    	$j++;
			foreach($description['export'] as $propertyId => $property) {
				$colName = $property['options'];
				if ($property['type'] == 'date') $sheet->setCellValue($colName.$j, $context->decodeDate($commitment->properties[$propertyId]));
				elseif ($property['type'] == 'number') $sheet->setCellValue($colName.$j, $commitment->properties[$propertyId]);
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colName.$j, (array_key_exists($commitment->properties[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$commitment->properties[$propertyId]]) : $commitment->properties[$propertyId]);
				else $sheet->setCellValue($colName.$j, $commitment->properties[$propertyId]);
			}
		}
		foreach($description['export'] as $propertyId => $property) {
			$colName = $property['options'];
			$sheet->getStyle($colName.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colName.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colName.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colName.'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colName)->setAutoSize(true);
		}
	}
}