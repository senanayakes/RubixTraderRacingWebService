<?php
/**
 * Index.php
 *
 * This is throwway mock api for rubix trader racing . NOT TO BE USED IN PRODUCTION.
 *
 * @author senanayakes
 * @package
 * @subpackage
 *
 * @copyright Copyright © Tabcorp Pty Ltd. All rights reserved. http://www.tabcorp.com.au/
 * @license This code is copyrighted and is the exclusive property of Tabcorp Pty Ltd. It may not be used, copied or redistributed without the written permission of Tabcorp.
 */


require_once 'MockAPIResponse.php';


use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\DI\FactoryDefault;
use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;
use Phalcon\Http\Response;

$loader = new Loader();
$response = new \MockAPI\Response\MockAPIResponse();


$loader->registerDirs(
	array(
		__DIR__ . '/models/'
	)
)->register();

$di = new FactoryDefault();///
// Set up the database service
$di->set('db', function () {
	return new PdoMysql(
		array(
			"host"     => "localhost",
			"username" => "root",
			"password" => "password",
			"dbname"   => "rubixtrader_racing"
		)
	);
});


$app = new Micro($di);


//COR'S STUFF

$app->before(function() use ($app) {
	$origin = $app->request->getHeader("ORIGIN") ? $app->request->getHeader("ORIGIN") : '*';

	$app->response->setHeader("Access-Control-Allow-Origin", $origin)
				  ->setHeader("Access-Control-Allow-Methods", 'GET,PUT,POST,DELETE,OPTIONS')
				  ->setHeader("Access-Control-Allow-Headers", 'Origin, X-Requested-With, Content-Range, Content-Disposition, Content-Type, Authorization')
				  ->setHeader("Access-Control-Allow-Credentials", true)
				  ->setHeader("'Content-type",  'application/json');

});

$app->options('/{catch:(.*)}', function() use ($app) {
	$app->response->setStatusCode(200, "OK")->send();
});





$app->get('/api', function() use ($app, $response) {

	$data = array('rubix trader racing mock api');
	$response->send(200, 'ok', $data);


});

$app->get('/api/categories/{category_name}', function($category_name) use ($app, $response) {


	switch ($category_name) {

		case 'horse':
			$sport_id = 1;
			break;

		case 'greyhound':
			$sport_id = 2;
			break;

		case 'harness':
			$sport_id = 3;
			break;

		default:
			$sport_id = 0;
			break;

	}


	if (!empty($sport_id)) {

		$sql = "SELECT m.* , v.name AS venue_name FROM meeting AS m
 			JOIN venue AS v on v.venue_id = m.venue_id
 			where v.sport_id = '$sport_id'
 			ORDER BY  v.venue_id
 		";

		$results = $app->modelsManager->executeQuery($sql);
		$data = array();
		$code = 200;
		$responseText = 'ok';

		foreach ($results as $row) {

			$data[] = array(
				'meeting_id' => $row->m->meeting_id,
				'venue_id' => $row->m->venue_id,
				'venue_name' => $row->venue_name,
				'name' => $row->m->name,
				'meeting_date' => $row->m->meeting_date,
				'timezone_at_venue' => $row->m->timezone_at_venue

			);

		}
	} else {
		$code = 404;
		$responseText = 'Not found';
		$data = array('error'=> 'invalid category');
	}

	$response->send($code, $responseText, $data);

});



$app->get('/api/events/{meeting_id}', function($meeting_id) use ($app, $response) {

	$meeting_id = (int) $meeting_id;

	if (!empty($meeting_id)) {

		$sql = "SELECT * FROM event WHERE meeting_id = '$meeting_id' order by race_num";

		$results = $app->modelsManager->executeQuery($sql);
		$data = array();
		$code = 200;
		$responseText = 'ok';

		foreach ($results as $row) {

			$data[] = array(
				'event_id' => $row->event_id,
				'venue_id' => $row->venue_id,
				'meeting_id' => $row->meeting_id,
				'race_num' => $row->race_num,
				'name' => $row->name,
				'race_status' => $row->race_status,
				'event_status'=> $row->event_status,
				'start_time'=> $row->start_time,
				'surface'=> $row->surface,
				'weather'=> $row->weather,
				'distance'=> $row->distance,
				'prize_money'=> $row->prize_money,
				'expected_dividends' => $row->expected_dividends
			);
		}


	} else {
		$code = 404;
		$responseText = 'Not found';
		$data = array('error'=> 'invalid Meeting Id');

	}

	$response->send($code, $responseText, $data);

});



$app->get('/api/event/{event_id}', function($event_id) use ($app, $response) {

	$event_id = (int) $event_id;

	if (!empty($event_id)) {

		$sql = "SELECT * FROM contestant WHERE event_id = '$event_id' order by 	contestant_id";

		$results = $app->modelsManager->executeQuery($sql);
		$data = array();
		$code = 200;
		$responseText = 'ok';

		foreach ($results as $row) {

			$data[] = array(
				'contestant_id' => $row->contestant_id,
				'event_id' => $row->event_id,
				'runner_num'=> $row->runner_num,
				'name'=> $row->name,
				'barrier' => $row->barrier,
				'status'=> $row->status,
				'finish_position' => $row->finish_position,
				'rider'=> $row->rider,
				'trainer' => $row->trainer,
				'handicap'=> $row->handicap,
				'handicap_str'=> $row->handicap_str,
				'last_five_starts'=> $row,
				'emergency' => $row->emergency

			);
		}


	} else {
		$code = 404;
		$responseText = 'Not found';
		$data = array('error'=> 'invalid Event Id');

	}

	$response->send($code, $responseText, $data);

});






$app->handle();
