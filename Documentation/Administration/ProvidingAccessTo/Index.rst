.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. include:: ../../Includes.txt


.. _administration-providing-access-to:

Providing access to glcrossword resources
-----------------------------------------

If you have deny the access to your /typo3conf/ext/ folder then you need to gain access at least to
the two following folders of glcrossword.

- /typo3conf/ext/glcrossword/css/
- /typo3conf/ext/glcrossword/js/

With apache you can put the following lines in your VirtualHost declaration.

::

    <Location /typo3conf/ext/glcrossword/css/>
    allow from all
    </Location>
    <Location /typo3conf/ext/glcrossword/js/>
    allow from all
    </Location>
