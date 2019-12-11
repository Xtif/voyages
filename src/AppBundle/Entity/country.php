<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\countryRepository")
 */
class country
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="photo", type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, unique=true)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="folder", type="string", length=255, unique=true)
     */
    private $folder;

    /**
     * @var string
     *
     * @ORM\Column(name="date", type="string", unique=false)
     */
    private $date;    

    /**
     * @var string
     *
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\episode", mappedBy="country", cascade={"all"})
     *
     */
    private $episodes;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->episodes = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set photo
     *
     * @param string $photo
     *
     * @return country
     */
    public function setPhoto($photo)
    {
        $this->photo = $photo;

        return $this;
    }

    /**
     * Get photo
     *
     * @return string
     */
    public function getPhoto()
    {
        return $this->photo;
    }

    /**
     * Set title
     *
     * @param string $title
     *
     * @return country
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set episodes
     *
     * @param string $episodes
     *
     * @return country
     */
    public function setEpisodes($episodes)
    {
        $this->episodes = $episodes;

        return $this;
    }

    /**
     * Get episodes
     *
     * @return string
     */
    public function getEpisodes()
    {
        return $this->episodes;
    }

    /**
     * Set date
     *
     * @param string $date
     *
     * @return country
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set folder
     *
     * @param string $folder
     *
     * @return country
     */
    public function setFolder($folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     *
     * @return string
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Add episode
     *
     * @param \AppBundle\Entity\episode $episode
     *
     * @return country
     */
    public function addEpisode(\AppBundle\Entity\episode $episode)
    {
        $this->episodes[] = $episode;

        return $this;
    }

    /**
     * Remove episode
     *
     * @param \AppBundle\Entity\episode $episode
     */
    public function removeEpisode(\AppBundle\Entity\episode $episode)
    {
        $this->episodes->removeElement($episode);
    }
}
