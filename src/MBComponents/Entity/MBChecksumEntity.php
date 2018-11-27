<?php
namespace MBComponents\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Zend\Crypt\Password\Bcrypt;

/**
 * MBChecksumEntity
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
abstract class MBChecksumEntity extends \MBComponents\Entity\MBBaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=1000, nullable=false)
     */
    protected $checksum;


    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return Entity
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get simpleIntegrity
     *
     * @return boolean
     */
    public function getIntegrity()
    {
        // this uses bcrypt
        $bcrypt = new Bcrypt();
        return $bcrypt->verify(implode('', $this->getIntegrityData()), $this->checksum);

        //this uses md5
        //return ($this->getChecksum() === $this->createChecksum());
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setMBComponentsData(LifecycleEventArgs $event) {
        $date = new \DateTime();
        if ($this->getCreated() == null) {
            $this->setCreated($date);
            $this->setModified($date);

            $entityManager = $event->getEntityManager();
            $repository = $entityManager->getRepository( get_class($this) );

            $checksum = $this->createChecksum();
            $this->setChecksum($checksum);
        }
        else {
            $this->setModified($date);
        }
    }

    /**
     * create simple checksum
     *
     * The simple checksum is created internally by using entity-data that are defined within an entity's getIntegrityData-method.
     *
     * @return string
     */
    public function createChecksum()
    {
        // this uses bcrypt
        $bcrypt = new Bcrypt();
        $checksum = $bcrypt->create(implode('', $this->getIntegrityData()));

        //this uses md5
        //$checksum = md5(implode('', $this->getIntegrityData()));

        return $checksum;
    }
    
    abstract public function getIntegrityData();
}
