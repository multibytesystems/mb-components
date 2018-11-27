<?php
namespace MBComponents\Entity\Repository;
 
use Doctrine\ORM\EntityRepository;
 
class MBInterleavedChecksumEntityRepository extends EntityRepository
{
    public function findLatest() {
        $querybuilder = $this->createQueryBuilder('a');
        $querybuilder->setMaxResults(1);
        $querybuilder->orderBy('a.id', 'DESC');

        $q = $querybuilder->getQuery();
        if (count($q->getResult()) > 0) {
            return $q->getSingleResult();
        }
        else {
            return null;
        }
    }
}
