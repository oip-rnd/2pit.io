<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class ArrayToSsmlViewHelper
{
	public static function convert($data, $footer, $description) {
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get(\Zend\I18n\Translator\TranslatorInterface::class);

		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';
		
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$workbook = new \PHPExcel;

		$title = $context->localize($description['title']);
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
		->setLastModifiedBy('P-PIT')
		->setTitle($title)
		->setSubject($title)
		->setDescription($title)
		->setKeywords($title)
		->setCategory($title);
		
		$sheet = $workbook->getActiveSheet();

		foreach ($description['properties'] as $propertyId => $property) {
			$sheet->setCellValue($property['col_name'].'1', $context->localize($property['labels']));
		}
		$j = 2;
		foreach ($data as $row) {
			foreach ($description['properties'] as $propertyId => $property) {
				if ($property) {
					if ($property['type'] == 'date') $value = $context->decodeDate($row[$propertyId]);
					elseif ($property['type'] == 'number') {
						$value = $row[$propertyId];
						$sheet->getStyle($property['col_name'].$j)->getNumberFormat()->setFormatCode('### ##0.00');
					}
					elseif ($property['type'] == 'select') $value = (array_key_exists('modalities', $property) && array_key_exists($row[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$row[$propertyId]]) : $row[$propertyId];
					elseif ($property['type'] == 'multiselect') {
						$value = array();
						foreach (explode(',', $row[$propertyId]) as $modalityId) {
							if (array_key_exists($modalityId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$modalityId]);
						}
	    				$value = implode(', ', $value);
					}
					else $value = $row[$propertyId];
				}
				$sheet->setCellValue($property['col_name'].$j, $value);
			}
			$j++;
		}
		foreach ($description['footer'] as $propertyId => $property) {
			if ($property) {
				if ($property['type'] == 'title') $value = $context->localize($property['labels']);
				elseif ($property['type'] == 'date') $value = $context->decodeDate($footer[$propertyId]);
				elseif ($property['type'] == 'number') {
					$value = $footer[$propertyId];
					$sheet->getStyle($property['col_name'].$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select') $value = (array_key_exists('modalities', $property) && array_key_exists($footer[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$footer[$propertyId]]) : $footer[$propertyId];
				elseif ($property['type'] == 'multiselect') {
					$value = array();
					foreach (explode(',', $footer[$propertyId]) as $modalityId) {
						if (array_key_exists($modalityId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$modalityId]);
					}
					$value = implode(', ', $value);
				}
				else $value = $footer[$propertyId];
			}
			$sheet->setCellValue($property['col_name'].$j, $value);
		}		
		foreach ($description['properties'] as $propertyId => $property) {
			$sheet->getStyle($property['col_name'].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($property['col_name'].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($property['col_name'].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($property['col_name'].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($property['col_name'])->setAutoSize(true);
		}
		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename='.$description['file_name'].'.xlsx ');
		$writer->save('php://output');
	}
}
