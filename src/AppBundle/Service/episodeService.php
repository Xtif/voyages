<?php

namespace AppBundle\Service;

class episodeService 
{

	private $episodeRepository;
  private $em;
  private $fileService;  

	public function __construct($episodeRepository, $entityManager, $fileService) {
		$this->episodeRepository = $episodeRepository; 
    $this->em = $entityManager;
    $this->fileService = $fileService;
	}

  // Renommage des src des images dans le text des épisodes
  public function renamePathPicture() {
    $episodes = $this->findAllEpisode();
    if ($episodes) {
      foreach ($episodes as $episode) {
        if ($episode->getId() > 7) {
          $oldText = $episode->getText();
          $dateEpisode = date_format($episode->getDateFrom(),"Y_m_d");
          $oldPath = $episode->getCountry()->getFolder() . "/" . $dateEpisode . $episode->getCountry()->getFolder() . "/" . $dateEpisode . "/" . $episode->getCountry()->getFolder();
          $newPath = $episode->getCountry()->getFolder();
          $newText = str_replace($oldPath, $newPath, $oldText);
          $episode->setText($newText);
          $this->em->persist($episode);
        }
      }
      $this->em->flush();
      return true;
    } else {
      return false;
    }
  } // End of renamePathPicture()

  public function findAllEpisode() 
  {
    $allEpisode = $this->episodeRepository->findAll();
    if ($allEpisode) {
      return $allEpisode;
    } else {
      return false;
    }
  } // End of findAllEpisode()

  public function findOneById($id) 
  {
    $episode = $this->episodeRepository->findOneById($id);
    if ($episode) {
      return $episode;
    } else {
      return false;
    }
  } // End of findOneById()

  public function findAllByCountry($country) 
  {
    $episodes = $this->episodeRepository->getEpisodesByCountry($country);
    if ($episodes) {
      return $episodes;
    } else {
      return false;
    }
  } // End of findAllByCountry()  

  public function findAllByCountryByDate($country) 
  {
    $episodes = $this->episodeRepository->getEpisodesByCountryByDate($country);
    if ($episodes) {
      return $episodes;
    } else {
      return false;
    }
  } // End of findAllByCountryByDate()  

  public function draftEpisode($id) {
    $episode = $this->findOneById($id);
    if ($episode) {
      $episode->setState("Brouillon");
      $this->em->persist($episode);
      $this->em->flush();
      return $episode;
    } else { 
      return false;
    } 
  } // End of draftEpisode()

  public function publishEpisode($id) {
    $episode = $this->findOneById($id);
    if ($episode) {
      $episode->setState("En ligne");
      $this->em->persist($episode);
      $this->em->flush();
      return $episode;
    } else { 
      return false;
    } 
  } // End of publishEpisode()

  public function removeEpisode($id) {
    $episode = $this->findOneById($id);
    if ($episode) { 
      $episode->setState("Corbeille");
      $this->em->persist($episode);
      $this->em->flush();
      return $episode;
    } else {
    	return false;
    } 
  } // End of removeEpisode()

  public function deleteEpisode($id) {
    $episode = $this->findOneById($id);
    if ($episode) { 
      $this->fileService->deleteFolder($episode->getCountry()->getFolder() . '/' . $episode->getNumber());
      $this->em->remove($episode);
      $this->em->flush();
      return true;
    } else {
      return false;
    } 
  } // End of removeEpisode()

  public function findThreeLastId() {
    $episodes = $this->episodeRepository->findThreeLastId();
    if ($episodes) {
      return $episodes;
    } else {
      return false;
    }
  } // End of findThreeLast()


  public function reorderNumberEpisodeByCountry($country) { // Réorganise les numéro d'épisodes en fonction des dates (dateFrom)
    $episodes = $this->findAllByCountryByDate($country);
    if ($episodes) {
      $i = 1;
      foreach ($episodes as $episode) {
        $episode->setNumber($i);
        $this->em->persist($episode);
        $i ++;
      }
      $this->em->flush(); 
      return true;      
    } else {
      return false;
    }
  } // End of reorderNumberEpisodeByCountry($country)






  
} //End of class