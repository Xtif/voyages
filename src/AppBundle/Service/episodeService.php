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
  } // End of findOneById()  

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

  public function findThreeLastId() 
  {
    $episodes = $this->episodeRepository->findThreeLastId();
    if ($episodes) {
      return $episodes;
    } else {
      return false;
    }
  } // End of findThreeLast()




  
} //End of class