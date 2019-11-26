.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../Includes.txt


.. _changelog:

ChangeLog
=========

========  =======================================================
Version:  Changes:
========  =======================================================
0.5.3     Completely testet in all typo3 versions from 4.5 to 6.1
--------  -------------------------------------------------------
0.5.4     Bugfix: Losing session while accessing from two
          different IP addresses to the crossword. Only the first
          IP address is working until someone has accessed the
          crossword from somewhere else.
--------  -------------------------------------------------------
0.5.5     Bugfix: Disable caching for this plugin. Otherwise the
          ajax content is not corect written in the session.

          Bugfix: Resolve UTF issue for the session. Add an extra
          troubleshooting chapter in the documentation.

          Bugfix: Resolf UTF error in the answers of the
          crossword. Answers with umlauts are now posible

          The editing of special letters with umlauts äöü or
          éà is now possible.

          Bugfix: If you click in an answerfield with more then
          one letter, then backspace don't work. This is now
          resolved.

          Compressing the javascript library of glcrossword for
          better performance.

          Insert the ext_autoload.php feature.
--------  -------------------------------------------------------
0.5.6     A turning crossword ball as the hourglass for receiving
          data from the ajax connection.

          Stop freezing the screen while click on the solution
          button. The solution is now displayed field by field in
          the crossword
--------  -------------------------------------------------------
0.5.7     If pressing the enter key, then will be the edit vector
          in the crossword changed if possible.

          Some Improvements in this Documentation

          Performance improvements in the javascript

          Map compressed javascript source to uncompressed source
          for debugging
--------  -------------------------------------------------------
0.5.8     Bugfix: Remove double IDs on the HTML elements, if you
          setup more then one crossword in a single HTML page.
          Now every ID gets the ID of the whole crossword as a
          suffix.
--------  -------------------------------------------------------
2.x       Compatibility version for Typo3 7.x, This Versions
          using still jQuery 1.10.2 and jQuery UI 1.10.3 and the
          qTip² 2.1.1 library.
--------  -------------------------------------------------------
3.x       New Version with the jQuery 3.x and Bootstrap 3.x
          library. The libraries jQuery UI and qTip² are not
          used any longer.
--------  -------------------------------------------------------
5.x       Update to extbase with fluid. Attention! This breaks with
          downward compatibility. Read Chapter :ref:`Migration<migration>` bevore you update to this version. Otherwise you may lost
          some data.
--------  -------------------------------------------------------
\
--------  -------------------------------------------------------
\
--------  -------------------------------------------------------
\
========  =======================================================
