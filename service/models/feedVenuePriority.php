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

class feedVenuePriority extends Model {

	public function getSource()
	{
		return 'feed_venue_priority'; // name of db table here
	}




}