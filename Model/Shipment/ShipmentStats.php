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
namespace Dhl\ShippingHandover\Model\Shipment;

use Dhl\Shipping\Model\ShippingInfo\OrderShippingInfoRepository;
use Dhl\Shipping\Util\ShippingProductsInterface;
use Dhl\Shipping\Webservice\UnitConverterInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\ShipmentRepositoryInterface;

/**
 * Class ShipmentStats
 *
 * @category  Dhl
 * @package   Dhl\ShippingHandover
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
class ShipmentStats
{
    /**
     * @var ShippingProductsInterface
     */
    private $shippingProducts;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var OrderShippingInfoRepository
     */
    private $shipmentRepository;

    /**
     * @var string
     */
    private $weightUom;

    /**
     * @var string[]
     */
    private $weightUomMap = [
        \Zend_Measure_Weight::KILOGRAM => 'kg',
        \Zend_Measure_Weight::POUND  => 'lb',
    ];

    /**
     * @var mixed[]
     */
    private $packages;

    /**
     * ShipmentStats constructor.
     *
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param ShippingProductsInterface $shippingProducts
     * @param UnitConverterInterface $unitConverter
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param string $weightUom
     * @param mixed[] $packages
     */
    public function __construct(
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        ShippingProductsInterface $shippingProducts,
        UnitConverterInterface $unitConverter,
        ShipmentRepositoryInterface $shipmentRepository,
        $weightUom,
        array $packages = []
    ) {
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->shippingProducts = $shippingProducts;
        $this->unitConverter = $unitConverter;
        $this->shipmentRepository = $shipmentRepository;
        $this->weightUom = $weightUom;
        $this->packages = $packages;
    }

    /**
     * @param mixed[] $package
     * @return float
     */
    private function normalizePackageWeight($package)
    {
        $packageWeight = $this->unitConverter->convertWeight(
            $package['params']['weight'],
            $package['params']['weight_units'],
            $this->weightUom
        );

        return $packageWeight;
    }

    /**
     * @return string
     */
    public function getWeightUom()
    {
        return $this->weightUomMap[$this->weightUom];
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        $weight = 0.0;
        foreach ($this->packages as $package) {
            $weight += $this->normalizePackageWeight($package);
        }

        return $weight;
    }

    /**
     * @return int
     */
    public function getNumberOfPackages()
    {
        return count($this->packages);
    }

    /**
     * @return string[]
     */
    public function getShippingProductNames()
    {
        $names = [];
        foreach ($this->packages as $package) {
            $names[] = $this->shippingProducts->getProductName($package['params']['container']);
        }

        return array_unique($names);
    }
}
