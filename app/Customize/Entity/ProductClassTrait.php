<?php

namespace Customize\Entity;

use Doctrine\ORM\Mapping as ORM;
use Eccube\Annotation\EntityExtension;

/**
  * @EntityExtension("Eccube\Entity\ProductClass")
 */
trait ProductClassTrait
{
    /**
     * @var boolean|null
     * 
     * @ORM\Column(name="zei_kubun", type="boolean", nullable=false, options={"default" : 0})
     */
    private $zei_kubun;

    /**
     * Set zei_kubun.
     *
     * @param boolean|null $zei_kubun
     *
     * @return ProductClassTrait
     */
    public function setZeiKubun($zei_kubun = null)
    {
        $this->zei_kubun = $zei_kubun;

        return $this;
    }

    /**
     * Get zei_kubun.
     *
     * @return boolean|null
     */
    public function getZeiKubun()
    {
        return $this->zei_kubun;
    }
    
}

