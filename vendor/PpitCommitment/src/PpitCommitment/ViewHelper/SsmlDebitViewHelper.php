<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class SsmlDebitViewHelper
{
	public static function convert($data) {
		$context = Context::getCurrent();
		$translator = $context->getServiceManager()->get('translator');

		include 'public/PHPExcel_1/Classes/PHPExcel.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/Writer/Excel2007.php';
		include 'public/PHPExcel_1/Classes/PHPExcel/CachedObjectStorageFactory.php';
		
		$cacheMethod = \PHPExcel_CachedObjectStorageFactory:: cache_to_phpTemp;
		$cacheSettings = array( ' memoryCacheSize ' => '8MB');
		\PHPExcel_Settings::setCacheStorageMethod($cacheMethod, $cacheSettings);

		$workbook = new \PHPExcel;

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
		
		$colNames = array(1 => 'A', 2 => 'B', 3 => 'C', 4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H', 9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M', 14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R', 19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W', 24 => 'X', 25 => 'Y', 26 => 'Z', 27 => 'AA', 28 => 'AB', 29 => 'AC', 30 => 'AD', 31 => 'AE', 32 => 'AF', 33 => 'AG', 34 => 'AH', 35 => 'AI', 36 => 'AJ', 37 => 'AK', 38 => 'AL', 39 => 'AM', 40 => 'AN', 41 => 'AO', 42 => 'AP', '43' => 'AQ', '44' => 'AR');
		$i = 1;
		$sheet->setCellValue($colNames[$i++].'1', 'MsgId');
		$sheet->setCellValue($colNames[$i++].'1', 'MsgCreDtIm');
		$sheet->setCellValue($colNames[$i++].'1', 'MsgNbOfTxs');
		$sheet->setCellValue($colNames[$i++].'1', 'MsgCtrlSum');
		$sheet->setCellValue($colNames[$i++].'1', 'InitgPty/Nm');
		$sheet->setCellValue($colNames[$i++].'1', 'PmtInfId');
		$sheet->setCellValue($colNames[$i++].'1', 'PtmMtd');
		$sheet->setCellValue($colNames[$i++].'1', 'NbOfTxs');
		$sheet->setCellValue($colNames[$i++].'1', 'CtrlSum');
		$sheet->setCellValue($colNames[$i++].'1', 'SvcLvl');
		$sheet->setCellValue($colNames[$i++].'1', 'LclInstrm');
		$sheet->setCellValue($colNames[$i++].'1', 'SeqTp');
		$sheet->setCellValue($colNames[$i++].'1', 'ReqdColltnDt');
		$sheet->setCellValue($colNames[$i++].'1', 'Cdtr/Nm');
		$sheet->setCellValue($colNames[$i++].'1', 'CdtrAcct/IBAN');
		$sheet->setCellValue($colNames[$i++].'1', 'CdtrAgt/Othr');
		$sheet->setCellValue($colNames[$i++].'1', 'CdtrSchmeId/PrvtId');
		$sheet->setCellValue($colNames[$i++].'1', 'CdtrSchmeId/PrvtId/Prtry');
		$sheet->setCellValue($colNames[$i++].'1', 'EndToEndId');
		$sheet->setCellValue($colNames[$i++].'1', 'InstdAmt');
		$sheet->setCellValue($colNames[$i++].'1', 'MntdRltdInf');
		$sheet->setCellValue($colNames[$i++].'1', 'DtOfSgntr');
		$sheet->setCellValue($colNames[$i++].'1', 'DbtrAgt/FinInstnId');
		$sheet->setCellValue($colNames[$i++].'1', 'Dbtr/Nm');
		$sheet->setCellValue($colNames[$i++].'1', 'DbtrAcct/IBAN');
		
		$j = 2;
		foreach ($data['PmtInf']['DrctDbtTxInf'] as $row) {
			$i = 1;

			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['GrpHdr']['MsgId']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['GrpHdr']['CreDtTm']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['GrpHdr']['NbOfTxs']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['GrpHdr']['CtrlSum']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['GrpHdr']['InitgPty']['Nm']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['PmtInfId']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['PmtMtd']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['NbOfTxs']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['CtrlSum']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['PmtTpInf']['SvcLvl']['Cd']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['PmtTpInf']['LclInstrm']['Cd']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['PmtTpInf']['SeqTp']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['ReqdColltnDt']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['Cdtr']['Nm']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['CdtrAcct']['Id']['IBAN']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['CdtrAgt']['FinInstnId']['Othr']['Id']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['Id']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $data['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm']['Prtry']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['PmtId']['EndToEndId']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['InstdAmt']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['DrctDbtTx']['MndtRltdInf']['MndtId']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['DrctDbtTx']['MndtRltdInf']['DtOfSgntr']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['DbtrAgt']['FinInstnId']['Othr']['Id']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['Dbtr']['Nm']);
			$sheet->getStyle($colNames[$i].$j)->getAlignment()->setWrapText(true);
			$sheet->setCellValue($colNames[$i++].$j, $row['DbtrAcct']['Id']['IBAN']);
			$j++;
		}
		$colNumber = $i - 1;
		for ($i = 1; $i <= $colNumber; $i++) {
			$sheet->getStyle($colNames[$i].'1')->getFont()->getColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingColor'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB(substr($context->getConfig('styleSheet')['panelHeadingBackground'], 1, 6));
			$sheet->getStyle($colNames[$i].'1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$sheet->getStyle($colNames[$i].'1')->getFont()->setBold(true);
			$sheet->getColumnDimension($colNames[$i])->setAutoSize(true);
		}
		
		$writer = new \PHPExcel_Writer_Excel2007($workbook);
		
		header('Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition:inline;filename=P-Pit_Debits.xlsx ');
		$writer->save('php://output');
	}
}
