<?php
/**
 * PpitCore V1.0 (https://github.com/p-pit/PpitCore)
 *
 * @link      https://github.com/p-pit/PpitCore
 * @copyright Copyright (c) 2016 Bruno Lartillot
 * @license   https://github.com/p-pit/PpitCore/blob/master/license.txt GNU-GPL license
 */

namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;
use PpitCore\Model\Interaction;

class SsmlEventViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');
		$type = $view->type;
		
		$title = $context->getConfig('event/search'.(($type) ? '/'.$type : ''))['title'][$context->getLocale()];
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-Pit')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		$i = 0;
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH');

		foreach($context->getConfig('event/export'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
			$property = $context->getConfig('event'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
			if ($property['type'] == 'specific') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->events as $event) {
			$j++;
			$i = 0;
			foreach($context->getConfig('event/export'.(($type) ? '/'.$type : '')) as $propertyId => $unused) {
				$property = $context->getConfig('event'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
				if ($property['type'] == 'specific') $property = $context->getConfig($property['definition']);
				if ($property) {
					$i++;
					if ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($event->properties[$propertyId]));
					elseif ($property['type'] == 'number') {
						$sheet->setCellValue($colNames[$i].$j, $event->properties[$propertyId]);
						$sheet->getStyle($colNames[$i].$j)->getNumberFormat()->setFormatCode('### ##0.00');
					}
					elseif ($property['type'] == 'select')  $sheet->setCellValue($colNames[$i].$j, (array_key_exists($event->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$event->properties[$propertyId]][$context->getLocale()] : $event->properties[$propertyId]);
					else $sheet->setCellValue($colNames[$i].$j, $event->properties[$propertyId]);
				}
			}
		}
		$i = 0;
		foreach($context->getConfig('event/export'.(($type) ? '/'.$type : '')) as $propertyId => $property) {
			$property = $context->getConfig('event'.(($type) ? '/'.$type : ''))['properties'][$propertyId];
			if ($property['type'] != 'specific' || $context->getConfig($property['definition'])) {
				$i++;
				$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
			}
		}
	}
}