<?php

namespace App\Model;

use Doctrine\DBAL\Query\QueryBuilder;
use Silex\Application;

class AquariumModel {

    private $db;

    public function __construct(Application $app) {
        $this->db = $app['db'];
    }
    // http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/query-builder.html#join-clauses
    public function getAllAquariums() {
//        $sql = "SELECT p.id, t.libelle, p.nom, p.prix, p.photo
//            FROM aquariums as p,typeaquarium as t
//            WHERE p.typeAquarium_id=t.id ORDER BY p.nom;";
//        $req = $this->db->query($sql);
//        return $req->fetchAll();
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.id', 't.libelle', 'p.nom', 'p.prix', 'p.photo')
            ->from('aquariums', 'p')
            ->innerJoin('p', 'typeAquarium', 't', 'p.typeAquarium_id=t.id')
            ->addOrderBy('p.nom', 'ASC');
        return $queryBuilder->execute()->fetchAll();

    }

    public function insertAquarium($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder->insert('aquariums')
            ->values([
                'nom' => '?',
                'typeAquarium_id' => '?',
                'prix' => '?',
                'photo' => '?'
            ])
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeAquarium_id'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['photo'])
        ;
        return $queryBuilder->execute();
    }
    public function expedieCommande($id){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('commandes')
            ->set('etat_id','2')
            ->where('id= ?')
            ->setParameter(0,$id);
        return $queryBuilder->execute();

    }
        public function getAllCommandes() {

        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('p.date_achat', 'u.login', 'p.prix','p.id','t.libelle')
            ->from('commandes', 'p')
            ->innerJoin('p','etats','t','p.etat_id=t.id')
            ->innerJoin('p','users','u','p.user_id=u.id');

        return $queryBuilder->execute()->fetchAll();
    
        }
    function getAquarium($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('id', 'typeAquarium_id', 'nom', 'prix', 'photo')
            ->from('aquariums')
            ->where('id= :id')
            ->setParameter('id', $id);
        return $queryBuilder->execute()->fetch();
    }

    public function updateAquarium($donnees) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('aquariums')
            ->set('nom', '?')
            ->set('typeAquarium_id','?')
            ->set('prix','?')
            ->set('photo','?')
            ->where('id= ?')
            ->setParameter(0, $donnees['nom'])
            ->setParameter(1, $donnees['typeAquarium_id'])
            ->setParameter(2, $donnees['prix'])
            ->setParameter(3, $donnees['photo'])
            ->setParameter(4, $donnees['id']);
        return $queryBuilder->execute();
    }

    public function deleteAquarium($id) {
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->delete('aquariums')
            ->where('id = :id')
            ->setParameter('id',(int)$id)
        ;
        return $queryBuilder->execute();
    }



}
