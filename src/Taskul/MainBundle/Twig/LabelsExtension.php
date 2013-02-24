<?php
namespace Taskul\MainBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
class LabelsExtension extends \Twig_Extension
{
    public function getFilters()
    {
        return array(
            'status' => new \Twig_Filter_Method($this, 'statusColor'),
            'labelsinactive' => new \Twig_Filter_Method($this, 'labelArray'),
        );
    }

    public function statusColor($text,$label)
    {
    	$class = 'label ';
    	switch ($label){
    		case 'inprogress':
    			$class .= 'label-warning';
    			break;
    		case 'todo':
    			$class .= 'label-important';
    			break;
    		case 'done':
    			$class .= 'label-success';
    			break;
    	}

        return '<span class="'.$class.'">'.$text.'</span>';
    }

	public function labelArray(ArrayCollection $array){
		$res = '';
		$array = $array->toArray();
		foreach($array as $a){
			$res .= '<span class="label">'.$a.'</span>&nbsp;';
		}

		return $res;

	}

    public function getName()
    {
        return 'label_extensions';
    }
}