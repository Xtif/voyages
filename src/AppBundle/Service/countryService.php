<?php

namespace AppBundle\Service;

class countryService 
{

	private $countryRepository;
  private $em;
  private $fileService;

	public function __construct($countryRepository, $entityManager, $fileService) {
		$this->countryRepository = $countryRepository; 
    $this->em = $entityManager; 
    $this->fileService = $fileService;
	}

  public function findAllCountry() 
  {
    $allCountry = $this->countryRepository->findAll();
    if ($allCountry) {
      return $allCountry;
    } else {
      return false;
    }
  } // End of findAllCountry()

  public function findOneById($id) 
  {
    $country = $this->countryRepository->findOneById($id);
    if ($country) {
      return $country;
    } else {
      return false;
    }
  } // End of findOneById()

  public function publishCountry($id) {
    $country = $this->findOneById($id);
    if ($country) {
      $this->em->persist($country);
      $this->em->flush();
      return $country;
    } else { 
      return false;
    } 
  } // End of publishCountry()

  public function deleteCountry($id) {
    $country = $this->findOneById($id);
    if ($country) { 
      $this->fileService->deleteFolder($country->getFolder());
      $this->em->remove($country);
      $this->em->flush();
      return $country;
    } else {
    	return false;
    } 
  } // End of removeCountry()
  
} //End of class