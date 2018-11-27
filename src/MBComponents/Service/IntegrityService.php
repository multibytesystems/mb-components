<?php

namespace MBComponents\Service;

class IntegrityService {
    protected $serviceLocator;

    public function __construct($serviceLocator) {
        $this->serviceLocator = $serviceLocator;
    }

    public function getServiceLocator() {
        return $this->serviceLocator;
    }

    public function getValidatedEntities($entityClass) {
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $entities = $em->getRepository($entityClass)->findBy([],['id' => 'ASC']);
        $lastIndex = count($entities) - 1;
        $entity = $entities[$lastIndex];

        foreach($entities as $e) {
            $this->validateInterleavedIntegrity($entities, $e);
        }
        return $entities;
    }

    protected function validateInterleavedIntegrity($entities, $entity) {
        $predecessor = $this->findPredecessor($entities, $entity);
        $result = $entity->verifyInterleavedChecksum($predecessor);

        $entity->setInterleavedIntegrity($result);
    }

    protected function findPredecessor($entities, $entity) {
        $item = null;
        foreach($entities as $obj) {
            if ($entity->getPredecessorId() == $obj->getId()) {
                $item = $obj;
                break;
            }
        }
        return $item;
    }
}