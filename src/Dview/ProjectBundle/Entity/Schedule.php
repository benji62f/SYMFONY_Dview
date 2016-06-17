<?php

namespace Dview\ProjectBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Schedule
 *
 * @ORM\Table(name="schedule")
 * @ORM\Entity(repositoryClass="Dview\ProjectBundle\Repository\ScheduleRepository")
 */
class Schedule
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
     * @ORM\Column(name="cron_format", type="string", length=255, unique=true)
     */
    private $cronFormat;

    /**
     * @var string
     *
     * @ORM\Column(name="human_format", type="string", length=255)
     */
    private $humanFormat;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set cronFormat
     *
     * @param string $cronFormat
     * @return Schedule
     */
    public function setCronFormat($cronFormat)
    {
        $this->cronFormat = $cronFormat;

        return $this;
    }

    /**
     * Get cronFormat
     *
     * @return string 
     */
    public function getCronFormat()
    {
        return $this->cronFormat;
    }

    /**
     * Set humanFormat
     *
     * @param string $humanFormat
     * @return Schedule
     */
    public function setHumanFormat($humanFormat)
    {
        $this->humanFormat = $humanFormat;

        return $this;
    }

    /**
     * Get humanFormat
     *
     * @return string 
     */
    public function getHumanFormat()
    {
        return $this->humanFormat;
    }
}
