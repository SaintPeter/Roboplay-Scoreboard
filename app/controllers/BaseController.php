<?php

// Global defines used in displaying videos
define('VG_GENERAL', 1);
define('VG_CUSTOM', 2);
define('VG_COMPUTE', 3);

// Global Defines for video flag statuses
define('FLAG_NORMAL', 0);
define('FLAG_REVIEW', 1);
define('FLAG_DISQUALIFIED', 2);


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