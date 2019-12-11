<?php

namespace AppBundle\Service;

class subscriberService 
{

	private $subscriberRepository;
  private $em;

	public function __construct($subscriberRepository, $entityManager) {
		$this->subscriberRepository = $subscriberRepository; 
    $this->em = $entityManager; 
	}

  public function subscribe($subscriber) {
    try {
      $this->em->persist($subscriber);
      $this->em->flush();
      return true;
    } catch (SOMEEXCEPTION $e) {
      return false;
    }
  }

  public function findAllSubscriber()
  {
    $allSubscriber = $this->subscriberRepository->findAll();
    if ($allSubscriber) {
      return $allSubscriber;
    } else {
      return false;
    }
  } // End of findAllSubscriber()

  public function unsubscribeOneById($id) 
  {
    $subscriber = $this->subscriberRepository->findOneById($id);
    if ($subscriber) {
      $this->em->remove($subscriber);
      $this->em->flush();
      return true;
    } else {
      return false;
    }
  } // End of unsubscribeOneById()

  public function countSubscriber() {
    $allSubscriber = $this->subscriberRepository->findAll();
    return count($allSubscriber);
  } // End of countSubscriber()
  
} //End of class