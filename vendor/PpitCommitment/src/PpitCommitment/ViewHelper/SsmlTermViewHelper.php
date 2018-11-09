<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class SsmlTermViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = (isset ($context->getConfig('commitment/search')['title']) ? $context->getConfig('commitmentTerm/search')['title'][$context->getLocale()] : $this->translate('Terms', 'ppit-commitment', $context->getLocale()));
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		foreach($context->getConfig('commitmentTerm/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $colName) {
			$property = $context->getConfig('commitmentTerm'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
			if (!$property) $property = $context->getConfig('commitmentTerm')['properties'][$propertyId];
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$sheet->setCellValue($colName.'1', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colName.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colName.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colName.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colName.'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->terms as $term) {
			$j++;
			foreach($context->getConfig('commitmentTerm/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $colName) {
				$property = $context->getConfig('commitmentTerm'.(($view->type) ? '/'.$view->type: ''))['properties'][$propertyId];
				if (!$property) $property = $context->getConfig('commitmentTerm')['properties'][$propertyId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				if ($property['type'] == 'date') $sheet->setCellValue($colName.$j, $term->properties[$propertyId]);
				elseif ($property['type'] == 'number') {
					$sheet->setCellValue($colName.$j, $term->properties[$propertyId]);
					$sheet->getStyle($colName.$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colName.$j, (array_key_exists($term->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$term->properties[$propertyId]][$context->getLocale()] : $term->properties[$propertyId]);
				else $sheet->setCellValue($colName.$j, $term->properties[$propertyId]);
			}
		}
		foreach($context->getConfig('commitmentTerm/export'.(($view->type) ? '/'.$view->type: '')) as $propertyId => $colName) {
			$sheet->getColumnDimension($colName)->setAutoSize(true);
		}
	}
}