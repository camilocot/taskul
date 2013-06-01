<?php
namespace Taskul\MainBundle\Component;

class DateClass extends \DateTime {

	public static function getHumanDiff(\DateTime $time)
	{
		$start = $time;
        $time_span = $start->diff(new \DateTime('now'));
        if((int)$time_span->format('%a')>0)
            return $time_span->format('%R%a days');
        else
            return $time_span->h.' horas y '.$time_span->m.' min';
	}
}