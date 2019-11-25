.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _programmers-reference-ajax-communication:

Ajax communication
------------------

The main class for the ajax communication is the class tx_glcrossword_ajax.

Over the session parameters will be send two values. The first is the unique ID of the current
processed crossword and the second is the method which should be accessed in the backend. The unique
ID we need to identify the crossword if we have more then one crossword in one HTML page. In the
beginning it tries to get the main crossword array from the session which is always stored in the
constuctor of the class tx_glcrossword_crossword. This array contains all crossword of the current
HTML page. The method which is sent through the session parameters is called. There are the three
possible methods at the present time. They are all given from the jQuery.ajax call in the
glcrossword javascript class for the ajax request.

===========================================  ==========================================  ====================================================
**Method PHP backend tx_glcrossword_ajax**   **Method javascript fontend glcrossword**   **Description**
-------------------------------------------  ------------------------------------------  ----------------------------------------------------
GetGeneralCrosswordData                      ProcessAjaxGeneralCrosswordData             Get the initial data like the question texts for the
                                                                                         crossword
-------------------------------------------  ------------------------------------------  ----------------------------------------------------
getSolutionData                              processAjaxSolutionData                     Get the solution for the whole crossword
-------------------------------------------  ------------------------------------------  ----------------------------------------------------
getHintData                                  processAjaxHintData                         Get a hint for one answer box
===========================================  ==========================================  ====================================================

The method **processAjaxGeneralCrosswordData**  returns the following array.

====================  =======================================================
**Component**         **Description**
--------------------  -------------------------------------------------------
questions             All Questions of the crossword with the coordinates,
                      the direction and the question text
--------------------  -------------------------------------------------------
errors                All errors inside the crossword like two questions in
                      the same box and in the same direction
--------------------  -------------------------------------------------------
errorsOOB             All errors out of the bounds of the crossword. If a
                      question is defined outside the borders of the
                      crossword, the this error is reported in this array.
--------------------  -------------------------------------------------------
isError               A general flag if there is any error in the crossword.
                      If this flag is false, the the both error arrays above
                      are empty.
--------------------  -------------------------------------------------------
LLTexts               All language dependent texts for the frontend. For
                      instance the text of the error message is stored in
                      this array.
--------------------  -------------------------------------------------------
relPath               The path to the crossword extension relative to the
                      HTML document. This path could be taken for inserting
                      further resources like images in the javascript
                      library.
--------------------  -------------------------------------------------------
size                  The size of the crossword in the x and y direction
--------------------  -------------------------------------------------------
borderWidth           The border width of the crossword for computing the
                      correct size of the HTML elements in javascript.
--------------------  -------------------------------------------------------
boxSize               The size of one box in the crossword.
--------------------  -------------------------------------------------------
editMatrix            A helper matrix for editing the crossword. The matrix
                      has the following dimensions.

                      First Index: Collumns of the crossword.

                      Second Index: Rows of the crossword.

                      Third Index:for 'fieldlength' with the length of the
                      current field

                      and for 'directions' with the following directions
                      array

                      First Index: Direction of the text.

                      See Constants with the prefix
                      tx_glcrossword_box::C_INT_EDIT_DIR\_*

                      Second Index: Side of the current box where the text is
                      going on.

                      See Constants with the prefix
                      tx_glcrossword_box::C_INT_EDIT_SIDE\_*

                      Value: Length of the text in this direction.
--------------------  -------------------------------------------------------
editCausingQuestions  For every answer box is stored which question has
                      caused this answer letter,

                      for controlling the edit mode in the crossword.

                      First Index: Collumns of the crossword.

                      Second Index: Rows of the crossword.

                      Third Index: Edit direction of the text. See

                      See Constants with the prefix
                      tx_glcrossword_box::C_INT_EDIT_DIR\_*

                      Value:x => x coordinate,

                      Y => y coordinate,

                      dir => direction of the question

                      See Constants with the prefix
                      tx_glcrossword_box_questions::C_INT_DIR\_*

                      text => <Text of the question>
====================  =======================================================

The method **processAjaxSolutionData** returns the following array.

==============  ==========================================
**Component**   **Description**
--------------  ------------------------------------------
solution        All data of the solution of the crossword.

                    Index 1: The x coordiante

                    Index 2: The y coordinate

                    value:   The answer content of this box.
==============  ==========================================

The method **ProcessAjaxHintData** returns the following array.

==============  ===========================================
**Component**   **Description**
--------------  -------------------------------------------
Hint            Just the letter of the requested answerbox.
==============  ===========================================

All data of the methods above are returned in a JSON object with two components.

==============  =======================================================
**Component**   **Description**
--------------  -------------------------------------------------------
result          The array with the result. The arrays are for the every
                method described above.
--------------  -------------------------------------------------------
intUniqueId     The unique ID of the current crossword. This is
                necessary to identify the crossword if you have more
                then one crossword in one HTML page.
==============  =======================================================

In the method glcrosswordHandleAjaxResponse of the glcrossword javascript class, we receiving the
data of all ajax responses. This method has one parameter with the actual method which should handle
the response and the JSON object with the answer data. This method is already given with the
jQuery.ajax() method call at the initial ajax request of the glcrossword class.
