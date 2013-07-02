<?php
namespace Taskul\MainBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;

class LabelsExtension extends \Twig_Extension
{
    private $translator;

    function __construct(TranslatorInterface $translator) {
        $this->translator = $translator;
    }
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

        return '<span class="'.$class.'">'.$this->translator->trans('task.status.'.$text,array(),'TaskBundle').'</span>';
    }

	public function labelArray(ArrayCollection $array){
		$res = '';
		$array = $array->toArray();
		foreach($array as $a){
			$res .= '<a href="#" class="label tags">'.$a.'</a>&nbsp;';
		}

		return $res;

	}

    public function getName()
    {
        return 'label_extensions';
    }
}