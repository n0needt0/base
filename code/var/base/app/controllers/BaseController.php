<?php

use Illuminate\Http\Response;

use Predis\ResponseErrorTest;

use Lasdorf\Utils as Utils;

use Lasdorf\FormattedJsonResponse\FormattedJsonResponse;

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

	protected function json_out($data=array())
	{

        $response = new FormattedJsonResponse($data);

        $response->setCallback(Input::get('callback'));

        if(Config::get('app.debug_json'))
        {
            //overwriteData with formatted MAY BE BUSTED later
            $response-> indent_json();
        }

        return $response;
	}
}