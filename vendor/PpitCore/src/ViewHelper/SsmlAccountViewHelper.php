<?php
namespace PpitCore\ViewHelper;

use PpitCore\Model\Context;

class SsmlAccountViewHelper
{
	public static function formatXls($description, $workbook, $view)
	{
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get(\Zend\I18n\Translator\TranslatorInterface::class);

		$title = (isset ($context->getConfig('core_account/search')['title']) ? $context->localize($description['search']['title']) : $translator->translate('Accounts', 'ppit-commitment', $context->getLocale()));
		
		// Set document properties
		$workbook->getProperties()->setCreator('P-PIT')
			->setLastModifiedBy('P-PIT')
			->setTitle($title)
			->setSubject($title)
			->setDescription($title)
			->setKeywords($title)
			->setCategory($title);

		$sheet = $workbook->getActiveSheet();
		
		$i = 0;
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN', 41 => 'AO', 42 => 'AP', 43 => 'AQ', 44 => 'AR', 45 => 'AS', 46 => 'AT', 47 => 'AU', 48 => 'AV', 49 => 'AW', 50 => 'AX', 51 => 'AY', 52 => 'AZ');
		foreach($description['export'] as $propertyId => $property) {
			$i++;
			$sheet->setCellValue($colNames[$i].'1', $context->localize($property['labels']));
		}

		$j = 1;
		foreach ($view->accounts as $account) {
			$j++;
			$i = 0;
			foreach($description['export'] as $propertyId => $property) {
				$i++;
				$backgroundColor = null;
				$color = null;
				foreach ($property['options'] as $optionId => $option) {
					if ($optionId == 'background-color') {
						foreach ($option as $colorId => $predicates) {
							$matches = true;
							foreach ($predicates as $predicatePropertyId => $value) {
								if (!in_array($account->properties[$predicatePropertyId], explode(',', $value))) {
									$matches = false;
									break;
								}
							}
							if ($matches) {
								$backgroundColor = $colorId;
								break;
							}
						}
					}
					elseif ($optionId == 'color') {
						foreach ($option as $colorId => $predicates) {
							$matches = true;
							foreach ($predicates as $predicatePropertyId => $value) {
								if (!in_array($account->properties[$predicatePropertyId], explode(',', $value))) {
									$matches = false;
									break;
								}
							}
							if ($matches) {
								$color = $colorId;
								break;
							}
						}
					}
				}
				if ($account->properties[$propertyId]) {
					if ($propertyId == 'name') $sheet->setCellValue($colNames[$i].$j, $account->name);
					elseif ($propertyId == 'contact_history') {
						$text = '';
						foreach ($account->properties[$propertyId] as $comment) {
							$text .= $context->decodeDate(substr($comment['time'], 0, 10)).substr($comment['time'], 10).":\n";
							if (array_key_exists('status', $comment)) $text .= $context->localize($context->getconfig('core_account/'.$type.'/property/statuses')[$comment['status']]['labels']);
							$text .= '('.$comment['n_fn'].') '.$comment['comment']."\n";
						}
						$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
						$sheet->setCellValue($colNames[$i].$j, $text);
					}
					elseif ($propertyId == 'place_id') $sheet->setCellValue($colNames[$i].$j, $account->place_caption);
					elseif ($propertyId == 'n_first') $sheet->setCellValue($colNames[$i].$j, $account->n_first);
					elseif ($propertyId == 'n_last') $sheet->setCellValue($colNames[$i].$j, $account->n_last);
					elseif ($propertyId == 'tel_work') $sheet->setCellValue($colNames[$i].$j, $account->tel_work);
					elseif ($propertyId == 'tel_cell') $sheet->setCellValue($colNames[$i].$j, $account->tel_cell);
					elseif ($propertyId == 'email') $sheet->setCellValue($colNames[$i].$j, $account->email);
					elseif ($propertyId == 'birth_date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->birth_date));
					elseif ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $context->decodeDate($account->properties[$propertyId]));
					elseif ($property['type'] == 'number') $sheet->setCellValue($colNames[$i].$j, $context->formatFloat($account->properties[$propertyId], 2));
					elseif (in_array($property['type'], ['select', 'computed'])) {
		    			$value = array();
		    			foreach (explode(',', $account->properties[$propertyId]) as $propertyId) {
			    			if (array_key_exists($propertyId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$propertyId]);
		    			}
		    			$value = implode(', ', $value);
						$sheet->setCellValue($colNames[$i].$j, $value);
					}
					elseif ($property['type'] == 'multiselect') {
						$value = array();
						foreach (explode(',', $account->properties[$propertyId]) as $modalityId) {
							if (array_key_exists($modalityId, $property['modalities'])) $value[] = $context->localize($property['modalities'][$modalityId]);
						}
	    				$value = implode(', ', $value);
						$sheet->setCellValue($colNames[$i].$j, $value);
					}
					elseif ($property['type'] == 'structure') {
						$text = '';
						foreach ($account->properties[$propertyId] as $row) {
							foreach ($property['fields'] as $fieldId => $field) {
								if (array_key_exists($fieldId, $row)) {
									$value = $row[$fieldId];
									$text .= $context->localize($field['labels']).': ';
									if ($property['type'] == 'date') $text .= $context->decodeDate($value);
									elseif ($property['type'] == 'number') $text .= $context->formatFloat($value, 2);
									elseif ($property['type'] == 'select') $text .= (array_key_exists($value, $property['modalities'])) ? $context->localize($property['modalities'][$value]) : $value;
									elseif ($property['type'] == 'multiselect') {
										if ($value) {
											$captions = array();
											foreach (explode(',', $value) as $modalityId) {
												$captions[] = $context->localize($property['modalities'][$modalityId]);
											}
											$text .= implode(', ', $captions);
										}
									}
									else $text .= $value;
								}
								$text .= "\n";
							}
							$text .= "\n";
						}
						$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
						$sheet->setCellValue($colNames[$i].$j, $text);
					}
					else $sheet->setCellValue($colNames[$i].$j, $account->properties[$propertyId]);
					if ($color) $sheet->getStyle($colNames[$i].$j)->getFont()->getColor()->setRGB($color);
					if ($backgroundColor) $sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB($backgroundColor);
				}
			}
		}
		$i = 0;
		foreach($description['export'] as $propertyId => $property) {
			$i++;
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}