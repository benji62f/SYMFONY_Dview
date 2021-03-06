<?php

namespace Dview\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Project
 *
 * @ORM\Table(name="project")
 * @ORM\Entity(repositoryClass="Dview\ProjectBundle\Repository\ProjectRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Project {

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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="Dview\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $manager;

    /**
     * @ORM\ManyToOne(targetEntity="Dview\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToMany(targetEntity="Dview\ProjectBundle\Entity\Suite", mappedBy="project", cascade={"remove"})
     */
    private $suites;

    /**
     * @ORM\OneToOne(targetEntity="Dview\MailBundle\Entity\MailConfig", cascade={"persist", "remove"})
     */
    private $mailConfig;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Project
     */
    public function setName($name) {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Set manager
     *
     * @param \Dview\UserBundle\Entity\User $manager
     * @return Project
     */
    public function setManager(\Dview\UserBundle\Entity\User $manager) {
        $this->manager = $manager;

        return $this;
    }

    /**
     * Get manager
     *
     * @return \Dview\UserBundle\Entity\User 
     */
    public function getManager() {
        return $this->manager;
    }

    /**
     * Set client
     *
     * @param \Dview\UserBundle\Entity\User $client
     * @return Project
     */
    public function setClient(\Dview\UserBundle\Entity\User $client) {
        $this->client = $client;

        return $this;
    }

    /**
     * Get client
     *
     * @return \Dview\UserBundle\Entity\User 
     */
    public function getClient() {
        return $this->client;
    }

    /**
     * Set date
     *
     * @ORM\PrePersist
     * 
     * @param \DateTime $date
     * @return Project
     */
    public function setDate() {
        $this->date = new \DateTime;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Constructor
     */
    public function __construct() {
        $this->suites = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add suite
     *
     * @param \Dview\ProjectBundle\Entity\Suite $suite
     * @return Project
     */
    public function addSuite(\Dview\ProjectBundle\Entity\Suite $suite) {
        $this->suites[] = $suite;
        $suite->setProject($this);

        return $this;
    }

    /**
     * Remove suite
     *
     * @param \Dview\ProjectBundle\Entity\Suite $suite
     */
    public function removeSuite(\Dview\ProjectBundle\Entity\Suite $suite) {
        $this->suites->removeElement($suite);
    }

    /**
     * Get suites
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSuites() {
        return $this->suites;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Project
     */
    public function setDescription($description) {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription() {
        return $this->description;
    }


    /**
     * Set mailConfig
     *
     * @param \Dview\MailBundle\Entity\MailConfig $mailConfig
     *
     * @return Project
     */
    public function setMailConfig(\Dview\MailBundle\Entity\MailConfig $mailConfig) {
        $this->mailConfig = $mailConfig;

        return $this;
    }

    /**
     * Get mailConfig
     *
     * @return \Dview\MailBundle\Entity\MailConfig
     */
    public function getMailConfig()
    {
        return $this->mailConfig;
    }
}
