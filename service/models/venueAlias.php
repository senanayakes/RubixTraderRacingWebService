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

class venueAlias extends  Model{

	public $venue_alias_id;

	public $feed_id;

	public $venue_id;

	public $name;



	public function getSource()
	{
		return 'venue_alias'; // name of db table here
	}


}