<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;
use PpitCore\Model\ProductOption;

class SsmlCommitmentViewHelper
{
	public static function formatXls($description, $workbook, $view)
	{
		$context = Context::getCurrent();
		$type = $description['type'];
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
		
		$colNames = [
			'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
			'AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV', 'AW', 'AX', 'AY', 'AZ',
			'BA', 'BB', 'BC', 'BD', 'BE', 'BF', 'BG', 'BH', 'BI', 'BJ', 'BK', 'BL', 'BM', 'BN', 'BO', 'BP', 'BQ', 'BR', 'BS', 'BT', 'BU', 'BV', 'BW', 'BX', 'BY', 'BZ',
		];

		reset($colNames);
		foreach($description['export'] as $propertyId => $property) {
			$colName = $property['options'];
			$sheet->setCellValue($colName.'1', $context->localize($property['labels']));
			next($colNames);
		}
		
		// Retrieve all the options form the database and buil a dictionnary with the option's reference as a key
		$cursor = ProductOption::getList($type, []);
		$options = array();
		foreach ($cursor as $option) $options[$option->reference] = $option;
		
		$optionCategories = array();
		$categoryDescription = $context->getConfig('productOption/'.$type.'/property/category')['modalities'];
		if (!$categoryDescription) $categoryDescription = $context->getConfig('productOption/generic/property/category')['modalities'];
		foreach ($categoryDescription as $categoryId => $category) {
			$colName = current($colNames);
			$sheet->setCellValue($colName.'1', $context->localize($category));
			$optionCategories[$categoryId] = current($colNames);
			next($colNames);
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
				elseif ($propertyId == 'place_id')  $sheet->setCellValue($colName.$j, $property['modalities'][$commitment->properties[$propertyId]]);
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colName.$j, (array_key_exists($commitment->properties[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$commitment->properties[$propertyId]]) : $commitment->properties[$propertyId]);
				else $sheet->setCellValue($colName.$j, $commitment->properties[$propertyId]);
			}
			
			// Dispatch the per-option amount in the corresponding column, named by the option's reference
			$vector = array();
			foreach ($commitment->options as $optionId => $optionRow) {
				if (array_key_exists('identifier', $optionRow) && array_key_exists('amount', $optionRow) && array_key_exists($optionRow['identifier'], $options)) {
					$option = $options[$optionRow['identifier']];
					if (array_key_exists($option->category, $vector)) $vector[$option->category] += $optionRow['amount'];
					else $vector[$option->category] = $optionRow['amount'];
				}
			}
			foreach ($vector as $optionCategory => $amount) {
				$colName = $optionCategories[$optionCategory];
				$sheet->setCellValue($colName.$j, $amount);
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
		foreach ($optionCategories as $colName) {
			$sheet->getStyle($colName.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colName.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colName.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colName.'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colName)->setAutoSize(true);
		}
	}
}