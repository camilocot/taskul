<?php

namespace Taskul\TimelineBundle\Entity;

use Spy\TimelineBundle\Entity\Timeline as BaseTimeline;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="spy_timeline")
 */
class Timeline extends BaseTimeline
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="Taskul\TimelineBundle\Entity\Action", inversedBy="timelines")
     * @ORM\JoinColumn(name="action_id", referencedColumnName="id")
     */
    protected $action;

    /**
     * @ORM\ManyToOne(targetEntity="Taskul\TimelineBundle\Entity\Component")
     * @ORM\JoinColumn(name="subject_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $subject;
}