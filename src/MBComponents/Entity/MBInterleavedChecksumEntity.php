<?php
namespace MBComponents\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Zend\Crypt\Password\Bcrypt;

/**
 * MBInterleavedChecksumEntity
 *
 * @ORM\MappedSuperclass
 * @ORM\HasLifecycleCallbacks()
 */
Abstract class MBInterleavedChecksumEntity extends \MBComponents\Entity\MBBaseEntity
{
    /**
     * @var string
     *
     * @ORM\Column(name="simple_checksum", type="string", length=1000, nullable=false)
     */
    protected $simpleChecksum;

    /**
     * @var string
     *
     * @ORM\Column(name="interleaved_checksum", type="string", length=1000, nullable=false)
     */
    protected $interleavedChecksum;

    /**
     * @var string
     *
     * @ORM\Column(name="predecessor_id", type="integer")
     */
    protected $predecessorId;

    /**
     * @var boolean
     */
    protected $interleavedIntegrity;


    /**
     * Get simpleChecksum
     *
     * @return string
     */
    public function getSimpleChecksum()
    {
        return $this->simpleChecksum;
    }

    /**
     * Set simpleChecksum
     *
     * @param string $simpleChecksum
     *
     * @return Entity
     */
    public function setSimpleChecksum($simpleChecksum)
    {
        $this->simpleChecksum = $simpleChecksum;

        return $this;
    }

    /**
     * Get interleavedChecksum
     *
     * @return string
     */
    public function getInterleavedChecksum()
    {
        return $this->interleavedChecksum;
    }

    /**
     * Set interleavedChecksum
     *
     * @param string $interleavedChecksum
     *
     * @return Entity
     */
    public function setInterleavedChecksum($interleavedChecksum)
    {
        $this->interleavedChecksum = $interleavedChecksum;

        return $this;
    }

    /**
     * Get predecessorId
     *
     * @return string
     */
    public function getPredecessorId()
    {
        return $this->predecessorId;
    }

    /**
     * Set predecessorId
     *
     * @param string $predecessorId
     *
     * @return Entity
     */
    public function setPredecessorId($predecessorId)
    {
        $this->predecessorId = $predecessorId;

        return $this;
    }

    /**
     * Get simpleIntegrity
     *
     * @return boolean
     */
    public function getSimpleIntegrity()
    {
        // this uses bcrypt
        $bcrypt = new Bcrypt();
        return $bcrypt->verify(implode('', $this->getIntegrityData()), $this->getSimpleChecksum());

        //this uses md5
        //return ($this->getSimpleChecksum() === $this->createSimpleChecksum());
    }

    /**
     * Get interleavedIntegrity
     *
     * @return boolean
     */
    public function getInterleavedIntegrity()
    {
        return $this->interleavedIntegrity;
    }

    /**
     * Set interleavedIntegrity
     *
     * @param boolean $interleavedIntegrity
     *
     * @return Entity
     */
    public function setInterleavedIntegrity($interleavedIntegrity)
    {
        $this->interleavedIntegrity = $interleavedIntegrity;

        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function setMBComponentsData(LifecycleEventArgs $event) {
        $date = new \DateTime();
        if ($this->getCreated() == null) {
            $this->setCreated($date);

            $entityManager = $event->getEntityManager();
            $repository = $entityManager->getRepository( get_class($this) );
            $prevEntity = $repository->findLatest();

            $simpleChecksum = $this->createSimpleChecksum();
            $this->setSimpleChecksum($simpleChecksum);

            $interleavedChecksum = $this->createInterleavedChecksum($prevEntity);
            $this->setInterleavedChecksum($interleavedChecksum);
            if ($prevEntity) {
                $this->setPredecessorId($prevEntity->getId());
            }
            else {
                $this->setPredecessorId(0);
            }
        }
        $this->setModified($date);
    }

    /**
     * ORM\PostLoad
     */
    /*public function checkInterleavedIntegrity(LifecycleEventArgs $event, $referenceDate = null) {
        $referenceDate = ($referenceDate) ? $referenceDate : new \DateTime();
        //if ($this->getCreated()->format('Y-m-d') === $referenceDate->format('Y-m-d')) {
            $entityManager = $event->getEntityManager();
            $repository = $entityManager->getRepository( get_class($this) );
            $prevEntity = $repository->findPredecessor($this->getPredecessorId());

            $result = ($this->getInterleavedChecksum() === $this->createInterleavedChecksum($prevEntity));
            $this->setInterleavedIntegrity($result);

            error_log('-----');
        //}
    }*/


    /**
     * create simple checksum
     *
     * The simple checksum is created internally by using entity-data that are defined within an entity's getIntegrityData-method.
     *
     * @return string
     */
    public function createSimpleChecksum()
    {
        // this uses bcrypt
        $bcrypt = new Bcrypt();
        $simpleChecksum = $bcrypt->create(implode('', $this->getIntegrityData()));

        //this uses md5
        //$simpleChecksum = md5(implode('', $this->getIntegrityData()));

        return $simpleChecksum;
    }

    /**
     * create interleaved checksum
     *
     * The interleaved checksum is created internally by using entity-data that are defined within an entity's getIntegrityData-method
     * and the direct predescessor entity within the related database table
     *
     * @return string
     */
    public function createInterleavedChecksum($prevEntity = null)
    {
        if ($prevEntity) {
            $prevData = $prevEntity->getIntegrityData();
            $prevData[] = $prevEntity->getInterleavedChecksum();
        }
        else {
            $prevData = array();
        }

        $csData = array_merge($prevData, $this->getIntegrityData());

        // this uses bcrypt
        $bcrypt = new Bcrypt();
        $interleavedChecksum1 = $bcrypt->create(implode('', $csData));

        //this uses md5
        //$interleavedChecksum1 = md5(implode('', $csData));

        return $interleavedChecksum1;
    }

    /**
     * verify interleaved checksum
     *
     * @return boolean
     */
    public function verifyInterleavedChecksum($prevEntity = null)
    {
        if ($prevEntity) {
            $prevData = $prevEntity->getIntegrityData();
            $prevData[] = $prevEntity->getInterleavedChecksum();
        }
        else {
            $prevData = array();
        }

        $csData = array_merge($prevData, $this->getIntegrityData());

        $bcrypt = new Bcrypt();
        return $bcrypt->verify(implode('', $csData), $this->getInterleavedChecksum());
    }
    
    abstract public function getIntegrityData();
}
