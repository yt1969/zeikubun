<?php

namespace Customize\Service;

use Eccube\Entity\OrderItem;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;
use Doctrine\Common\Collections\Collection;

class OrderHelper extends \Eccube\Service\OrderHelper
{
    /**
     * @param Collection|ArrayCollection|CartItem[] $CartItems
     *
     * @return OrderItem[]
     */
    protected function createOrderItemsFromCartItems($CartItems)
    {
        $ProductItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCT);
        $ProductIncTaxItemType = $this->orderItemTypeRepository->find(OrderItemType::PRODUCTINCTAX);
        $TaxInclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
        $TaxExclude = $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);

        return array_map(function ($item) use ($ProductItemType, $ProductIncTaxItemType, $TaxInclude, $TaxExclude) {
            /* @var $item CartItem */
            /* @var $ProductClass \Eccube\Entity\ProductClass */
            $ProductClass = $item->getProductClass();
            /* @var $Product \Eccube\Entity\Product */
            $Product = $ProductClass->getProduct();

            $OrderItem = new OrderItem();
            $OrderItem
                ->setProduct($Product)
                ->setProductClass($ProductClass)
                ->setProductName($Product->getName())
                ->setProductCode($ProductClass->getCode())
                ->setPrice($ProductClass->getPrice02())
                ->setQuantity($item->getQuantity())
                ->setTaxDisplayType($ProductClass->getZeiKubun() == 0 ? $TaxExclude : $TaxInclude)
                ->setOrderItemType($ProductClass->getZeiKubun() == 0 ? $ProductItemType : $ProductIncTaxItemType);

            $ClassCategory1 = $ProductClass->getClassCategory1();
            if (!is_null($ClassCategory1)) {
                $OrderItem->setClasscategoryName1($ClassCategory1->getName());
                $OrderItem->setClassName1($ClassCategory1->getClassName()->getName());
            }
            $ClassCategory2 = $ProductClass->getClassCategory2();
            if (!is_null($ClassCategory2)) {
                $OrderItem->setClasscategoryName2($ClassCategory2->getName());
                $OrderItem->setClassName2($ClassCategory2->getClassName()->getName());
            }

            return $OrderItem;
        }, $CartItems instanceof Collection ? $CartItems->toArray() : $CartItems);
    }

}

