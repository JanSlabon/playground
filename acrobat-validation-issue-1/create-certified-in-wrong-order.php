<?php
// The resulting PDF has a default approval signature applied, following a certification
// signature.
//
// Acrobat validates this totally fine while other viewers (e.g. Foxit, Sign Live) complain.
// (The creation of this structure is not possible in SetaPDF > 2.38.3)
//
// ISO 32000-1 section 12.8.2.2 "DocMDP", subsection 12.8.2.2.1 "General":
//    > The DocMDP transform method shall be used to detect modifications relative to a signature
//    > field that is signed by the author of a document (the person applying the first signature).
//    > A document can contain only one signature field that contains a DocMDP transform method;
//    > *it shall be the first signed field in the document*.
//
// The Annots array is a direct object of the page dictionary.

require_once 'vendor/autoload.php';

$writer = new \SetaPDF_Core_Writer_String();
$document = new \SetaPDF_Core_Document($writer);
$pages = $document->getCatalog()->getPages();
$pages->create('a4');

$signer = new \SetaPDF_Signer($document);
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);

$writer2 = new SetaPDF_Core_Writer_File(__DIR__ . '/output/certified-in-wrong-order.pdf');
$document = \SetaPDF_Core_Document::loadByString($writer, $writer2);
$signer = new \SetaPDF_Signer($document);
$signer->setCertificationLevel(\SetaPDF_Signer::CERTIFICATION_LEVEL_NO_CHANGES_ALLOWED);
$field = $signer->addSignatureField();
$signer->setSignatureFieldName($field->getQualifiedName());
$module = new \SetaPDF_Signer_Signature_Module_Pades();
$module->setCertificate(__DIR__ . '/assets/setapdf-no-pw.pem');
$module->setPrivateKey('file://' . __DIR__ . '/assets/setapdf-no-pw.pem', '');
$signer->sign($module);
