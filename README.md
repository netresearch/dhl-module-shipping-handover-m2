DHL Shipping Handover Extension
===============================

The DHL Shipping Handover extension for Magento® 2 adds handover note capabilities.

Facts
-----
* version: 0.1.0

Description
-----------
This extension enables merchants to create handover notes from the shipments grid.
Handover notes summarize package information for drop-off at a DHL facility or
pick-up at the merchant's place.

Requirements
------------
* PHP >= 5.6.5
* PHP >= 7.0.6

Compatibility
-------------
* Magento >= 2.1.4

Installation Instructions
-------------------------

### Obtain Sources ###
Run the following commands in your project root directory:

    composer config repositories.module-shipping-handover-m2 vcs https://github.com/netresearch/module-shipping-handover-m2.git
    composer require dhl/module-shipping-handover-m2

### Enable Module ###
Once the source files are available, make them known to the application:

    ./bin/magento module:enable Dhl_ShippingHandover
    ./bin/magento setup:upgrade

Last but not least, flush cache and compile.

    ./bin/magento cache:flush
    ./bin/magento setup:di:compile

Uninstallation
--------------

To unregister the shipping module from the application, run the following command:

    ./bin/magento module:uninstall --remove-data Dhl_ShippingHandover
    composer remove dhl/module-shipping-handover-m2

Support
-------
In case of questions or problems, please have a look at the
[Support Portal (FAQ)](http://dhl.support.netresearch.de/) first.

If the issue cannot be resolved, you can contact the support team via the
[Support Portal](http://dhl.support.netresearch.de/) or by sending an email
to <dhl.support@netresearch.de>.

Developer
---------
* Max Melzer | [Netresearch GmbH & Co. KG](http://www.netresearch.de/)
* Andreas Müller | [Netresearch GmbH & Co. KG](http://www.netresearch.de/)

License
-------
[OSL - Open Software Licence 3.0](http://opensource.org/licenses/osl-3.0.php)

Copyright
---------
(c) 2017 DHL eCommerce
