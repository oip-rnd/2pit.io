<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class SsmlCrossTableViewHelper
{
	public static function formatXls($workbook, $matrix, $columns, $rows, $title)
	{
		$context = Context::getCurrent();
		$title = $context->localize($title);
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		foreach($columns as $columnId => $columnDef) {
			$colName = $columnDef['columnId'];
			$sheet->setCellValue($colName.'1', $context->localize($columnDef['labels']));
			$sheet->getStyle($colName.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colName.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colName.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colName.'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($matrix as $rowId => $row) {
			$j++;
			$sheet->setCellValue('A'.$j, $context->localize($rows[$rowId]['labels']));
			foreach($row as $cellId => $cell) {
				$colName = $columns[$cellId]['columnId'];
				$sheet->setCellValue($colName.$j, $cell);
				$sheet->getStyle($colName.$j)->getNumberFormat()->setFormatCode('### ##0.00');
			}
		}
		foreach($columns as $columnId => $columnDef) {
			$colName = $columnDef['columnId'];
			$sheet->getColumnDimension($colName)->setAutoSize(true);
		}
	}
}