<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;
use Doctrine\ORM\EntityManager;
use Eccube\Entity\Master\OrderItemType;
use Eccube\Repository\Master\OrderItemTypeRepository;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * mtb_order_item_typeに税込商品を追加する
 */
final class Version20221017022750 extends AbstractMigration implements ContainerAwareInterface
{
    /** @var ContainerInterface */
    private $container;

    /** @var EntityManager */
    private $em;

    /** @var OrderItemTypeRepository */
    private $OrderItemTypeRepository;

    /** @var array */
    private $OrderItemType;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
        $this->em = $this->container->get('doctrine.orm.entity_manager');
    }

    public function up(Schema $schema): void
    {
        // INSERT mtb_order_item_type したい内容
        $arr_insert_mtb_order_item_type = [
            [
                "id"      => "7",
                "name"    => "税込商品",
                "sort_no" => "6"
            ]
        ];

        $this->em->beginTransaction();

        foreach ($arr_insert_mtb_order_item_type as $c) {
            $OrderItemType = new OrderItemType();
            $OrderItemType
                ->setId($c["id"])
                ->setName($c["name"])
                ->setSortNo($c["sort_no"]);

            $this->em->persist($OrderItemType);
            $this->em->flush($OrderItemType);
        }

        $this->em->commit();
    }

    public function down(Schema $schema): void
    {
    }
}
