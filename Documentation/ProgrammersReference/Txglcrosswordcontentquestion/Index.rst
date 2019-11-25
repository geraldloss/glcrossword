.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _programmers-reference-txglcrosswordcontentquestion:

tx_glcrossword_content_question
-------------------------------

==================  ===============  ======================================================
**Attribute**       **Protection**   **Description**
------------------  ---------------  ------------------------------------------------------
$m_intUid           Protected        UID of the question in the database
------------------  ---------------  ------------------------------------------------------
$m_strQuestion      Protected        Question text
------------------  ---------------  ------------------------------------------------------
$m_strAnswer        Protected        Answert of the question
------------------  ---------------  ------------------------------------------------------
$m_intEditMask      Protected        Edit mask of the answer. 1121 means 2 letters in the
                                     third box and one letter in box one, two and for.
------------------  ---------------  ------------------------------------------------------
$m_intActualLength  Protected        Actual length of the answer considering the edit mask.
==================  ===============  ======================================================
