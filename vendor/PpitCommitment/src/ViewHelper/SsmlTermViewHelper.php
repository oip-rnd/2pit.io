<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class SsmlTermViewHelper
{
	public static function formatXls($description, $workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get(\Zend\I18n\Translator\TranslatorInterface::class);

		$title = $translator->translate('Terms', 'ppit-commitment', $context->getLocale());
		
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
			$sheet->setCellValue($colName.'1', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colName.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colName.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colName.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colName.'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->terms as $term) {
			$j++;
			foreach($description['export'] as $propertyId => $property) {
				$colName = $property['options'];
				if ($propertyId == 'place_id') $sheet->setCellValue($colName.$j, $term->place_caption);
				elseif ($property['type'] == 'date') $sheet->setCellValue($colName.$j, $context->decodeDate($term->properties[$propertyId]));
				elseif ($property['type'] == 'number') {
					$sheet->setCellValue($colName.$j, $term->properties[$propertyId]);
					$sheet->getStyle($colName.$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colName.$j, (array_key_exists($term->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$term->properties[$propertyId]][$context->getLocale()] : $term->properties[$propertyId]);
				else $sheet->setCellValue($colName.$j, $term->properties[$propertyId]);
			}
		}
		foreach($description['export'] as $propertyId => $property) {
			$colName = $property['options'];
			$sheet->getColumnDimension($colName)->setAutoSize(true);
		}
	}
}