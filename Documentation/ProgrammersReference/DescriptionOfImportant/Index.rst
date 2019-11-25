.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _programmers-reference-description-of-important:

Description of important arrays
-------------------------------

**Array with edit matrix**

.. code-block:: php

    * This matrix has the following dimensions.
    * First Index:     Collumns of the crossword.
    * Second Index:     Rows of the crossword.
    * Third Index:        for 'fieldlength' with the length of the current field
    *                     and for 'directions' with the following array
    *         First Index:     Direction of the text.
    *         Second Index:     Side where the text is going on.
    *         Value:         Length of the text in this direction.


Sample for directions array

==============  =========  ===========
**Direction**   **Side**   **Length**
--------------  ---------  -----------
To Bottom       Top        2
--------------  ---------  -----------
To Bottom       Bottom     3
--------------  ---------  -----------
To Top          Top
--------------  ---------  -----------
To Top          Bottom
--------------  ---------  -----------
To Left         Left
--------------  ---------  -----------
To Left         Right
--------------  ---------  -----------
To Right        Left       3
--------------  ---------  -----------
To Right        Right
==============  =========  ===========

**Array with the causing question matrix**

For every answer box is stored which question has caused this answer letter.

.. code-block:: php

         * For every answer box is stored which question has caused this answer letter,
         * for controlling the edit mode in the crossword.
         *
         * First Index: Collumns of the crossword.
         * Second Index: Rows of the crossword.
         * Third Index: Edit direction of the text. See
         *                 See Constants with the prefix tx_glcrossword_box::C_INT_EDIT_DIR_*
         * Value:    x => x coordinate,
         *             Y => y coordinate,
         *             dir => direction of the question
         *              See Constants with the prefix tx_glcrossword_box_questions::C_INT_DIR_*
         *             text => <Text of the question> */
