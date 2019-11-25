.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _programmers-reference-txglcrosswordcrossword:

tx_glcrossword_crossword
------------------------

========================  ===============  =======================================================
**Attribute**             **Protection**   **Description**
------------------------  ---------------  -------------------------------------------------------
$m_arrCrosswords          Static Public    Global Array with all Crosswords
------------------------  ---------------  -------------------------------------------------------
$m_intUniqueId            Protected        The unique ID of this Crossword
------------------------  ---------------  -------------------------------------------------------
$m_intWidthOfCrossword    Protected        Width of the crossword.
------------------------  ---------------  -------------------------------------------------------
$m_intHeighthOfCrossword  Protected        Height of the crossword.
------------------------  ---------------  -------------------------------------------------------
$m_arrBoxes[x,y]          Protected        Array where every box is stored with ist x and y
                                           coordinates.
------------------------  ---------------  -------------------------------------------------------
$m_arrErrorBoxes          Protected        Array with boxes, which are out of bound or with
                                           similair failures.
------------------------  ---------------  -------------------------------------------------------
$m_blnIsError             Protected        If this flag is true, then is in the whole crossword at
                                           least one box with error.
------------------------  ---------------  -------------------------------------------------------
$m_strErrorDialog         Protected        The content of the error dialog widget on the frontend
------------------------  ---------------  -------------------------------------------------------
$m_objPiBase              Public           The tslib_pibase for some helper methods.
========================  ===============  =======================================================
