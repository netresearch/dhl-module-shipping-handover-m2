<?php
/**
 * Dhl ShippingHandover
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to
 * newer versions in the future.
 *
 * PHP version 7
 *
 * @category  Dhl
 * @package   Dhl\ShippingHandover
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\ShippingHandover\Block\Adminhtml;

use Dhl\Shipping\Config\GlConfigInterface;
use Dhl\ShippingHandover\Model\Adminhtml\System\Config\Source\Type;
use Dhl\ShippingHandover\Model\Shipment\ShipmentStats;
use Dhl\ShippingHandover\Model\Shipment\ShipmentStatsFactory;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Collection;
use Magento\Sales\Model\Spi\ShipmentResourceInterface;
use Magento\Store\Model\ScopeInterface;
use Picqer\Barcode\BarcodeGenerator;
use Picqer\Barcode\BarcodeGeneratorPNG;

/**
 * HandoverNote Block
 *
 * @category  Dhl
 * @package   Dhl\ShippingHandover
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
class HandoverNote extends Template
{
    /**
     * @var GlConfigInterface
     */
    private $config;

    /**
     * @var BarcodeGeneratorPNG
     */
    private $barcodeGenerator;

    /**
     * @var ShipmentStats
     */
    private $shipmentStatsFactory;

    /**
     * @var ShipmentResourceInterface | Shipment
     */
    private $shipmentResource;

    /**
     * @var ShipmentStats
     */
    private $shipmentStats;

    /**
     * HandoverNote constructor.
     *
     * @param Context $context
     * @param GlConfigInterface $config
     * @param BarcodeGeneratorPNG $barcodeGenerator
     * @param ShipmentStatsFactory $shipmentStatsFactory
     * @param ShipmentResourceInterface $shipmentResource
     * @param mixed[] $data
     */
    public function __construct(
        Context $context,
        GlConfigInterface $config,
        BarcodeGeneratorPNG $barcodeGenerator,
        ShipmentStatsFactory $shipmentStatsFactory,
        ShipmentResourceInterface $shipmentResource,
        array $data = []
    ) {
        $this->config = $config;
        $this->barcodeGenerator = $barcodeGenerator;
        $this->shipmentStatsFactory = $shipmentStatsFactory;
        $this->shipmentResource = $shipmentResource;

        parent::__construct($context, $data);
    }

    /**
     * Initialize the the block with the current shipment collection.
     *
     * @param ShipmentSearchResultInterface | Collection $shipments
     * @return void
     */
    public function preparePackages(Collection $shipments)
    {
        $packages = [];
        foreach ($shipments as $shipment) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $this->shipmentResource->unserializeFields($shipment);
            $packages = array_merge($packages, $shipment->getPackages());
        }

        $this->shipmentStats = $this->shipmentStatsFactory->create([
            'packages' => $packages,
            'weightUom' => \Zend_Measure_Weight::KILOGRAM,
        ]);
    }

    /**
     * Obtain DHL logo URL from assets.
     *
     * @return string
     */
    public function getDhlImage()
    {
        return $this->_assetRepo->getUrl('Dhl_Shipping::images/dhl_shipping/dhl_logo.png');
    }

    /**
     * Obtain current localized date.
     *
     * @return string
     */
    public function getDate()
    {
        return $this->_localeDate->formatDate(null, \IntlDateFormatter::MEDIUM, true);
    }

    /**
     * Obtain current consignment number.
     *
     * @return string
     */
    public function getConsignmentNumber()
    {
        return $this->config->getConsignmentNumber();
    }

    /**
     * Obtain barcode image binary for current consignment number.
     *
     * @return string
     */
    public function getConsignmentNumberBarcode()
    {
        $barcode = $this->barcodeGenerator->getBarcode(
            $this->getConsignmentNumber(),
            BarcodeGenerator::TYPE_CODE_128,
            1.5,
            50
        );

        return $barcode;
    }

    /**
     * Obtain merchant's account number.
     *
     * @return string
     */
    public function getAccountNumber()
    {
        return $this->config->getPickupAccountNumber();
    }

    /**
     * Obtain barcode image binary for merchant's account number.
     *
     * @return string
     */
    public function getAccountNumberBarcode()
    {
        $barcode = $this->barcodeGenerator->getBarcode(
            $this->getAccountNumber(),
            BarcodeGenerator::TYPE_CODE_128,
            2,
            50
        );

        return $barcode;
    }

    /**
     * @return string
     */
    public function getPickupName()
    {
        $pickupName = $this->_scopeConfig->getValue(
            'general/store_information/name',
            ScopeInterface::SCOPE_STORE
        );

        return $pickupName;
    }

    /**
     * Obtain list of shipping products.
     *
     * @return string[]
     */
    public function getServices()
    {
        return $this->shipmentStats->getShippingProductNames();
    }

    /**
     * Obtain merchant's distribution center.
     *
     * @return string
     */
    public function getDistributionCenter()
    {
        return $this->config->getDistributionCenter();
    }

    /**
     * Obtain normalized total weight of all packages.
     *
     * @return float
     */
    public function getWeight()
    {
        return $this->shipmentStats->getWeight();
    }

    /**
     * Obtain weight unit.
     *
     * @return string
     */
    public function getWeightUnit()
    {
        return $this->shipmentStats->getWeightUom();
    }

    /**
     * Obtain total number of included packages.
     *
     * @return int
     */
    public function getNumberOfItems()
    {
        return $this->shipmentStats->getNumberOfPackages();
    }

    /**
     * Check if merchant drops off packages at a DHL facility
     *
     * @return bool
     */
    public function isDropOffActive()
    {
        $handoverType = $this->_scopeConfig->getValue('carriers/dhlshipping/shipping_handover_type');

        return ($handoverType === Type::TYPE_DROP_OFF);
    }

    /**
     * Check if merchant has packages picked up by DHL
     *
     * @return bool
     */
    public function isPickupActive()
    {
        $handoverType = $this->_scopeConfig->getValue('carriers/dhlshipping/shipping_handover_type');

        return ($handoverType === Type::TYPE_PICK_UP);
    }
}
