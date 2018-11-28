<?php
namespace PpitCommitment\ViewHelper;

use PpitCore\Model\Context;

class XmlDebitViewHelper
{
	public static function convert($data) {

		$template =
<<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.008.001.02 pain.008.001.02.xsd">
</Document>
XML;
		
		$content = new \SimpleXMLElement($template);
		$CstmrDrctDbtInitn = $content->addChild('CstmrDrctDbtInitn');
		$GrpHdr = $CstmrDrctDbtInitn->addChild('GrpHdr');
		$GrpHdr->addChild('MsgId', $data['GrpHdr']['MsgId']);
		$GrpHdr->addChild('CreDtTm', $data['GrpHdr']['CreDtTm']);
		$GrpHdr->addChild('NbOfTxs', $data['GrpHdr']['NbOfTxs']);
		$GrpHdr->addChild('CtrlSum', $data['GrpHdr']['CtrlSum']);
		$InitgPty = $GrpHdr->addChild('InitgPty');
		$InitgPty->addChild('Nm', $data['GrpHdr']['InitgPty']['Nm']);
		$PmtInf = $CstmrDrctDbtInitn->addChild('PmtInf');
		$PmtInf->addChild('PmtInfId', $data['PmtInf']['PmtInfId']);
		$PmtInf->addChild('PmtMtd', $data['PmtInf']['PmtMtd']);
		$PmtInf->addChild('NbOfTxs', $data['PmtInf']['NbOfTxs']);
		$PmtInf->addChild('CtrlSum', $data['PmtInf']['CtrlSum']);
		$PmtTpInf = $PmtInf->addChild('PmtTpInf');
		$SvlLvl = $PmtTpInf->addChild('SvcLvl');
		$SvlLvl->addChild('Cd', $data['PmtInf']['PmtTpInf']['SvcLvl']['Cd']);
		$LclInstrm = $PmtTpInf->addChild('LclInstrm');
		$LclInstrm->addChild('Cd', $data['PmtInf']['PmtTpInf']['LclInstrm']['Cd']);
		$PmtTpInf->addChild('SeqTp', $data['PmtInf']['PmtTpInf']['SeqTp']);
		$PmtInf->addChild('ReqdColltnDt', $data['PmtInf']['ReqdColltnDt']);
		$Cdtr = $PmtInf->addChild('Cdtr');
		$Cdtr->addChild('Nm', $data['PmtInf']['Cdtr']['Nm']);
		$CdtrAcct = $PmtInf->addChild('CdtrAcct');
		$Id = $CdtrAcct->addChild('Id');
		$Id->addChild('IBAN', $data['PmtInf']['CdtrAcct']['Id']['IBAN']);
		$CdtrAgt = $PmtInf->addChild('CdtrAgt');
		$FinInstnId = $CdtrAgt->addChild('FinInstnId');
		$Othr = $FinInstnId->addChild('Othr');
		$Othr->addChild('Id', $data['PmtInf']['CdtrAgt']['FinInstnId']['Othr']['Id']);
		$CdtrSchmeId = $PmtInf->addChild('CdtrSchmeId');
		$Id = $CdtrSchmeId->addChild('Id');
		$PrvtId = $Id->addChild('PrvtId');
		$Othr = $PrvtId->addChild('Othr');
		$Othr->addChild('Id', $data['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['Id']);
		$SchmeNm = $Othr->addChild('SchmeNm');
		$SchmeNm->addChild('Prtry', $data['PmtInf']['CdtrSchmeId']['Id']['PrvtId']['Othr']['SchmeNm']['Prtry']);
		
		foreach ($data['PmtInf']['DrctDbtTxInf'] as $row) {
			$DrctDbtTxInf = $PmtInf->addChild('DrctDbtTxInf');
			$PmtId = $DrctDbtTxInf->addChild('PmtId');
			$PmtId->addChild('EndToEndId', $row['PmtId']['EndToEndId']);
			$InstdAmt = $DrctDbtTxInf->addChild('InstdAmt', $row['InstdAmt']);
			$InstdAmt->addAttribute('Ccy', 'EUR');
			$DrctDbtTx = $DrctDbtTxInf->addChild('DrctDbtTx');
			$MndtRltdInf = $DrctDbtTx->addChild('MndtRltdInf');
			$MndtRltdInf->addChild('MndtId', $row['DrctDbtTx']['MndtRltdInf']['MndtId']);
			$MndtRltdInf->addChild('DtOfSgntr', $row['DrctDbtTx']['MndtRltdInf']['DtOfSgntr']);
			$DbtrAgt = $DrctDbtTxInf->addChild('DbtrAgt');
			$FinInstnId = $DbtrAgt->addChild('FinInstnId');
			$Othr = $FinInstnId->addChild('Othr');
			$Othr->addChild('Id', $row['DbtrAgt']['FinInstnId']['Othr']['Id']);
			$Dbtr = $DrctDbtTxInf->addChild('Dbtr');
			$Dbtr->addChild('Nm', $row['Dbtr']['Nm']);
			$DbtrAcct = $DrctDbtTxInf->addChild('DbtrAcct');
			$Id = $DbtrAcct->addChild('Id');
			$Id->addChild('IBAN', $row['DbtrAcct']['Id']['IBAN']);
/*			$RgltryRptg = $DrctDbtTxInf->addChild('RgltryRptg');
			$Dtls = $RgltryRptg->addChild('Dtls');
			$Dtls->addChild('Cd', $row['RgltryRptg']['Dtls']['Cd']);*/
		}
		return $content->asXML();
	}
}
