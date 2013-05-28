<?php

namespace Taskul\MainBundle\Component;


class CheckAjaxResponse
{
	protected $redirectUrl;
	protected $ajaxData;

	public function __construct($redirectUrl, $ajaxData)
	{
		$this->redirectUrl = $redirectUrl;
		$this->ajaxData = $ajaxData;
	}

	public function getRedirectUrl() {
	    return $this->redirectUrl;
	}

	public function getAjaxData() {
	    return $this->ajaxData;
	}

}