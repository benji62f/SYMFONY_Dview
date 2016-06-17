<?php

namespace Dview\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MainConfig
 *
 * @ORM\Table(name="main_config")
 * @ORM\Entity(repositoryClass="Dview\ProjectBundle\Repository\MainConfigRepository")
 */
class MainConfig {

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
     * @ORM\Column(name="browserName", type="string", length=255)
     */
    private $browserName;

    /**
     * @var string
     *
     * @ORM\Column(name="browserVersion", type="string", length=255)
     */
    private $browserVersion;

    /**
     * @var int
     *
     * @ORM\Column(name="browserWidth", type="integer")
     */
    private $browserWidth;

    /**
     * @var int
     *
     * @ORM\Column(name="browserHeight", type="integer")
     */
    private $browserHeight;

    /**
     * @var float
     *
     * @ORM\Column(name="threshold", type="float")
     */
    private $threshold;

    /**
     * @var string
     *
     * @ORM\Column(name="schedule", type="string", length=255)
     */
    private $schedule;

    /**
     * Get id
     *
     * @return int
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set browserName
     *
     * @param string $browserName
     *
     * @return MainConfig
     */
    public function setBrowserName($browserName) {
        $this->browserName = $browserName;

        return $this;
    }

    /**
     * Get browserName
     *
     * @return string
     */
    public function getBrowserName() {
        return $this->browserName;
    }

    /**
     * Set browserVersion
     *
     * @param string $browserVersion
     *
     * @return MainConfig
     */
    public function setBrowserVersion($browserVersion) {
        $this->browserVersion = $browserVersion;

        return $this;
    }

    /**
     * Get browserVersion
     *
     * @return string
     */
    public function getBrowserVersion() {
        return $this->browserVersion;
    }

    /**
     * Set browserWidth
     *
     * @param integer $browserWidth
     *
     * @return MainConfig
     */
    public function setBrowserWidth($browserWidth) {
        $this->browserWidth = $browserWidth;

        return $this;
    }

    /**
     * Get browserWidth
     *
     * @return int
     */
    public function getBrowserWidth() {
        return $this->browserWidth;
    }

    /**
     * Set browserHeight
     *
     * @param integer $browserHeight
     *
     * @return MainConfig
     */
    public function setBrowserHeight($browserHeight) {
        $this->browserHeight = $browserHeight;

        return $this;
    }

    /**
     * Get browserHeight
     *
     * @return int
     */
    public function getBrowserHeight() {
        return $this->browserHeight;
    }

    /**
     * Set threshold
     *
     * @param float $threshold
     *
     * @return MainConfig
     */
    public function setThreshold($threshold) {
        $this->threshold = $threshold;

        return $this;
    }

    /**
     * Get threshold
     *
     * @return float
     */
    public function getThreshold() {
        return $this->threshold;
    }

    /**
     * Set schedule
     *
     * @param string $schedule
     *
     * @return MainConfig
     */
    public function setSchedule($schedule) {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return string
     */
    public function getSchedule() {
        return $this->schedule;
    }

}
