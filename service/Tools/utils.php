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

namespace RubixTraderRacingWebService\Tools;


class Utils {


	public function index_set_strict(&$array, $index, $default = false) {

		if (is_array($array)) {
			return (isset($array[$index]) && $array[$index] !== null) ? $array[$index] : $default;
		}

		return $default;
	}


}