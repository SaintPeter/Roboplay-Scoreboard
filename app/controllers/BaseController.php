<?php

class BaseController extends Controller {

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

	public function __construct()
	{
		Breadcrumbs::setDivider('');
		Breadcrumbs::setCssClasses('breadcrumb');
		Breadcrumbs::addCrumb('Home', '/scoreboard');
	}

}