.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _administration-providing-access-to:

Providing access to glcrossword resources
-----------------------------------------

If you have deny the access to your /typo3conf/ext/ folder then you need to gain access at least to
the two following folder of glcrossword.

- /typo3conf/ext/glcrossword/Resources\Public\

With apache you can put the following lines in your VirtualHost declaration.

::

    <Location /typo3conf/ext/glcrossword/Resources\Public\>
		Require all granted
    </Location>
    
