<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class TypeAquariumModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }


    public function getAllTypeAquariums() {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 'p.libelle')
            ->from('typeaquarium', 'p')
            ->addOrderBy('p.libelle', 'ASC');
        return $queryBuilder->execute()->fetchAll();
    }
}