<?php
namespace App\Controller;

use App\Model\UserModel;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

class UserController implements ControllerProviderInterface {

	private $userModel;


	public function index(Application $app) {
		return $this->connexionUser($app);
	}

	public function connexionUser(Application $app)
	{
		return $app["twig"]->render('v_session_connexion.html.twig');
	}

	public function validFormConnexionUser(Application $app, Request $req)
	{

		$app['session']->clear();
		$donnees['login']=$req->get('login');
		$donnees['password']=$req->get('password');

		$this->userModel = new UserModel($app);
		$data=$this->userModel->verif_login_mdp_Utilisateur($donnees['login'],$donnees['password']);

		if($data != NULL)
		{
			$app['session']->set('droit', $data['droit']);  //dans twig {{ app.session.get('droit') }}
			$app['session']->set('login', $data['login']);
			$app['session']->set('logged', 1);
			$app['session']->set('user_id', $data['id']);
			return $app->redirect($app["url_generator"]->generate("accueil"));
		}
		else
		{
			$app['session']->set('erreur','mot de passe ou login incorrect');
			return $app["twig"]->render('v_session_connexion.html.twig');
		}
	}
	public function deconnexionSession(Application $app)
	{
		$app['session']->clear();
		$app['session']->getFlashBag()->add('msg', 'vous êtes déconnecté');
		return $app->redirect($app["url_generator"]->generate("accueil"));
	}
    public function changerCoo(Application $app){
        $this->userModel = new userModel($app);
        $infoUser = $this->userModel->getUser($app['session']->get('user_id'));
        return $app["twig"]->render('frontOff\Divers\espaceClient.html.twig',['donnees'=>$infoUser]);
    }
	public function validFormChangerCoo(Application $app){
        if(isset($_POST['nom']) && isset($_POST['login']) and isset($_POST['password']) and isset($_POST['ville']) and isset($_POST['adresse']) and isset($_POST['code_postal']) and isset($_POST['Email']) and isset($_POST['id'])){
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'login' => htmlspecialchars($_POST['login']),  //$app['request']-> ne focntionne plus
                'password' => htmlspecialchars($_POST['password']),
                'ville' => htmlspecialchars($_POST['ville']), //$req->query->get('photo')-> ne focntionne plus
                'adresse' => htmlspecialchars($_POST['adresse']),
                'code_postal' => htmlspecialchars($_POST['code_postal']),
                'Email' => htmlspecialchars($_POST['Email']),
                'id' => htmlspecialchars($_POST['id'])//$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['login']))) $erreurs['login']='login composé de 2 lettres minimum';
            if ((! preg_match("/^[A-Za-z0-9 ]{4,}/",$donnees['password']))) $erreurs['password']='Veuillez tapper votre mdp pour effectuer les changements';
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['ville']))) $erreurs['ville']='ville composé de 2 lettres minimum';
            if ((! preg_match("/^[A-Za-z0-9 ]{2,}/",$donnees['adresse']))) $erreurs['adresse']='adresse composé de 2 lettres minimum';
            if ((! preg_match("/^[0-9 ]{5,}/",$donnees['code_postal']))) $erreurs['code_postal']='code postal composé de 5 chiffres minimum';
            if ((! preg_match("/^[A-Za-z0-9 ]{2,}/",$donnees['Email']))) $erreurs['Email']='Email composé de 2 lettres minimum';

            if(! empty($erreurs))
            {
                $this->userModel = new userModel($app);
                $infoUser = $this->userModel->getUser($app['session']->get('user_id'));
                return $app["twig"]->render('frontOff\Divers\espaceClient.html.twig',['donnees'=>$infoUser,'erreurs'=>$erreurs]);
            }
            else
            {
                $this->userModel = new userModel($app);
                $this->userModel->updateUser($donnees);
                return $app->redirect($app["url_generator"]->generate("panier.index"));
            }
        }
    }

	public function connect(Application $app) {
		$controllers = $app['controllers_factory'];

        $controllers->get('/changerCoo', 'App\Controller\UserController::changerCoo')->bind('user.changerCoo');
        $controllers->put('/changerProfil', 'App\Controller\UserController::validFormChangerCoo')->bind('user.validFormChangeCoo');
		$controllers->match('/', 'App\Controller\UserController::index')->bind('user.index');
		$controllers->get('/login', 'App\Controller\UserController::connexionUser')->bind('user.login');
		$controllers->post('/login', 'App\Controller\UserController::validFormConnexionUser')->bind('user.validFormlogin');
		$controllers->get('/logout', 'App\Controller\UserController::deconnexionSession')->bind('user.logout');
		return $controllers;
	}
}