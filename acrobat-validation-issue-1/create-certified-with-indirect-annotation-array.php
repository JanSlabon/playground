<?php
// One certification signature and an approval signature
//
// The Annots array is an indirect object, which already triggers "1 Miscellaneous Change(s)"
// message in the signature panel.

require_once 'vendor/autoload.php';

$writer = new \SetaPDF_Core_Writer_String();
$document = new \SetaPDF_Core_Document($writer);
$pages = $document->getCatalog()->getPages();
$page = $pages->create('a4');

$dict = $page->getObject()->ensure();
$o = $document->createNewObject(new \SetaPDF_Core_Type_Array());
$dict->offsetSet('Annots', $o);

$signer = new \SetaPDF_Signer($document);
$signer->setCertificationLevel(SetaPDF_Signer::CERTIFICATION_LEVEL_FORM_FILLING);
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);

$writer2 = new SetaPDF_Core_Writer_File(__DIR__ . '/output/certified-with-indirect-annotation-array.pdf');
$document = \SetaPDF_Core_Document::loadByString($writer, $writer2);
$signer = new \SetaPDF_Signer($document);
$field = $signer->addSignatureField();
$signer->setSignatureFieldName($field->getQualifiedName());
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);
