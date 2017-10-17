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
 * @author    Andreas Müller <andreas.mueller@netresearch.de>
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2017 Netresearch GmbH & Co. KG
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link      http://www.netresearch.de/
 */
namespace Dhl\ShippingHandover\Controller\Adminhtml\Note;

use Dhl\Shipping\Config\GlConfigInterface;
use Dhl\ShippingHandover\Block\Adminhtml\HandoverNote;
use Magento\Backend\App\Action;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Ui\Component\MassAction\Filter;

/**
 * Provide handover note as download response for given shipments.
 *
 * @category Dhl
 * @package  Dhl\ShippingHandover
 * @author   Andreas Müller <andreas.mueller@netresearch.de>
 * @author   Max Melzer <max.melzer@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de
 */
class Printout extends Action
{
    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var FileFactory
     */
    private $fileFactory;

    /**
     * @var GlConfigInterface
     */
    private $config;

    /**
     * Printout constructor.
     *
     * @param Action\Context $context
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param SearchCriteriaBuilder $criteriaBuilder
     * @param Filter $filter
     * @param FileFactory $fileFactory
     * @param GlConfigInterface $config
     */
    public function __construct(
        Action\Context $context,
        ShipmentRepositoryInterface $shipmentRepository,
        SearchCriteriaBuilder $criteriaBuilder,
        Filter $filter,
        FileFactory $fileFactory,
        GlConfigInterface $config
    ) {
        $this->shipmentRepository = $shipmentRepository;
        $this->searchCriteriaBuilder = $criteriaBuilder;
        $this->filter = $filter;
        $this->fileFactory = $fileFactory;
        $this->config = $config;

        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $this->_view->loadLayout();

        /** @var AbstractDb $searchResult */
        $searchResult = $this->shipmentRepository->getList($this->searchCriteriaBuilder->create());
        /** @var ShipmentSearchResultInterface $shipmentCollection */
        $shipmentCollection = $this->filter->getCollection($searchResult);

        /** @var HandoverNote $block */
        $block = $this->_view->getLayout()->getBlock('shipment_handover_note');
        $block->preparePackages($shipmentCollection);

        $html = $block->toHtml();
        $filename = sprintf('handover-%s.html', $this->config->getConsignmentNumber());

        $this->config->incrementConsignmentNumber();

        // send handover note to client
        $response = $this->fileFactory->create(
            $filename,
            $html,
            DirectoryList::VAR_DIR,
            'text/html'
        );

        return $response;
    }
}
