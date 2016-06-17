<?php

namespace Dview\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Test
 *
 * @ORM\Table(name="test")
 * @ORM\Entity(repositoryClass="Dview\ProjectBundle\Repository\TestRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Test {

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
     * @ORM\Column(name="idOnMongoDB", type="string", length=24, unique=true)
     */
    private $idOnMongoDB;

    /**
     * @ORM\ManyToOne(targetEntity="Dview\ProjectBundle\Entity\Suite", inversedBy="tests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $suite;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity="Dview\MailBundle\Entity\MailConfig", cascade={"persist", "remove"})
     */
    private $mailConfig;

    /**
     * @ORM\ManyToOne(targetEntity="Dview\MailBundle\Entity\MailConfig", cascade={"persist"})
     */
    private $mailConfigExt;

    /**
     * @var boolean
     * 
     * @ORM\Column(name="$useMailConfigExt", type="boolean")
     */
    private $useMailConfigExt = true;

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
     * @return Test
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
     * Set idOnMongoDB
     *
     * @param string $idOnMongoDB
     * @return Test
     */
    public function setIdOnMongoDB($idOnMongoDB) {
        $this->idOnMongoDB = $idOnMongoDB;

        return $this;
    }

    /**
     * Get idOnMongoDB
     *
     * @return string 
     */
    public function getIdOnMongoDB() {
        return $this->idOnMongoDB;
    }

    /**
     * Set suite
     *
     * @param \Dview\ProjectBundle\Entity\Suite $suite
     * @return Test
     */
    public function setSuite(\Dview\ProjectBundle\Entity\Suite $suite) {
        $this->suite = $suite;

        return $this;
    }

    /**
     * Get suite
     *
     * @return \Dview\ProjectBundle\Entity\Suite 
     */
    public function getSuite() {
        return $this->suite;
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
     * Set mailConfig
     *
     * @param \Dview\MailBundle\Entity\MailConfig $mailConfig
     *
     * @return Test
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
    public function getMailConfig() {
        return $this->mailConfig;
    }

    /**
     * Set mailConfigExt
     *
     * @param \Dview\MailBundle\Entity\MailConfig $mailConfigExt
     *
     * @return Test
     */
    public function setMailConfigExt(\Dview\MailBundle\Entity\MailConfig $mailConfigExt) {
        $this->mailConfigExt = $mailConfigExt;

        return $this;
    }

    /**
     * Get mailConfigExt
     *
     * @return \Dview\MailBundle\Entity\MailConfig
     */
    public function getMailConfigExt() {
        return $this->mailConfigExt;
    }

    /**
     * Set useMailConfigExt
     *
     * @param boolean $useMailConfigExt
     *
     * @return Test
     */
    public function setUseMailConfigExt($useMailConfigExt) {
        $this->useMailConfigExt = $useMailConfigExt;

        return $this;
    }

    /**
     * Get useMailConfigExt
     *
     * @return boolean
     */
    public function getUseMailConfigExt() {
        return $this->useMailConfigExt;
    }

}
