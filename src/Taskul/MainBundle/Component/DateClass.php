<?php
namespace Taskul\MainBundle\Component;

class DateClass extends \DateTime {

	public static function getHumanDiff(\DateTime $time)
	{
		$start = $time;
        $time_span = $start->diff(new \DateTime('now'));
        if((int)$time_span->format('%a')>0)
            return $time_span->format('%R%a días');
        else if($time_span->h == 0 && $time_span->m < 31)
        	return 'Ahora';
        else if ($time_span->h > 0 && $time_span->m < 10)
        	return $time_span->h.'h ';
        else
            return $time_span->h.'h y '.$time_span->m.'m';
	}
}