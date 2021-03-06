<?php
namespace Taskul\MainBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Translation\TranslatorInterface;
use JMS\TranslationBundle\Annotation\Ignore;

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
            'labelColor' => new \Twig_Filter_Method($this, 'labelColor'),
        );
    }

    public function statusColor($text,$label)
    {
        return $this->labelColor($this->translator->trans(/** @Ignore */'task.status.'.$text,array(),'TaskBundle'),$label);
    }

    public function labelColor($text,$label)
    {
        return '<span class="'.$this->color($label).'">'.$text.'</span>';
    }

    public function color($label)
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

        return $class;
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