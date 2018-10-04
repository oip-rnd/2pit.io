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
use PpitCore\Model\Place;
use PpitCore\Model\Vcard;

class SsmlUserViewHelper
{
	public static function formatXls($workbook, $view)
	{
		$context = Context::getCurrent();
		$places = Place::getList(array());
		$translator = $context->getServiceManager()->get('translator');
		
		$title = $context->getConfig('coreUser/search')['title'][$context->getLocale()];
		
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
		$colNames = array(
				1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z',
				27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN', 41 => 'AO', 42 => 'AP', 43 => 'AQ', 44 => 'AR', 45 => 'AS', 46 => 'AT', 47 => 'AU', 48 => 'AV', 49 => 'AW', 50 => 'AX', 51 => 'AY', 52 => 'AZ',
				53 => 'BA', 54 => 'BB', 55 => 'BC', 56 => 'BD', 57 => 'BE', 58 => 'BF', 59 => 'BG', 60 => 'BH', 61 => 'BI', 62 => 'BJ', 63 => 'BK', 64 => 'BL', 65 => 'BM', 66 => 'BN', 67 => 'BO', 68 => 'BP', 69 => 'BQ', 70 => 'BR', 71 => 'BS', 72 => 'BT', 73 => 'BU', 74 => 'BV', 75 => 'BW', 76 => 'BX', 77 => 'BY', 78 => 'BZ',
				79 => 'CA', 80 => 'CB', 81 => 'CC', 82 => 'CD', 83 => 'CE', 84 => 'CF', 85 => 'CG', 86 => 'CH', 87 => 'CI', 88 => 'CJ', 89 => 'CK', 90 => 'CL', 91 => 'CM', 92 => 'CN', 93 => 'CO', 94 => 'CP', 95 => 'CQ', 96 => 'CR', 97 => 'CS', 98 => 'CT', 99 => 'CU', 100 => 'CV', 101 => 'CW', 102 => 'CX', 103 => 'CY', 104 => 'CZ',
		);

		// Headers
		$first = true;
		foreach($context->getConfig('coreUser/export') as $propertyId => $unused) {
			$property = $context->getConfig('coreUser')['properties'][$propertyId];
			if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
			$i++;
			$sheet->setCellValue($colNames[$i].'2', $property['labels'][$context->getLocale()]);
			$sheet->getStyle($colNames[$i].'2')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'2')->getFont()->setBold(true);
		}

		$borderStyle = array(
			'borders' => array(
				'left' => array(
					'style' => \PHPExcel_Style_Border::BORDER_THICK,
					'color' => array('argb' => 'FFFF0000'),
				),
			),
		);
		
		// Roles
		$first = true;
		foreach ($context->getConfig('manageable_roles') as $roleId) {
			$role = $context->getConfig('ppit_roles')[$roleId];
			$i++;
			if ($first) {
				$sheet->setCellValue($colNames[$i].'1', $translator->translate('Roles', 'ppit-core', $context->getLocale()));
				$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
				$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
				$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
				$sheet->getStyle($colNames[$i].'1')->applyFromArray($borderStyle);
				$sheet->getStyle($colNames[$i].'2')->applyFromArray($borderStyle);
			}
			$sheet->setCellValue($colNames[$i].'2', $role['labels'][$context->getLocale()]);
			$sheet->getStyle($colNames[$i].'2')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'2')->getAlignment()->setWrapText(true);
			$sheet->getStyle($colNames[$i].'2')->getFont()->setBold(true);
			$first = false;
		}

		// Perimeters
		foreach ($context->getConfig('perimeters') as $applicationId => $application) {
			foreach($application as $perimeterId => $perimeter) {
				$perimeter = $context->getConfig($perimeter);
				$first = true;
				foreach($perimeter['modalities'] as $modalityId => $modality) {
					$i++;
					if ($first) {
						$sheet->setCellValue($colNames[$i].'1', $perimeter['labels'][$context->getLocale()]);
						$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
						$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
						$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
						$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
						$sheet->getStyle($colNames[$i].'1')->applyFromArray($borderStyle);
						$sheet->getStyle($colNames[$i].'2')->applyFromArray($borderStyle);
					}
					$sheet->setCellValue($colNames[$i].'2', $modality[$context->getLocale()]);
					$sheet->getStyle($colNames[$i].'2')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
					$sheet->getStyle($colNames[$i].'2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
					$sheet->getStyle($colNames[$i].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->getStyle($colNames[$i].'2')->getAlignment()->setWrapText(true);
					$sheet->getStyle($colNames[$i].'2')->getFont()->setBold(true);
					$first = false;
				}
			}
		}

		// Places
		$first = true;
		foreach ($places as $place) {
			$i++;
			if ($first) {
				$sheet->setCellValue($colNames[$i].'1', $translator->translate('Places', 'ppit-core', $context->getLocale()));
				$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
				$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
				$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
				$sheet->getStyle($colNames[$i].'1')->applyFromArray($borderStyle);
				$sheet->getStyle($colNames[$i].'2')->applyFromArray($borderStyle);
			}
			$sheet->setCellValue($colNames[$i].'2', $place->caption);
			$sheet->getStyle($colNames[$i].'2')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'2')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'2')->getAlignment()->setWrapText(true);
			$sheet->getStyle($colNames[$i].'2')->getFont()->setBold(true);
			$first = false;
		}
		
		// Rows
		$j = 2;
		foreach ($view->users as $user) {
			$j++;
			$i = 0;
			foreach($context->getConfig('coreUser/export') as $propertyId => $unused) {
				$property = $context->getConfig('coreUser')['properties'][$propertyId];
				if ($property['definition'] != 'inline') $property = $context->getConfig($property['definition']);
				$i++;
				if ($propertyId == 'n_fn') $sheet->setCellValue($colNames[$i].$j, $user->n_fn);
				elseif ($propertyId == 'username') $sheet->setCellValue($colNames[$i].$j, $user->username);
				elseif ($propertyId == 'email') $sheet->setCellValue($colNames[$i].$j, $user->email);
				elseif ($property['type'] == 'date') $sheet->setCellValue($colNames[$i].$j, $user->properties[$propertyId]);
				elseif ($property['type'] == 'number') {
					$sheet->setCellValue($colNames[$i].$j, $user->properties[$propertyId]);
					$sheet->getStyle($colNames[$i].$j)->getNumberFormat()->setFormatCode('### ##0.00');
				}
				elseif ($property['type'] == 'select')  $sheet->setCellValue($colNames[$i].$j, (array_key_exists($user->properties[$propertyId], $property['modalities'])) ? $property['modalities'][$user->properties[$propertyId]][$context->getLocale()] : $user->properties[$propertyId]);
				else $sheet->setCellValue($colNames[$i].$j, $user->properties[$propertyId]);
				
				// Font color
				if ($j % 2 == 0) $sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EEE');
			}

			// Roles
			$first = true;
			foreach ($context->getConfig('manageable_roles') as $roleId) {
				$role = $context->getConfig('ppit_roles')[$roleId];
				$i++;
				if ($first) {
					$sheet->getStyle($colNames[$i].$j)->applyFromArray($borderStyle);
				}
				if (array_key_exists($roleId, $user->roles)) {
					$sheet->getStyle($colNames[$i].$j)->getFont()->getColor()->setRGB('006179');
					$sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('79CCF3');
					$sheet->getStyle($colNames[$i].$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->setCellValue($colNames[$i].$j, 'X');
				} elseif ($j % 2 == 0) $sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EEE');

				$first = false;
			}

			// Perimeters
			foreach ($context->getConfig('perimeters') as $applicationId => $application) {
				foreach($application as $perimeterId => $perimeter) {
					$perimeter = $context->getConfig($perimeter);
					$first = true;
					foreach($perimeter['modalities'] as $modalityId => $modality) {
						$i++;
						if ($first) {
							$sheet->getStyle($colNames[$i].$j)->applyFromArray($borderStyle);
						}
						if (array_key_exists($applicationId, $user->perimeters) && array_key_exists($perimeterId, $user->perimeters[$applicationId]) && in_array($modalityId, $user->perimeters[$applicationId][$perimeterId])) {
							$sheet->getStyle($colNames[$i].$j)->getFont()->getColor()->setRGB('006179');
							$sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('79CCF3');
							$sheet->getStyle($colNames[$i].$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
							$sheet->setCellValue($colNames[$i].$j, 'X');
						}
						elseif ($j % 2 == 0) $sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EEE');

						$first = false;
					}
				}
			}
		
			// Places
			$first = true;
			foreach ($places as $place) {
				$i++;
				if ($first) {
					$sheet->getStyle($colNames[$i].$j)->applyFromArray($borderStyle);
				}
				if (array_key_exists('p-pit-admin', $user->perimeters) && array_key_exists('place_id', $user->perimeters['p-pit-admin']) && in_array($place->id, $user->perimeters['p-pit-admin']['place_id'])) {
					$sheet->getStyle($colNames[$i].$j)->getFont()->getColor()->setRGB('006179');
					$sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('79CCF3');
					$sheet->getStyle($colNames[$i].$j)->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$sheet->setCellValue($colNames[$i].$j, 'X');
				}
				elseif ($j % 2 == 0) $sheet->getStyle($colNames[$i].$j)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('EEE');

				$first = false;
			}
		}
		
		// Columns width
		$i = 0;
		foreach($context->getConfig('coreUser/export') as $propertyId => $property) {
			$i++;
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
	}
}