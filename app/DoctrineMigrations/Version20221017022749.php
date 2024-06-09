<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Csv;
use Eccube\Entity\Master\CsvType;
use Eccube\Repository\CsvRepository;
use Eccube\Repository\Master\CsvTypeRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * 商品CSVに税区分を追加する
 */
final class Version20221017022749 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var EntityManager */
    private $em;

    /** @var CsvRepository */
    private $CsvRepository;

    /** @var CsvTypeRepository */
    private $CsvTypeRepository;

    /** @var array */
    private $CsvType;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');

        $this->CsvRepository = $this->em->getRepository("Eccube\Entity\Csv");
        $this->CsvTypeRepository = $this->em->getRepository("Eccube\Entity\Master\CsvType");

        $this->CsvType["Product"]  = $this->CsvTypeRepository->find(1); // 商品CSV
        $this->CsvType["Customer"] = $this->CsvTypeRepository->find(2); // 会員CSV
        $this->CsvType["Order"]    = $this->CsvTypeRepository->find(3); // 受注CSV
        $this->CsvType["Shipping"] = $this->CsvTypeRepository->find(4); // 配送CSV
        $this->CsvType["Category"] = $this->CsvTypeRepository->find(5); // カテゴリCSV
    }

    public function up(Schema $schema): void
    {
        // INSERT dtb_csv したい内容
        $arr_insert_csv = [
            [
                "csv_type"    => $this->CsvType["Product"],           // 商品CSV
                "entity_name" => "Eccube\\\\Entity\\\\ProductClass",  // 追加したい項目はどのEntityにあるか
                "disp_name"   => "税区分",                             // 表示上の項目名
                "field_name"  => "zei_kubun",                         // 項目名
                "enabled"     => false                                // true : 出力する項目, false : 出力しない項目
            ]
        ];

        $this->em->beginTransaction();

        foreach ($arr_insert_csv as $c) {
            $next_sort_no = $this->getCsvNextSortNo($c["csv_type"]);

            $Csv = new Csv();
            $Csv
                ->setCsvType($c["csv_type"])
                ->setEntityName($c["entity_name"])
                ->setDispName($c["disp_name"])
                ->setFieldName($c["field_name"])
                ->setEnabled($c["enabled"])
                ->setSortNo($next_sort_no);

            $this->em->persist($Csv);
            $this->em->flush($Csv);
        }

        $this->em->commit();
    }

    public function down(Schema $schema): void
    {
    }

    /**
     * dtb_csvをCsvTypeで絞り込み、次のsort_noの返却する。
     * @param CsvType $CsvType
     * @return int
     */
    private function getCsvNextSortNo(CsvType $CsvType)
    {
        $Csv = $this->CsvRepository->findOneBy(["CsvType" => $CsvType], ['sort_no' => 'DESC']);
        return $Csv->getSortNo() + 1;
    }
}
