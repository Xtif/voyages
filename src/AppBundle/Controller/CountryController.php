<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Repository\countryRepository;
use AppBundle\Repository\episodeRepository;
use AppBundle\Repository\imageRepository;

use AppBundle\Entity\Country;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\File;


class CountryController extends Controller
{

  private $countryService;
  private $episodeService;
  private $fileService;
  private $cleanStringService;
  private $uploadsDirectory;

  public function __construct($countryService, $episodeService, $fileService, $cleanStringService, $uploadsDirectory) {
    $this->countryService = $countryService;
    $this->episodeService = $episodeService;
    $this->fileService = $fileService;
    $this->cleanStringService = $cleanStringService;
    $this->uploadsDirectory = $uploadsDirectory;
  }

  public function createCountryAction(Request $request) {

    $country = new Country();

    // Création du formulaire
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $country);

    // Création des champs du formulaire
    $formBuilder
      ->add('title', TextType::class,
        array(
          'label' => 'Nom du pays',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Nom du pays'
          )
        )
      )
      ->add('date', ChoiceType::class,
        array(
          'choices' => array(
            date('Y') => date('Y'),
            date('Y')-1 => date('Y')-1,
            date('Y')-2 => date('Y')-2,
            date('Y')-3 => date('Y')-3,
            date('Y')-4 => date('Y')-4,
            date('Y')-5 => date('Y')-5,
            date('Y')-6 => date('Y')-6,
            date('Y')-7 => date('Y')-7,
            date('Y')-8 => date('Y')-8,
            date('Y')-9 => date('Y')-9,
            date('Y')-10 => date('Y')-10
          ),
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Année'
          ),
          'label'  => 'Année'
        )
      )
      ->add('photo', FileType::class,
        array(
          'label' => 'Photo principale (5184 x 3456 px - 72 dpi)',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Sélectionnez une photo',
          )
        )
      )
      ->add('submit', 
        SubmitType::class,
          array(
            'label' => 'Créer la fiche du pays',
            'attr' => array(
              'class' => 'btn btn-info'
            )
          )
      );

    // On génère le formulaire
    $form = $formBuilder->getForm();

    // Si le formulaire est soumis
    if ($request->isMethod('POST')) {

      // On fait le lien Requete <=> Formulaire, la variable $observation contient les valeurs entrées dans le formulaire
      $form->handleRequest($request);

      // Si les données sont correctes
      if ($form->isValid()) { 

        // On recupère l'entity manager
        $em = $this->getDoctrine()->getManager();

        //Suppression des accents et espaces pour le folder de sauvegarde
        $cleanFolderName = $this->cleanStringService->cleanString($country->getTitle());

        $country->setFolder($cleanFolderName);

        // Ajout de la photo
        $mainPhotoName = 'Main_photo_' . $cleanFolderName;
        $fileName = $this->fileService->upload($country->getPhoto(), $this->getParameter('uploads_directory') . '/' . $cleanFolderName . '/', $mainPhotoName);

        $country->setPhoto($fileName);

        $em->persist($country);
        $em->flush();

        // On redirige vers la page suivante
        return $this->redirectToRoute('country_read', array('country_id' => $country->getId()));
      } else { // Si les données ne sont pas valides
        return $this->render('default/country_create.html.twig', array('form' => $form->createView()));
      }

    } else { // Si on arrive sur la page pour la première fois
      return $this->render('default/country_create.html.twig', array('form' => $form->createView()));
    }
  } // End of createCountry()


  public function readCountryAction($country_id) {
    $country = $this->countryService->findOneById($country_id);  
    return $this->render('default/country_read.html.twig', array("country" => $country, "episodes" => $country->getEpisodes()));
  } // End of readCountryAction()


  public function updateCountryAction(Request $request, $country_id) {

    $country = $this->countryService->findOneById($country_id);
    $oldPhoto = $country->getPhoto();
    $oldFolderName = $country->getFolder();

    // Création du formulaire
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $country);

    // Création des champs du formulaire
    $formBuilder
      ->add('title', TextType::class,
        array(
          'label' => 'Modifier le nom du pays',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Nom du pays'
          )
        )
      )
      ->add('date', ChoiceType::class,
        array(
          'choices' => array(
            date('Y') => date('Y'),
            date('Y')-1 => date('Y')-1,
            date('Y')-2 => date('Y')-2,
            date('Y')-3 => date('Y')-3,
            date('Y')-4 => date('Y')-4,
            date('Y')-5 => date('Y')-5,
            date('Y')-6 => date('Y')-6,
            date('Y')-7 => date('Y')-7,
            date('Y')-8 => date('Y')-8,
            date('Y')-9 => date('Y')-9,
            date('Y')-10 => date('Y')-10
          ),
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Modifier l\'année'
          ),
          'label'  => 'Année'
        )
      )
      ->add('photo', FileType::class,
        array(
          'data_class' => null,
          'required' => false,
          'label' => 'Modifier la photo principale (5184 x 3456 px - 72 dpi)',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Sélectionnez une photo',
          )
        )
      )
      ->add('submit', 
        SubmitType::class,
          array(
            'label' => 'Modifier la fiche du pays',
            'attr' => array(
              'class' => 'btn btn-info'
            )
          )
      );

    // On génère le formulaire
    $form = $formBuilder->getForm();

    // Si le formulaire est soumis
    if ($request->isMethod('POST')) {

      // On fait le lien Requete <=> Formulaire, la variable $observation contient les valeurs entrées dans le formulaire
      $form->handleRequest($request);

      // Si les données sont correctes
      if ($form->isValid()) { 

        // On recupère l'entity manager
        $em = $this->getDoctrine()->getManager();

        //Suppression des accents pour le folder de sauvegarde
        $cleanFolderName = $this->cleanStringService->cleanString($country->getTitle());

        //Assignation du folder dans la BDD
        $country->setFolder($cleanFolderName);

        //Renommage du folder sur le serveur
        rename($this->getParameter('uploads_directory') . '/' . $oldFolderName . '/', $this->getParameter('uploads_directory') . '/' . $cleanFolderName . '/');

        if ($country->getPhoto()) {
          // Modification de la photo
          $fileName = $this->fileService->update($country->getPhoto(), $oldPhoto, 'Main_photo', $this->getParameter('uploads_directory') . '/' . $cleanFolderName . '/');
          $country->setPhoto($fileName);
        } else {
          $country->setPhoto($oldPhoto);
        }

        $em->persist($country);
        $em->flush();

        // On redirige vers la page suivante
        return $this->redirectToRoute('country_read', array('country_id' => $country->getId()));
      } else { // Si les données ne sont pas valide
        $photo = $country->getPhoto();
        return $this->render('default/country_update.html.twig', array('form' => $form->createView(), 'country' => $country));
      }

    } else { // Si on arrive sur la page pour la première fois 
      return $this->render('default/country_update.html.twig', array('form' => $form->createView(), 'country' => $country));
    }
  } // End of createCountry()


  public function deleteCountryAction($country_id) {
    if ($this->countryService->deleteCountry($country_id)) {
      $this->addFlash('Success', 'Le pays a bien été supprimé !');
      return $this->redirectToRoute('country_list');
    } else {
      $this->addFlash('Error', 'Ce pays n\'existe pas !');
      return $this->redirectToRoute('country_list');
    }
  } // End of removeAction()


  public function listCountryAction() {
    $allCountry = $this->countryService->findAllCountry();
    return $this->render('default/country_list.html.twig', array("allCountry" => $allCountry));
  } // End of countryListAction()

} // End of class
