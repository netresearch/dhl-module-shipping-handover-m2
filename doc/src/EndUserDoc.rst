.. |date| date:: %Y/%m/%d
.. |year| date:: %Y

.. footer::
   .. class:: footertable

   +-------------------------+-------------------------+
   | Last update: |date|     | .. class:: rightalign   |
   |                         |                         |
   |                         | ###Page###/###Total###  |
   +-------------------------+-------------------------+

.. header::
   .. image:: images/dhl.jpg
      :width: 4.5cm
      :height: 1.2cm
      :align: right

.. sectnum::

==================================================
DHL Shipping M2 Handover Note Extension
==================================================

*DHL Shipping Handover Note* is a supplemental module for the the *DHL Shipping* module for the Magento 2® eCommerce platform.

It enables merchants to create consignments and transmit them to the *DHL eCommerce Global API*, as well as fill out and print handover notes.

.. admonition:: Handover notes

    Handover notes summarize package information for drop-off at a DHL facility or pick-up at the merchant's place.

.. contents:: End user documentation

.. raw:: pdf

   PageBreak

Requirements
============

The following requirements must be met for the smooth operation of the module:

PHP
---

These PHP versions are supported:

- PHP 7.1
- PHP 7.0
- PHP 5.6

Magento 2®
----------

The following Magento 2® versions are supported:

- Community Edition 2.1.x
- Community Edition 2.2.x

DHL Shipping Module
-------------------

This module *only* works in combination with the *DHL Shipping* module for Magento 2®.


Using the module
=================

Setting up DHL Shipping
----------------------------

Please make sure the *DHL Shipping* module is configured correctly for use with the *DHL eCommerce Global API*.

The *DHL Shipping Handover Note* module *only* works in conjunction with the *DHL Shipping* module and an *eCommerce Global API* account.

Language support
----------------

The module supports the locale ``en_US``.

.. raw:: pdf

   PageBreak

Installation and configuration
==============================

This section explains how to install and configure the module.

Installation
------------

Obtain Sources
~~~~~~~~~~~~~~

Run the following commands from your project root directory to install *DHL Shipping Handover Note* via Composer:

::

    composer config repositories.module-shipping-handover-m2 vcs https://github.com/netresearch/module-shipping-handover-m2.git

::

    composer require dhl/module-shipping-handover-m2


Enable Module
~~~~~~~~~~~~~

Once the source files are available, make them known to the application:

::

    ./bin/magento module:enable Dhl_ShippingHandover

::

    ./bin/magento setup:upgrade

Finally, flush cache and compile.

::

    ./bin/magento cache:flush

::

    ./bin/magento setup:di:compile


Module configuration
--------------------

The module adds one more configuration option to the options already present in the *DHL Shipping* module:

::

    System → Configuration → Sales → Shipping Methods → DHL Shipping

From there scroll down to:

::

    Account Data eCommerce Global API → Handover Type

You can change this preference at any time.
The chosen Handover Type ("Pick-up" or "Drop-off") will be pre-selected on all successively created handover notes.

.. raw:: pdf

   PageBreak

Workflow and features
=====================

Transmitting consignment ids to the *DHL eCommerce Global API*
--------------------------------------------------------------


When creating a shipment, the *DHL Shipping Handover Note* module will transmit a consignment id corresponding to the next handover note.
All shipments will be assigned the same consignment id until a handover note for those shipments is created.
After creating a handover note, newly created shipments will be assigned a new consignment id.

Creating a handover note
------------------------

The following section describes how to use the module to print handover notes.

In the Shipments grid (``Sales → Shipments``) you can select one or more shipments.
Then choose "Print Handover Note" from the "Actions" menu to download a handover note (in ``HTML`` format).
The handover note combines the selected shipments into one consignment.

The unique id of each handover note is noted in the top right corner of the document and in the file name.

The handover note can then be opened and printed by using your web browser's native print dialog.

.. admonition:: Note on consignment ids

   The module expects that you create shipments belonging to one consignment, then print the corresponding handover
   note. Other shipments created before creating the handover note will have an incorrect consignment id transmitted
   to the *eCommerce Global API*.

.. raw:: pdf

   PageBreak

Uninstalling or disabling the module
====================================

To unregister the module from your Magento 2® installation, run the following command from your installation's root folder:

::

    ./bin/magento module:uninstall --remove-data Dhl_ShippingHandover

::

    composer remove dhl/module-shipping-handover-m2

In case you only want to *disable* the module without uninstalling it, you can do so from the Magento 2® admin panel:

::

   Stores → Configuration → Advanced → Advanced → Disable Modules Output

Technical support
=================

In case of questions or problems, please have a look at the Support Portal (FAQ) first:

::

    http://dhl.support.netresearch.de/

.. admonition:: Contacting Support

    If the problem cannot be resolved, you can contact the support team via the Support Portal or by sending an email to ``dhl.support@netresearch.de``.

