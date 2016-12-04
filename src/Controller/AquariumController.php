<?php
namespace App\Controller;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;   // modif version 2.0

use Symfony\Component\HttpFoundation\Request;   // pour utiliser request

use App\Model\AquariumModel;
use App\Model\TypeAquariumModel;

use Symfony\Component\Validator\Constraints as Assert;   // pour utiliser la validation
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Security;

class AquariumController implements ControllerProviderInterface
{
    private $aquariumModel;
    private $typeAquariumModel;


    public function initModel(Application $app){  //  ne fonctionne pas dans le const
        $this->aquariumModel = new AquariumModel($app);
        $this->typeAquariumModel = new TypeAquariumModel($app);
    }
    
  

    public function index(Application $app) {
        return $this->show($app);
    }

    public function showCommandes(Application $app){
        $this->aquariumModel=new AquariumModel($app);
        $commandes =$this->aquariumModel->getAllCommandes();
        return $app["twig"]->render('backOff/backOFFICE.html.twig',['commandes'=>$commandes]);
    }
    public function show(Application $app) {
        $this->aquariumModel = new AquariumModel($app);
        $aquariums = $this->aquariumModel->getAllAquariums();
        return $app["twig"]->render('backOff/Aquarium/show.html.twig',['data'=>$aquariums]);
    }

    public function add(Application $app) {
        $this->typeAquariumModel = new TypeAquariumModel($app);
        $typeAquariums = $this->typeAquariumModel->getAllTypeAquariums();
        return $app["twig"]->render('backOff/Aquarium/add.html.twig',['typeAquariums'=>$typeAquariums,'path'=>BASE_URL]);
    }

    public function validFormAdd(Application $app, Request $req) {
       // var_dump($app['request']->attributes);
        if (isset($_POST['nom']) && isset($_POST['typeAquarium_id']) and isset($_POST['nom']) and isset($_POST['photo'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'typeAquarium_id' => htmlspecialchars($req->get('typeAquarium_id')),  //$app['request']-> ne focntionne plus
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo'))  //$req->query->get('photo')-> ne focntionne plus
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeAquarium_id']))$erreurs['typeAquarium_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';

            if(! empty($erreurs))
            {
                $this->typeAquariumModel = new TypeAquariumModel($app);
                $typeAquariums = $this->typeAquariumModel->getAllTypeAquariums();
                return $app["twig"]->render('backOff/Aquarium/add.html.twig',['donnees'=>$donnees,'erreurs'=>$erreurs,'typeAquariums'=>$typeAquariums]);
            }
            else
            {
                $this->aquariumModel = new AquariumModel($app);
                $this->aquariumModel->insertAquarium($donnees);
                return $app->redirect($app["url_generator"]->generate("aquarium.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb data form Add');
    }

    public function delete(Application $app, $id) {
        $this->typeAquariumModel = new TypeAquariumModel($app);
        $typeAquariums = $this->typeAquariumModel->getAllTypeAquariums();
        $this->aquariumModel = new AquariumModel($app);
        $donnees = $this->aquariumModel->getAquarium($id);
        return $app["twig"]->render('backOff/Aquarium/delete.html.twig',['typeAquariums'=>$typeAquariums,'donnees'=>$donnees]);
    }

    public function validFormDelete(Application $app, Request $req) {
        $id=$app->escape($req->get('id'));
        if (is_numeric($id)) {
            $this->aquariumModel = new AquariumModel($app);
            $this->aquariumModel->deleteAquarium($id);
            return $app->redirect($app["url_generator"]->generate("aquarium.index"));
        }
        else
            return $app->abort(404, 'error Pb id form Delete');
    }


    public function edit(Application $app, $id) {
        $this->typeAquariumModel = new TypeAquariumModel($app);
        $typeAquariums = $this->typeAquariumModel->getAllTypeAquariums();
        $this->aquariumModel = new AquariumModel($app);
        $donnees = $this->aquariumModel->getAquarium($id);
        return $app["twig"]->render('backOff/Aquarium/edit.html.twig',['typeAquariums'=>$typeAquariums,'donnees'=>$donnees]);
    }

    public function validFormEdit(Application $app, Request $req) {
        // var_dump($app['request']->attributes);
        if (isset($_POST['nom']) && isset($_POST['typeAquarium_id']) and isset($_POST['nom']) and isset($_POST['photo']) and isset($_POST['id'])) {
            $donnees = [
                'nom' => htmlspecialchars($_POST['nom']),                    // echapper les entrées
                'typeAquarium_id' => htmlspecialchars($req->get('typeAquarium_id')),  //$app['request']-> ne focntionne plus
                'prix' => htmlspecialchars($req->get('prix')),
                'photo' => $app->escape($req->get('photo')),  //$req->query->get('photo')-> ne focntionne plus
                'id' => $app->escape($req->get('id'))//$req->query->get('photo')
            ];
            if ((! preg_match("/^[A-Za-z ]{2,}/",$donnees['nom']))) $erreurs['nom']='nom composé de 2 lettres minimum';
            if(! is_numeric($donnees['typeAquarium_id']))$erreurs['typeAquarium_id']='veuillez saisir une valeur';
            if(! is_numeric($donnees['prix']))$erreurs['prix']='saisir une valeur numérique';
            if (! preg_match("/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/",$donnees['photo'])) $erreurs['photo']='nom de fichier incorrect (extension jpeg , jpg ou png)';
            if(! is_numeric($donnees['id']))$erreurs['id']='saisir une valeur numérique';
            $contraintes = new Assert\Collection(
                [
                    'id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'typeAquarium_id' => [new Assert\NotBlank(),new Assert\Type('digit')],
                    'nom' => [
                        new Assert\NotBlank(['message'=>'saisir une valeur']),
                        new Assert\Length(['min'=>2, 'minMessage'=>"Le nom doit faire au moins {{ limit }} caractères."])
                    ],
                    //http://symfony.com/doc/master/reference/constraints/Regex.html
                    'photo' => [
                        new Assert\Length(array('min' => 5)),
                        new Assert\Regex([ 'pattern' => '/[A-Za-z0-9]{2,}.(jpeg|jpg|png)/',
                        'match'   => true,
                        'message' => 'nom de fichier incorrect (extension jpeg , jpg ou png)' ]),
                    ],
                    'prix' => new Assert\Type(array(
                        'type'    => 'numeric',
                        'message' => 'La valeur {{ value }} n\'est pas valide, le type est {{ type }}.',
                    ))
                ]);
            $errors = $app['validator']->validate($donnees,$contraintes);  // ce n'est pas validateValue

        //    $violationList = $this->get('validator')->validateValue($req->request->all(), $contraintes);
//var_dump($violationList);

          //   die();
            if (count($errors) > 0) {
                // foreach ($errors as $error) {
                //     echo $error->getPropertyPath().' '.$error->getMessage()."\n";
                // }
                // //die();
                //var_dump($erreurs);

            // if(! empty($erreurs))
            // {
                $this->typeAquariumModel = new TypeAquariumModel($app);
                $typeAquariums = $this->typeAquariumModel->getAllTypeAquariums();
                return $app["twig"]->render('backOff/Aquarium/edit.html.twig',['donnees'=>$donnees,'errors'=>$errors,'erreurs'=>$erreurs,'typeAquariums'=>$typeAquariums]);
            }
            else
            {
                $this->aquariumModel = new AquariumModel($app);
                $this->aquariumModel->updateAquarium($donnees);
                return $app->redirect($app["url_generator"]->generate("aquarium.index"));
            }

        }
        else
            return $app->abort(404, 'error Pb id form edit');

    }
    public function valideCommande($id,Application $app){
        $this->aquariumModel=new aquariumModel($app);
        $this->aquariumModel->expedieCommande($id);
        return $app->redirect($app["url_generator"]->generate('aquarium.showCommandes'));


    }

    public function connect(Application $app) {  //http://silex.sensiolabs.org/doc/providers.html#controller-providers
        $controllers = $app['controllers_factory'];


        $controllers->get('/valideCommande/{id}','App\Controller\aquariumController::valideCommande')->bind('commande.valideCommande');
        $controllers->get('/showCommandes','App\Controller\aquariumController::showCommandes')->bind('aquarium.showCommandes');
        $controllers->get('/', 'App\Controller\aquariumController::index')->bind('aquarium.index');
        $controllers->get('/show', 'App\Controller\aquariumController::show')->bind('aquarium.show');

        $controllers->get('/add', 'App\Controller\aquariumController::add')->bind('aquarium.add');
        $controllers->post('/add', 'App\Controller\aquariumController::validFormAdd')->bind('aquarium.validFormAdd');

        $controllers->get('/delete/{id}', 'App\Controller\aquariumController::delete')->bind('aquarium.delete')->assert('id', '\d+');
        $controllers->delete('/delete', 'App\Controller\aquariumController::validFormDelete')->bind('aquarium.validFormDelete');

        $controllers->get('/edit/{id}', 'App\Controller\aquariumController::edit')->bind('aquarium.edit')->assert('id', '\d+');
        $controllers->put('/edit', 'App\Controller\aquariumController::validFormEdit')->bind('aquarium.validFormEdit');

        return $controllers;
    }
}
