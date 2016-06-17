<?php

namespace Dview\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MailConfig
 *
 * @ORM\Table(name="mail_config")
 * @ORM\Entity(repositoryClass="Dview\MailBundle\Repository\MailConfigRepository")
 */
class MailConfig {

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var bool
     *
     * @ORM\Column(name="onOK", type="boolean")
     */
    private $onOK = false;

    /**
     * @var bool
     *
     * @ORM\Column(name="onKO", type="boolean")
     */
    private $onKO = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="withCapture", type="boolean")
     */
    private $withCapture = false;

    /**
     * @var string
     *
     * @ORM\Column(name="additionalContent", type="text", nullable=true)
     */
    private $additionalContent;

    /**
     * @var bool
     *
     * @ORM\Column(name="withStatus", type="boolean")
     */
    private $withStatus = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="withInfo", type="boolean")
     */
    private $withInfo = true;

    /**
     * @var bool
     *
     * @ORM\Column(name="withAdditionalContent", type="boolean")
     */
    private $withAdditionalContent = false;

    /**
     * @var string
     *
     * @ORM\Column(name="customObject", type="string", length=255, nullable=true)
     */
    private $customObject;

    /**
     * @ORM\ManyToMany(targetEntity="Dview\UserBundle\Entity\User")
     * @ORM\JoinTable(name="receivers",
     *      joinColumns={@ORM\JoinColumn(name="mail_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id", unique=true)}
     *      )
     */
    private $receivers;

    public function __construct() {
        $this->receivers = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set onOK
     *
     * @param boolean $onOK
     *
     * @return MailConfig
     */
    public function setOnOK($onOK) {
        $this->onOK = $onOK;

        return $this;
    }

    /**
     * Get onOK
     *
     * @return bool
     */
    public function getOnOK() {
        return $this->onOK;
    }

    /**
     * Set onKO
     *
     * @param boolean $onKO
     *
     * @return MailConfig
     */
    public function setOnKO($onKO) {
        $this->onKO = $onKO;

        return $this;
    }

    /**
     * Get onKO
     *
     * @return bool
     */
    public function getOnKO() {
        return $this->onKO;
    }

    /**
     * Set withCapture
     *
     * @param boolean $withCapture
     *
     * @return MailConfig
     */
    public function setWithCapture($withCapture) {
        $this->withCapture = $withCapture;

        return $this;
    }

    /**
     * Get withCapture
     *
     * @return bool
     */
    public function getWithCapture() {
        return $this->withCapture;
    }

    /**
     * Set additionalContent
     *
     * @param string $additionalContent
     *
     * @return MailConfig
     */
    public function setAdditionalContent($additionalContent) {
        $this->additionalContent = $additionalContent;

        return $this;
    }

    /**
     * Get additionalContent
     *
     * @return string
     */
    public function getAdditionalContent() {
        return $this->additionalContent;
    }

    /**
     * Set withStatus
     *
     * @param boolean $withStatus
     *
     * @return MailConfig
     */
    public function setWithStatus($withStatus) {
        $this->withStatus = $withStatus;

        return $this;
    }

    /**
     * Get withStatus
     *
     * @return bool
     */
    public function getWithStatus() {
        return $this->withStatus;
    }

    /**
     * Set withInfo
     *
     * @param boolean $withInfo
     *
     * @return MailConfig
     */
    public function setWithInfo($withInfo) {
        $this->withInfo = $withInfo;

        return $this;
    }

    /**
     * Get withInfo
     *
     * @return bool
     */
    public function getWithInfo() {
        return $this->withInfo;
    }

    /**
     * Set customObject
     *
     * @param string $customObject
     *
     * @return MailConfig
     */
    public function setCustomObject($customObject) {
        $this->customObject = $customObject;

        return $this;
    }

    /**
     * Get customObject
     *
     * @return string
     */
    public function getCustomObject() {
        return $this->customObject;
    }

    /**
     * Add receiver
     *
     * @param \Dview\UserBundle\Entity\User $receiver
     *
     * @return MailConfig
     */
    public function addReceiver(\Dview\UserBundle\Entity\User $receiver) {
        $this->receivers[] = $receiver;

        return $this;
    }

    /**
     * Remove receiver
     *
     * @param \Dview\UserBundle\Entity\User $receiver
     */
    public function removeReceiver(\Dview\UserBundle\Entity\User $receiver) {
        $this->receivers->removeElement($receiver);
    }

    /**
     * Get receivers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReceivers() {
        return $this->receivers;
    }

    /**
     * Set withAdditionalContent
     *
     * @param boolean $withAdditionalContent
     *
     * @return MailConfig
     */
    public function setWithAdditionalContent($withAdditionalContent) {
        $this->withAdditionalContent = $withAdditionalContent;

        return $this;
    }

    /**
     * Get withAdditionalContent
     *
     * @return boolean
     */
    public function getWithAdditionalContent() {
        return $this->withAdditionalContent;
    }

}
