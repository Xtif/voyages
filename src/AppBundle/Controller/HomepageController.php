<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use AppBundle\Entity\Subscriber;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class HomepageController extends Controller
{

    private $countryService;
    private $subscriberService;
    private $episodeService;

    public function __construct($countryService, $subscriberService, $episodeService) {
      $this->countryService = $countryService;
      $this->subscriberService = $subscriberService;
      $this->episodeService = $episodeService;
    }

    public function indexAction(Request $request)
    {
        $allCountry = $this->countryService->findAllCountry();
        $lastEpisodes = $this->episodeService->findThreeLastId();

        $subscriber = new Subscriber();

	    // Création du formulaire
	    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $subscriber);

	    // Création des champs du formulaire
	    $formBuilder
		    ->add('email', EmailType::class,
			    array(
			    	'label' => 'Entrer votre email',
			    	'label_attr' => array(
			    		'class' => 'm-0 mt-1 align-text-bottom'
			    	),
			    	'attr' => array
			    	(
			        	'class' => 'form-control col-lg-6 ml-2 mr-2',
			        	'placeholder' => 'Email'
			    	)
			    )
		    )
	    	->add('submit', SubmitType::class,
	        	array(
	            	'label' => 'S\'abonner',
	            	'attr' => array
	            	(
	            		'class' => 'btn btn-info align-center'
	            	)
	        	)
	    	);

	    // On génère le formulaire
	    $form = $formBuilder->getForm();

	    // Si le formulaire est soumis
	    if ($request->isMethod('POST')) 
	    {

		    // On fait le lien Requete <=> Formulaire, la variable $observation contient les valeurs entrées dans le formulaire
		    $form->handleRequest($request);

		    // Si les données sont correctes
		    if ($form->isValid()) 
		    { 

		    	// On recupère l'entity manager
		        $em = $this->getDoctrine()->getManager();

		        if ($this->subscriberService->subscribe($subscriber)) 
		        {
		        	$this->addFlash('Success', 'Vous êtes désormais abonné !');
		        } else {
		        	$this->addFlash('Error', 'Une erreur s\'est produite, veuillez réessayer !');
		        }

		        // On redirige vers la page suivante
		        return $this->redirectToRoute('homepage', array('allCountry' => $allCountry, 'lastEpisodes' => $lastEpisodes));
		    } else { // Si les données ne sont pas valides
		    	return $this->render('default/homepage.html.twig', array('form' => $form->createView(), 'allCountry' => $allCountry, 'lastEpisodes' => $lastEpisodes));
		    }

	    } else { // Si on arrive sur la page pour la première fois
	      return $this->render('default/homepage.html.twig', array('form' => $form->createView(), 'allCountry' => $allCountry, 'lastEpisodes' => $lastEpisodes));
	    }
	} //End homepageAction
	       
} //End class
