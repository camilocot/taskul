<?php
namespace Taskul\TimelineBundle\Driver\ORM;

use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Persistence\ObjectManager;
use Spy\TimelineBundle\Driver\Doctrine\AbstractTimelineManager;
use Spy\Timeline\Driver\TimelineManagerInterface;
use Spy\Timeline\Model\ComponentInterface;
use Spy\Timeline\Model\TimelineInterface;
use Spy\Timeline\ResultBuilder\Pager\PagerInterface;
use Spy\TimelineBundle\Driver\ORM\TimelineManager as BaseTimelineManager;

class TimelineManager extends BaseTimelineManager
{

	/**
     * El original no filtra los resultados
     */
    public function countKeys(ComponentInterface $subject, array $options = array())
    {

        return count($this->getTimeline($subject,$options));
    }
}