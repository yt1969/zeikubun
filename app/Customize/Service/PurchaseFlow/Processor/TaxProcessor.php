<?php

namespace Customize\Service\PurchaseFlow\Processor;

use Eccube\Entity\Master\OrderItemType;
use Eccube\Entity\Master\TaxDisplayType;

class TaxProcessor extends \Eccube\Service\PurchaseFlow\Processor\TaxProcessor
{
    /**
     * 税表示区分を取得する.
     *
     * - 商品: 税抜
     * - 送料: 税込
     * - 値引き: 税抜
     * - 手数料: 税込
     * - ポイント値引き: 税込
     *
     * @param $OrderItemType
     *
     * @return TaxDisplayType
     */
    protected function getTaxDisplayType($OrderItemType)
    {
        if ($OrderItemType instanceof OrderItemType) {
            $OrderItemType = $OrderItemType->getId();
        }

        switch ($OrderItemType) {
            case OrderItemType::PRODUCT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
            case OrderItemType::PRODUCTINCTAX:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            case OrderItemType::DELIVERY_FEE:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            case OrderItemType::DISCOUNT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
            case OrderItemType::CHARGE:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            case OrderItemType::POINT:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::INCLUDED);
            default:
                return $this->entityManager->find(TaxDisplayType::class, TaxDisplayType::EXCLUDED);
        }
    }
}
