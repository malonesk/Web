<?php
namespace App\Model;

use Silex\Application;
use Doctrine\DBAL\Query\QueryBuilder;;

class UserModel {

	private $db;

	public function __construct(Application $app) {
		$this->db = $app['db'];
	}

	public function verif_login_mdp_Utilisateur($login,$mdp){
		$sql = "SELECT id,login,password,droit FROM users WHERE login = ? AND password = ?";
		$res=$this->db->executeQuery($sql,[$login,$mdp]);   //md5($mdp);
		if($res->rowCount()==1)
			return $res->fetch();
		else
			return false;
	}
    public function getCommandesUser($idUser){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->select('*')
            ->from('commandes')
            ->where('user_id = ?')
            ->innerJoin('commandes','etats', 't','etat_id=t.id')
            ->setParameter(0, $idUser)
            ->addOrderBy('t.libelle', 'DESC');;

        return $queryBuilder->execute()->fetchAll();

}

    public function updateUser($donnees){
        $queryBuilder = new QueryBuilder($this->db);
        $queryBuilder
            ->update('users','p')
            ->set('p.login','?')
            ->set('p.ville','?')
            ->set('p.password','?')
            ->set('p.code_postal','?')
            ->set('p.nom','?')
            ->set('p.adresse','?')
            ->set('p.Email','?')
            ->where('p.id=?')
            ->setParameter(0,$donnees['login'])
            ->setParameter(1,$donnees['ville'])
            ->setParameter(2,$donnees['password'])
            ->setParameter(3,$donnees['code_postal'])
            ->setParameter(4,$donnees['nom'])
            ->setParameter(5,$donnees['adresse'])
            ->setParameter(6,$donnees['Email'])
            ->setParameter(7,$donnees['id'])
            ;
        $queryBuilder->execute();
    }
	public function getUser($user_id) {
		$queryBuilder = new QueryBuilder($this->db);
		$queryBuilder
			->select('*')
			->from('users')
			->where('id = :idUser')
			->setParameter('idUser', $user_id);
		return $queryBuilder->execute()->fetch();

	}
}