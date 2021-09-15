<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace AlexB\AdditionalOrderAttributes\Plugin;

use Magento\Sales\Api\Data\OrderExtensionFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class OrderRepositoryPlugin
 * assigns a custom order attributes to an order object
 */
class OrderRepositoryPlugin
{
    /**
     * Order custom fields names
     */
    const CUSTOM_ATTRIBUTES = [
        'test1',
        'test2'
    ];
    /**
     * Order Extension Attributes Factory
     *
     * @var OrderExtensionFactory
     */
    protected OrderExtensionFactory $extensionFactory;

    /**
     * OrderRepositoryPlugin constructor
     * @param OrderExtensionFactory $extensionFactory
     */
    public function __construct(OrderExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * Adds "test1" and "test1" extension attributes to the order data object
     * to make it accessible in the order API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $order
     *
     * @return OrderInterface
     */
    public function afterGet(
        OrderRepositoryInterface $subject,
        OrderInterface           $order
    ): OrderInterface
    {
        $extensionAttributes = $order->getExtensionAttributes();
        $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
        $this->setAdditionalExtensionAttributes($order, $extensionAttributes);

        return $order->setExtensionAttributes($extensionAttributes);
    }

    /**
     * Gets the additional attributes data and sets these data into an order object
     *
     * @param $order
     * @param $extensionAttributes
     * @return void
     */
    protected function setAdditionalExtensionAttributes($order, $extensionAttributes)
    {
        foreach (self::CUSTOM_ATTRIBUTES as $attributeName) {
            $extensionAttributes->setData(
                $attributeName,
                $order->getData($attributeName)
            );
        }
    }

    /**
     * Adds "test1" and "test1" extension attributes to the order data object
     * to make it accessible in the order API data
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderSearchResultInterface $searchResults
     * @return OrderSearchResultInterface
     */
    public function afterGetList(
        OrderRepositoryInterface   $subject,
        OrderSearchResultInterface $searchResults
    ): OrderSearchResultInterface
    {
        foreach ($searchResults->getItems() as $order) {
            $extensionAttributes = $order->getExtensionAttributes();
            $extensionAttributes = $extensionAttributes ? $extensionAttributes : $this->extensionFactory->create();
            $this->setAdditionalExtensionAttributes($order, $extensionAttributes);
            $order->setExtensionAttributes($extensionAttributes);
            $orders[] = $order;
        }
    
        $searchResults->setItems($orders);
        return $searchResults;
    }
}
