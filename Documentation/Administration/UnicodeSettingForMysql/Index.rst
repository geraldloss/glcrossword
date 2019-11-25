.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _administration-unicode-setting-for-mysql:

Unicode setting for mysql database
----------------------------------

You need to check in your install tool or in your localconf.php the parameter [SYS][setDBinit].
There you need the following configuration.

::

    SET NAMES utf8;
    SET SESSION character_set_server=utf8;

.. note::

	**Attention! Don't user this parameter in your [SYS][setDBinit]**

	::

    	SET CHARACTER SET utf8;

	This can destroy your session especially if you use umlauts or special characters in your crossword.


	For further informations see http://wiki.typo3.org/UTF-8_support.

Clear your cache after you have done this settings.
