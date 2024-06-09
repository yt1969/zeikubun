<?php

/*
 * This file is part of EC-CUBE
 *
 * Copyright(c) EC-CUBE CO.,LTD. All Rights Reserved.
 *
 * http://www.ec-cube.co.jp/
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Customize\Service;

use Eccube\Entity\ProductClass;

class TaxRuleService extends \Eccube\Service\TaxRuleService
{
    /**
     * 設定情報に基づいて税金の金額を返す
     *
     * @param  int                                    $price        計算対象の金額
     * @param  int|\Eccube\Entity\Product|null        $product      商品
     * @param  int|\Eccube\Entity\ProductClass|null   $productClass 商品規格
     * @param  int|\Eccube\Entity\Master\Pref|null    $pref         都道府県
     * @param  int|\Eccube\Entity\Master\Country|null $country      国
     *
     * @return double                                 税金付与した金額
     */
    public function getTax($price, $product = null, $productClass = null, $pref = null, $country = null)
    {
        /*
         * 商品別税率が有効で商品別税率が設定されている場合は商品別税率
         * 商品別税率が有効で商品別税率が設定されていない場合は基本税率
         * 商品別税率が無効の場合は基本税率
         */
        /* @var $TaxRule \Eccube\Entity\TaxRule */
        if ($this->BaseInfo->isOptionProductTaxRule() && $productClass) {
            if ($productClass instanceof ProductClass) {
                $TaxRule = $productClass->getTaxRule() ?: $this->taxRuleRepository->getByRule(null, null, $pref, $country);
            } else {
                $TaxRule = $this->taxRuleRepository->getByRule($product, $productClass, $pref, $country);
            }
        } else {
            $TaxRule = $this->taxRuleRepository->getByRule(null, null, $pref, $country);
        }
        
        /* @var $ZeiKubun \Eccube\Entity\ProductClass */
        $ZeiKubun = $productClass->getZeiKubun();

        return $this->calcTax($price, $TaxRule->getTaxRate(), $TaxRule->getRoundingType()->getId(), $TaxRule->getTaxAdjust(), $ZeiKubun);
    }

    /**
     * calcIncTax
     *
     * @param  int                                    $price        計算対象の金額
     * @param  int|\Eccube\Entity\Product|null        $product      商品
     * @param  int|\Eccube\Entity\ProductClass|null   $productClass 商品規格
     * @param  int|\Eccube\Entity\Master\Pref|null    $pref         都道府県
     * @param  int|\Eccube\Entity\Master\Country|null $country      国
     *
     * @return double
     */
    public function getPriceIncTax($price, $product = null, $productClass = null, $pref = null, $country = null)
    {
        if ($productClass->getZeiKubun() == 0) {
            return $price + $this->getTax($price, $product, $productClass, $pref, $country);
        } else {
            return $price;
        }
    }

    /**
     * 税金額を計算する
     *
     * @param  int    $price     計算対象の金額
     * @param  int    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  int    $taxAdjust 調整額
     * @param  int    $ZeiKubun  税区分
     *
     * @return double 税金額
     */
    public function calcTax($price, $taxRate, $RoundingType, $taxAdjust = 0, $ZeiKubun = 0)
    {
        if($ZeiKubun == 0) {
            $tax = $price * $taxRate / 100;
        } else {
            $tax = $price / ($taxRate + 100) * $taxRate;
        }
        $roundTax = self::roundByRoundingType($tax, $RoundingType);

        return $roundTax + $taxAdjust;
    }

    /**
     * 税込金額から税金額を計算する
     *
     * @param  int    $price     計算対象の金額
     * @param  int    $taxRate   税率(%単位)
     * @param  int    $RoundingType  端数処理
     * @param  int    $taxAdjust 調整額
     *
     * @return float  税金額
     */
    public function calcTaxIncluded($price, $taxRate, $RoundingType, $taxAdjust = 0)
    {
        $tax = ($price - $taxAdjust) * $taxRate / (100 + $taxRate);

        return self::roundByRoundingType($tax, $RoundingType);
    }

    /**
     * 課税規則に応じて端数処理を行う
     *
     * @param  integer $value    端数処理を行う数値
     * @param integer $RoundingType
     *
     * @return double        端数処理後の数値
     */
    public static function roundByRoundingType($value, $RoundingType)
    {
        switch ($RoundingType) {
            // 四捨五入
            case \Eccube\Entity\Master\RoundingType::ROUND:
                $ret = round($value);
                break;
            // 切り捨て
            case \Eccube\Entity\Master\RoundingType::FLOOR:
                $ret = floor($value);
                break;
            // 切り上げ
            case \Eccube\Entity\Master\RoundingType::CEIL:
                $ret = ceil($value);
                break;
            // デフォルト:切り上げ
            default:
                $ret = ceil($value);
                break;
        }

        return $ret;
    }
}
