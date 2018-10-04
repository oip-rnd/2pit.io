<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class SsmlProductViewHelper
{
	public static function formatXls($type, $workbook, $content)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		$title = (isset ($context->getConfig('core_product/export/'.$type)['title']) ? $context->localize($context->getConfig('core_product/export/'.$type)['title']) : $translator->translate('Products', 'ppit-core', $context->getLocale()));
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		foreach($context->getConfig('core_product/export/'.$type)['properties'] as $propertyId => $column) {
			$property = $context->getConfig('core_product/'.$type.'/property/'.$propertyId);
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$sheet->setCellValue($column.'1', $property['labels'][$context->getLocale()]);
		}

		$j = 1;
		foreach ($content['data'] as $product) {
			$j++;
			foreach($context->getConfig('core_product/export/'.$type)['properties'] as $propertyId => $column) {
				if ($product[$propertyId]) {
					$property = $context->getConfig('core_product/'.$type.'/property/'.$propertyId);
					if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
					if ($property['type'] == 'date') $sheet->setCellValue($column.$j, $context->decodeDate($product[$propertyId]));
					elseif ($property['type'] == 'number') $sheet->setCellValue($column.$j, $context->formatFloat($product[$propertyId], 2));
					elseif ($property['type'] == 'select') {
		    			$value = array();
		    			foreach (explode(',', $product[$propertyId]) as $propertyId) {
			    			if (array_key_exists($propertyId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$propertyId]);
		    			}
		    			$value = implode(', ', $value);
						$sheet->setCellValue($column.$j, $value);
					}
					elseif ($propertyId == 'variants') {
						$text = '';
						$first = true;
						foreach (json_decode($product[$propertyId], true) as $row) {
							if (!$first) $text .= "\n";
							$first = false;
							foreach ($row as $key => $value) $text .= "$key: $value";
						}
						$sheet->getStyle($column.$j)->getAlignment()->setWrapText(true);
						$sheet->setCellValue($column.$j, $text);
					}
					else $sheet->setCellValue($column.$j, $product[$propertyId]);
				}
			}
		}
		foreach($context->getConfig('core_product/export/'.$type)['properties'] as $propertyId => $column) {
			$sheet->getStyle($column.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($column.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($column.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($column.'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($column)->setAutoSize(true);
		}
	}
}