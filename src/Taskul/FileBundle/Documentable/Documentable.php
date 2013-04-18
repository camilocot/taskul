<?php

namespace Taskul\FileBundle\Documentable;

interface Documentable
{
	public function getClassName();
	public function setClassName($className);
}