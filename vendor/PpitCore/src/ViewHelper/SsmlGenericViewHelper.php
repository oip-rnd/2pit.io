<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class SsmlGenericViewHelper
{
	public static function formatXls($workbook, $content)
	{
		$context = Context::getCurrent();
		$title = $context->localize($content['title']);

		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		$j = 0;
		foreach($content['sheet'] as $row) {
			$j++;
			foreach($row as $cellId => $cell) {
				$sheet->setCellValue($cellId.$j, $context->localize($cell));
				if (array_key_exists('format', $cell) && $cell['format'] == 'number') $sheet->getStyle($cellId.$j)->getNumberFormat()->setFormatCode('### ##0.00');
			}
		}
		reset($content['sheet']);
		foreach(current($content['sheet']) as $cellId => $unused) {
			$sheet->getStyle($cellId.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($cellId.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($cellId.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($cellId.'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($cellId)->setAutoSize(true);
		}
	}
}