<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use AppBundle\Repository\countryRepository;
use AppBundle\Repository\episodeRepository;
use AppBundle\Repository\imageRepository;

use AppBundle\Entity\Episode;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\HttpFoundation\File\File;

class EpisodeController extends Controller
{

  private $countryService;
  private $episodeService;
  private $fileService;
  private $cleanStringService;
  private $uploadsDirectory;
  private $rootDirectory;

  public function __construct($countryService, $episodeService, $fileService, $cleanStringService, $uploadsDirectory) {
    $this->countryService = $countryService;
    $this->episodeService = $episodeService;
    $this->fileService = $fileService;
    $this->cleanStringService = $cleanStringService;
    $this->uploadsDirectory = $uploadsDirectory;
  }

  public function initializeEpisodeAction(Request $request) {

    $episode = new Episode();

    // Création du formulaire
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $episode);

    // Création des champs du formulaire
    $formBuilder
      ->add('title', TextType::class,
        array(
          'label' => 'Titre de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Titre de l\'épisode'
          )
        )
      )
      ->add('number', IntegerType::class,
        array(
          'label' => 'Numéro de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'N°'
          )
        )
      )
      ->add('dateFrom', DateType::class,
        array(
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'De'
          ),
          'label'  => 'Date de début'
        )
      )
      ->add('dateTo', DateType::class,
        array(
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'A'
          ),
          'label'  => 'Date de fin'
        )
      )
      ->add('country', EntityType::class,
        array(
          'class' => 'AppBundle:country',
          'choice_label' => 'title',
          'label' => 'Pays associé',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Pays associé'
          )
        )
      )
      ->add('mainPhoto', FileType::class,
        array(
          'label' => 'Photo principale',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Sélectionnez une photo',
          )
        )
      )
      ->add('video', FileType::class,
        array(
          'label' => 'Vidéo de l\'épisode',
          'required' => false,
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Vidéo de l\'épisode',
          )
        )
      )
      ->add('map', TextType::class,
        array(
          'label' => 'Lien de la carte',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Lien de la carte'
          )
        )
      )
      ->add('shortText', TextareaType::class,
        array(
          'label' => 'Résumé de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Résumé de l\'épisode'
          )
        )
      )
      ->add('submit', 
        SubmitType::class,
          array(
            'label' => 'Créer l\'épisode',
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

        // Ajout de la photo et de la video
        $fileNamePhoto = $this->fileService->upload($episode->getMainPhoto(), $this->getParameter('uploads_directory') . '/' . $episode->getCountry()->getFolder() . '/' . $episode->getNumber() . '/', 'Main_photo_episode_' . $episode->getNumber());
        $episode->setMainPhoto($fileNamePhoto);

        if ($episode->getVideo()) {
          $fileNameVideo = $this->fileService->upload($episode->getVideo(), $this->getParameter('uploads_directory') . '/' . $episode->getCountry()->getFolder() . '/' . $episode->getNumber() . '/', 'Video_episode_' . $episode->getNumber());
          $episode->setVideo($fileNameVideo);
        }

        //Gestion des images du corps de texte
        // $episode->setText(str_replace("../", $_SERVER['HTTP_ORIGIN'] . "/voyages/web/", $episode->getText()));
        
        $em->persist($episode);
        $em->flush();

        // On redirige vers la page suivante
        return $this->redirectToRoute('episode_read', array('country_folder' => $episode->getCountry()->getFolder(), 'episode_id' => $episode->getId()));
      } else { // Si les données ne sont pas valides
        return $this->render('default/episode_initialize.html.twig', array('form' => $form->createView()));
      }

    } else { // Si on arrive sur la page pour la première fois
      return $this->render('default/episode_initialize.html.twig', array('form' => $form->createView()));
    }
  } // End of initializeEpisode()


  public function readEpisodeAction($country_id, $episode_id) {
    $episode = $this->episodeService->findOneById($episode_id); 
    $country = $this->countryService->findOneById($country_id);
    return $this->render('default/episode_read.html.twig', array("country" => $country, "episode" => $episode));
  } // End of readEpisodeAction()


  public function updateEpisodeAction(Request $request, $episode_id) {

    $episode = $this->episodeService->findOneById($episode_id);
    $oldPhoto = $episode->getMainPhoto();
    $oldVideo = $episode->getVideo();
    $oldNumber = $episode->getNumber();
    $oldCountry = $episode->getCountry();

    // Création du formulaire
    $formBuilder = $this->get('form.factory')->createBuilder(FormType::class, $episode);

    // Création des champs du formulaire
    $formBuilder
      ->add('title', TextType::class,
        array(
          'label' => 'Titre de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Titre de l\'épisode'
          )
        )
      )
      ->add('number', IntegerType::class,
        array(
          'label' => 'Numéro de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'N°'
          )
        )
      )
      ->add('dateFrom', DateType::class,
        array(
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'De'
          ),
          'label'  => 'Date de début'
        )
      )
      ->add('dateTo', DateType::class,
        array(
          'attr'   => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'A'
          ),
          'label'  => 'Date de fin'
        )
      )
      ->add('country', EntityType::class,
        array(
          'class' => 'AppBundle:country',
          'choice_label' => 'title',
          'label' => 'Pays associé',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Pays associé'
          )
        )
      )
      ->add('mainPhoto', FileType::class,
        array(
          'data_class' => null,
          'required' => false,
          'label' => 'Photo principale',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Sélectionnez une photo',
          )
        )
      )
      ->add('video', FileType::class,
        array(
          'data_class' => null,
          'label' => 'Vidéo de l\'épisode',
          'required' => false,
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Vidéo de l\'épisode',
          )
        )
      )
      ->add('map', TextType::class,
        array(
          'label' => 'Lien de la carte',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Lien de la carte'
          )
        )
      )
      ->add('shortText', TextareaType::class,
        array(
          'label' => 'Résumé de l\'épisode',
          'attr' => array(
            'class' => 'form-control col-lg-10 m-auto',
            'placeholder' => 'Résumé de l\'épisode'
          )
        )
      )
      ->add('text', TextareaType::class,
        array(
          'label' => 'Contenu de l\'épisode',
          'required' => false,
          'attr' => array(
            'class' => 'tinymce content-episode form-control col-lg-12 m-auto'
          ),
        )
      )
      ->add('submit', 
        SubmitType::class,
          array(
            'label' => 'Modifier l\'épisode',
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

        //Renommage du folder sur le serveur
        rename($this->getParameter('uploads_directory') . '/' . $oldCountry->getFolder() . '/' . $oldNumber . '/', $this->getParameter('uploads_directory') . '/' . $episode->getCountry()->getFolder() . '/' . $episode->getNumber() . '/');

        if ($episode->getMainPhoto()) {
          // Modification de la photo
          $fileName = $this->fileService->update($episode->getMainPhoto(), $oldPhoto, 'Main_photo_episode_' . $episode->getNumber(), $this->getParameter('uploads_directory') . '/' . $episode->getCountry()->getFolder() . '/' . $episode->getNumber() . '/');
          $episode->setMainPhoto($fileName);
        } else {
          $episode->setMainPhoto($oldPhoto);
        }

        if ($episode->getVideo()) {
          // Modification de la video
          $fileNameVideo = $this->fileService->update($episode->getVideo(), $oldVideo, 'Main_video_episode_' . $episode->getNumber(), $this->getParameter('uploads_directory') . '/' . $episode->getCountry()->getFolder() . '/' . $episode->getNumber() . '/');
          $episode->setVideo($fileNameVideo);
        } else {
          $episode->setVideo($oldVideo);
        }

        //Gestion des images du corps de texte
        // $episode->setText(str_replace("../../", $_SERVER['HTTP_ORIGIN'] . "/voyages/web/", $episode->getText()));

        $em->persist($episode);
        $em->flush();

        // On redirige vers la page suivante
        return $this->redirectToRoute('episode_read', array('country_folder' => $episode->getCountry()->getFolder(), 'episode_id' => $episode->getId()));
      } else { // Si les données ne sont pas valide
        $photo = $episode->getMainPhoto();
        return $this->render('default/episode_update.html.twig', array('form' => $form->createView(), 'country_folder' => $episode->getCountry()->getFolder(), 'episode' => $episode));
      }

    } else { // Si on arrive sur la page pour la première fois 
      return $this->render('default/episode_update.html.twig', array('form' => $form->createView(), 'country_folder' => $episode->getCountry()->getFolder(), 'episode' => $episode));
    }    
  } // End of updateEpisode()


  public function deleteEpisodeAction($episode_id) {
    if ($this->episodeService->deleteEpisode($episode_id)) {
      $this->addFlash('Success', 'Le pays a bien été supprimé !');
      return $this->redirectToRoute('episode_list');
    } else {
      $this->addFlash('Error', 'Ce pays n\'existe pas !');
      return $this->redirectToRoute('episode_list');
    }
  } // End of deleteEpisodeAction()


  public function listEpisodeAction() {
    $allEpisode = $this->episodeService->findAllEpisode();
    return $this->render('default/episode_list.html.twig', array("allEpisode" => $allEpisode));
  } // End of episodeListAction()


  public function imageUploadAction(Request $request) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);

    //Recupere la date du jour et la formate
    $date = new \DateTime();
    $dateFormat = $date->format('Y-m-d-h-i-s'); 

    //Prend la derniere image ajoutée
    reset($_FILES);
    $tmpFile = current($_FILES);

    //Recupere l'extension et crée le nouveau chemin
    $ext = pathinfo($tmpFile['name'], PATHINFO_EXTENSION);
    $fileToWrite = 'uploads/uploaded_photo/' . $dateFormat . '.' . $ext;

    //Déplace le fichier dans uploaded photo
    move_uploaded_file($tmpFile['tmp_name'], $fileToWrite);

    //Retourne le chemin de ficher en json
    $returnPath = $fileToWrite;
    $return = json_encode(array('location' => $returnPath), JSON_UNESCAPED_SLASHES);
    return new Response($return);
  }

/*

  public function publishAction($id) {
    $observation = $this->observationsService->publishObservation($id);
    if ($observation) {
      $this->addFlash('Success', 'L\'observation a bien été publié !');
      return $this->redirectToRoute('observation', array("id" => $id));
    } else {
      $this->addFlash('Error', 'Cette observation n\'existe pas !');
      return $this->redirectToRoute('error_page');
    }
  } // End of publishAction()

*/

} // End of class
