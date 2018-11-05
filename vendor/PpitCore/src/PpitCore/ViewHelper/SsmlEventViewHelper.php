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
	public static function formatXls($workbook, $view, $description)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');
		$type = $view->type;
		
		$title = $context->localize($description['search']['title']);
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-Pit')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();

		foreach($description['export'] as $propertyId => $property) {
			$column = $property['column'];
			$sheet->setCellValue($column.'1', $context->localize($property['labels']));
			$sheet->getStyle($column.'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($column.'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($column.'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($column.'1')->getFont()->setBold(true);
		}

		$j = 1;
		foreach ($view->events as $event) {
			$j++;
			foreach($description['export'] as $propertyId => $property) {
				$column = $property['column'];
				if ($property) {
					if ($propertyId == 'matching_log') {
						$matching_log = array();
						if ($event->matching_log) foreach ($event->matching_log as $accountId => $log) if (array_key_exists($accountId, $description['export']['matched_accounts']['modalities'])) $matching_log[$context->localize($description['export']['matched_accounts']['modalities'][$accountId])] = $log;
						$sheet->setCellValue($column.$j, json_encode($matching_log, JSON_PRETTY_PRINT));
					}
					elseif ($propertyId == 'feedbacks') {
						$feedbacks = array();
						foreach ($event->feedbacks as $giverId => $giver) {
							if ($giverId == $event->account_id) $giverName = $event->n_fn;
							else {
								if (array_key_exists($giverId, $description['export']['matched_accounts']['modalities'])) $giverName = $context->localize($description['export']['matched_accounts']['modalities'][$giverId]);
								else $giverName = $giverId;
							}
							foreach ($giver as $receiverId => $feedback) {
								if ($receiverId == $event->account_id) $receiverName = $event->n_fn;
								else {
									if (array_key_exists($receiverId, $description['export']['matched_accounts']['modalities'])) $receiverName = $context->localize($description['export']['matched_accounts']['modalities'][$receiverId]);
									else $receiverName = $receiverId;
								}
								$feedbacks[$giverName] = [$receiverName => $feedback];
							}
						}
						$sheet->setCellValue($column.$j, json_encode($feedbacks, JSON_PRETTY_PRINT));
					}
					elseif ($property['type'] == 'date') $sheet->setCellValue($column.$j, $context->decodeDate($event->properties[$propertyId]));
					elseif ($property['type'] == 'number') {
						$sheet->setCellValue($column.$j, $event->properties[$propertyId]);
						$sheet->getStyle($column.$j)->getNumberFormat()->setFormatCode('### ##0.00');
					}
					elseif ($property['type'] == 'select')  $sheet->setCellValue($column.$j, (array_key_exists('modalities', $property) && array_key_exists($event->properties[$propertyId], $property['modalities'])) ? $context->localize($property['modalities'][$event->properties[$propertyId]]) : $event->properties[$propertyId]);
					elseif ($property['type'] == 'multiselect') {
						$value = array();
						foreach (explode(',', $event->properties[$propertyId]) as $modalityId) {
							if (array_key_exists($modalityId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$modalityId]);
						}
	    				$value = implode(', ', $value);
						$sheet->setCellValue($column.$j, $value);
					}
					else $sheet->setCellValue($column.$j, $event->properties[$propertyId]);
				}
			}
		}
		$sheet->getDefaultStyle()->getAlignment()->setWrapText(true);
		foreach($description['export'] as $propertyId => $property) {
			$column = $property['column'];
			if ($property) {
				$sheet->getColumnDimension($column)->setAutoSize(true);
			}
		}
	}
}