<?php
// One certification signature and an approval signature
//
// The Annots array is an direct object.

require_once 'vendor/autoload.php';

$writer = new \SetaPDF_Core_Writer_String();
$document = new \SetaPDF_Core_Document($writer);
$pages = $document->getCatalog()->getPages();
$page = $pages->create('a4');

$dict = $page->getObject()->ensure();
$dict->offsetSet('Annots', new \SetaPDF_Core_Type_Array());

$signer = new \SetaPDF_Signer($document);
$signer->setCertificationLevel(SetaPDF_Signer::CERTIFICATION_LEVEL_FORM_FILLING);
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);

$writer2 = new SetaPDF_Core_Writer_File(__DIR__ . '/output/certified-with-direct-annotation-array.pdf');
$document = \SetaPDF_Core_Document::loadByString($writer, $writer2);
$signer = new \SetaPDF_Signer($document);
$field = $signer->addSignatureField();
$signer->setSignatureFieldName($field->getQualifiedName());
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);
