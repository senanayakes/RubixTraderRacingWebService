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


use Phalcon\Mvc\Model;

class fixedOdds extends  Model{

	public function getSource()
	{
		return 'fixed_odds'; // name of db table here
	}

}