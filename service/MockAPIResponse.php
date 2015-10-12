<?php

/**
 * Name goes here
 * Description goes here
 *
 * @author senanayakes
 * @package
 * @subpackage
 *
 * @copyright Copyright © Tabcorp Pty Ltd. All rights reserved. http://www.tabcorp.com.au/
 * @license This code is copyrighted and is the exclusive property of Tabcorp Pty Ltd. It may not be used, copied or redistributed without the written permission of Tabcorp.
 */

namespace MockAPI\Response;

use Phalcon\Http\Response;

class MockAPIResponse {

	private $response;

	public function __construct() {
		$this->response = new Response();
	}


	public function send($code, $message, array $data) {

		$this->response->setStatusCode($code, $message);
		$this->response->setJsonContent($data);
		$this->response->send();

	}

}