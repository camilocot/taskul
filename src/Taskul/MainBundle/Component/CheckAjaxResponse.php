<?php

namespace Taskul\MainBundle\Component;


class CheckAjaxResponse
{
	protected $response;
	protected $ajaxData;
	protected $isRedirect;

	public function __construct($response, $ajaxData, $isRedirect=TRUE)
	{
		$this->isRedirect = $isRedirect;
		$this->response = $response;
		$this->ajaxData = $ajaxData;

	}

	public function getResponse() {
	    return $this->response;
	}

	public function getAjaxData() {
	    return $this->ajaxData;
	}

	public function getIsRedirect()
	{
		return $this->isRedirect;
	}

}