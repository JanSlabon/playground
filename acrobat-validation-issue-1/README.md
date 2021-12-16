# Validation behavior of Acrobat for certification signatures

We trapped into some issues related to validation of certification 
signatures in Acrobat Acrobat Reader (2021.007.20099)

## A certification signature can be added AFTER an approval signature

Acrobat accepts this order while other viewers (e.g. FoxitPhantom, Sing Live!) 
complain when verifying the signature.

ISO 32000-1 section 12.8.2.2 "DocMDP", subsection 12.8.2.2.1 "General" states:
> The DocMDP transform method shall be used to detect modifications relative to a signature
> field that is signed by the author of a document (the person applying the first signature).
> A document can contain only one signature field that contains a DocMDP transform method;
> **it shall be the first signed field in the document**.

In ISO 32000-2 "it shall be the first signed field in the document" was removed.
So we can leave this open but we decided not to allow such structure because it
is questionable how it is validated by the final application.

An example document with this structure is available in: [certified-in-wrong-order.pdf](output/certified-in-wrong-order.pdf)

## Annots array as a direct vs. indirect object

A more strange behavior we found is if the Annots array of a page object is an 
indirect reference or a direct array object. It looks like Acrobat take the
modification of this array in a different way depending on how it is stored
(direct vs. indirect).

Starting with simple approval signatures: [approvals-with-indirect-annotation-array.pdf](output/approvals-with-indirect-annotation-array.pdf)
The Annots array is an indirect reference. Acrobat validates but states that there is

> 1 Miscellaneous Change(s)

between both signatures. If we switch to a direct object, this does not happen: [approvals-with-direct-annotation-array.pdf](output/approvals-with-direct-annotation-array.pdf)

While this is only a small message it will have a much more impact if the first 
signature is a certification signature. The certification signature will be marked
as invalid because of treating the change in the indirect object as a not allowed 
"Miscellaneous Change": [certified-with-indirect-annotation-array.pdf](output/certified-with-indirect-annotation-array.pdf)

Having the Annots array as a direct object, the document is validated absolutely 
fine: [certified-with-direct-annotation-array.pdf](output/certified-with-direct-annotation-array.pdf)

The only difference between the documents is following structure change. This sturcutre
does not trigger a "Miscellaneous Change":

    1 0 obj
    <</Type/Page/MediaBox[ 0 0 595.28 841.89]/Resources<<>>/Parent 3 0 R/Annots[ 5 0 R]>>
    endobj
    
    5 0 obj
    <</Type/Annot/Subtype/Widget/Rect[ 0 0 0 0]/FT/Sig/H/P/F 4/T(Signature)/P 1 0 R>>
    endobj

While this one does:

    1 0 obj
    <</Type/Page/MediaBox[ 0 0 595.28 841.89]/Resources<<>>/Parent 3 0 R/Annots 4 0 R>>
    endobj

    4 0 obj
    [ 6 0 R]
    endobj

    6 0 obj
    <</Type/Annot/Subtype/Widget/Rect[ 0 0 0 0]/FT/Sig/H/P/F 4/T(Signature)/P 1 0 R>>
    endobj
