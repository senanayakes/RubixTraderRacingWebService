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


//get all venue information
$app->get('/api/venues', function() use ($app, $response) {

	$sql = "SELECT v.*, s.*, f.*, va.*
			FROM venue AS v
			INNER JOIN sport AS s ON s.sport_id = v.sport_id
			LEFT JOIN venueAlias AS va ON va.venue_id = v.venue_id
			LEFT JOIN feed AS f ON f.feed_id = va.feed_id
			";

	$results = $app->modelsManager->executeQuery($sql);
	$code = 200;
	$responseText = 'ok';
	$data = array();

	foreach ($results as $row) {
		$data[]= array(
			'venue_id' => $row->v->venue_id,
			'venue_name'=> $row->v->name,
			'sport_id' => $row->s->sport_id,
			'sport_name'=> $row->s->name,
			'venue_alias_id'=>$row->va->venue_alias_id,
			'feed_id' => $row->f->feed_id,
			'feed_name'=> $row->f->name

		);

	}

	$response->send($code, $responseText, $data);

});





//get meetings for a sport
$app->get('/api/categories/{category_name:[a-z]*}', function($category_name) use ($app, $response) {

	$sports = array('horse'=> 1,
					'greyhound'=> 2,
					'harness'=> 3
	);

	$code = 200;
	$responseText = 'ok';
	$data = array();

	if (array_key_exists($category_name, $sports)) {

		$sport_id = $sports[$category_name];

		$sql = "SELECT m.* ,  v.* , s.*, va.*, f.* , *
				FROM meeting AS m
				LEFT JOIN event AS e ON e.event_id = m.event_id
				INNER JOIN venue AS v ON v.venue_id = m.venue_id
				INNER JOIN sport AS s ON s.sport_id = v.sport_id
				LEFT JOIN venueAlias AS va on va.venue_id = v.venue_id
				LEFT JOIN feed AS f on f.feed_id = va.feed_id
				WHERE v.sport_id = '$sport_id'
				ORDER BY v.venue_id";


		$results = $app->modelsManager->executeQuery($sql);

		foreach ($results as $row) {

			$data[] = array(
				'meeting_id' => $row->m->meeting_id,
				'venue_id' => $row->m->venue_id,
				'venue_name' => $row->v->name,
				'venue_alias_id'=> $row->va->venue_alias_id,
				'meeting_name' => $row->m->name,
				'meeting_date' => $row->m->meeting_date,
				'timezone_at_venue' => $row->m->timezone_at_venue,
				'sport_id' =>$row->s->sport_id,
				'sport_name'=> $row->s->name,
				'feed_id' => $row->f->feed_id,
				'feed_name'=> $row->f->name
			);

		}

	}

	$response->send($code, $responseText, $data);

});


//get all events for a meeting
$app->get('/api/events/{meeting_id:[0-9]*}', function($meeting_id) use ($app, $response) {

	$meeting_id = (int) $meeting_id;
	$code = 200;
	$responseText = 'ok';
	$data = array();

	if (!empty($meeting_id)) {

		$sql = "SELECT e . * , v . * , va . * , va . * , f.*, m.*, s.*
				FROM event AS e
				INNER JOIN meeting AS m on m.meeting_id = e.meeting_id
				INNER JOIN venue AS v ON v.venue_id = e.venue_id
				INNER JOIN venueAlias AS va ON va.venue_id = v.venue_id
				INNER JOIN sport AS s on s.sport_id = v.sport_id
				INNER JOIN feed AS f ON f.feed_id = va.feed_id
				WHERE e.meeting_id = '$meeting_id'";


		$results = $app->modelsManager->executeQuery($sql);
		foreach ($results as $row) {
			$data[] = array(
				'meeting_id' => $row->m->meeting_id,
				'meeting_name' => $row->m->name,
				'meeting_date' => $row->m->meeting_date,
				'venue_id' => $row->m->venue_id,
				'venue_name' => $row->v->name,
				'event_id' => $row->e->event_id,
				'race_num' => $row->e->race_num,
				'event_name'=> $row->e->name,
				'race_status'=> $row->e->race_status,
				'event_status'=> $row->e->event_status,
				'start_time'=> $row->e->start_time,
				'surface' => $row->e->surface,
				'weather'=> $row->e->weather,
				'distance'=> $row->e->distance,
				'prize_money'=> $row->e->prize_money,
				'expected_dividends'=> $row->e->expected_dividends,
				'timezone_at_venue' => $row->m->timezone_at_venue,
				'sport_id' =>$row->s->sport_id,
				'sport_name'=> $row->s->name,
				'feed_id' => $row->f->feed_id,
				'feed_name'=> $row->f->name

			);

		}

	}

	$response->send($code, $responseText, $data);

});


//get all events for a meeting
$app->get('/api/event/{event_id:[0-9]*}', function($event_id) use ($app, $response) {

	$code = 200;
	$responseText = 'ok';
	$data = array();
	$event_id = (int) $event_id;

	if (!empty($event_id)) {

		$sql = "SELECT e . * , m . * , mt . * , fo.*, c.*, s.*
				FROM event AS e
				INNER JOIN meeting AS mt ON mt.meeting_id = e.meeting_id
				LEFT JOIN market AS m ON m.event_id = e.event_id
				LEFT JOIN contestant AS c ON c.event_id = e.event_id
				LEFT JOIN fixedOdds AS fo ON fo.event_id = e.event_id AND fo.market_id = m.market_id AND fo.contestant_id = c.contestant_id
				LEFT JOIN source AS s ON s.source_id = fo.source_id
				WHERE e.event_id = $event_id";


		$results = $app->modelsManager->executeQuery($sql);

		$markets = array();
		$contestants = array();
		$data = array();

		foreach ($results as $row) {

			$event_id = $row->e->event_id;
			if (!isset($data['event_id'])) {

				$data = array(
					'event_id' => $row->e->event_id,
					'venue_id' => $row->e->venue_id,
					'meeting_id' => $row->e->meeting_id,
					'event_name'=> $row->e->name,
					'race_status'=> $row->e->race_status,
					'event_status'=> $row->e->event_status,
					'start_time'=> $row->e->start_time,
					'surface'=> $row->e->surface,
					'weather'=> $row->e->weather,
					'distance'=> $row->e->distance,
					'prize_money'=> $row->e->prize_money,
					'expected_dividends' => $row->e->expected_dividends
				);

			}

			$market_id = $row->m-> market_id;
			if (!empty($market_id)) {

				$markets[] = array(
					'market_id' => $market_id,
					'market_name'=> $row->m->name

				);

			}

			$contestant_id = $row->c->contestant_id;
			if (!empty($contestant_id)) {
				$contestants[] = array(
					'contestant_id' => $contestant_id,
					'name' => $row->c->name,
					'runner_num'=> $row->c->runner_num,
					'barrier' => $row->c->barrier,
					'status'=> $row->c->status,
					'finish_position' => $row->c->finish_position,
					'rider'=> $row->c->rider,
					'trainer'=> $row->c->trainer,
					'handicap'=> $row->c->handicap,
					'handicap_str'=> $row->c->handicap_str,
					'last_five_starts'=> $row->c->last_five_starts,
					'emergency'=> $row->c->emergency,
					'win_price' => $row->fo->win_price,
					'place_price'=> $row->fo->place_price,
					'source_name'=> $row->s->name,
					'source_id'=> $row->fo->source_id
				);



			}

		}

	}


	$sql = "SELECT * FROM poolType";
	$poolTypeResult = $app->modelsManager->executeQuery($sql);

	$poolTypes = array();

	foreach ($poolTypeResult as $poolType) {
		$poolTypes[] = array(
			'pool_type_id' =>  $poolType-> pool_type_id,
			'pool_type_name'=> $poolType->name

		);

	}

	$sql = "SELECT pe . * , p . * , pt . *, s.*
			FROM poolEvents AS pe
			INNER JOIN pool AS p ON p.pool_id = pe.pool_id
			INNER JOIN poolType AS pt ON pt.pool_type_id = p.pool_type_id
			INNER JOIN source AS s ON s.source_id = p.source_id
			WHERE pe.event_id = $event_id";


	$eventPoolsResult = $app->modelsManager->executeQuery($sql);
	$eventPools = array();
	foreach ($eventPoolsResult as $eventPool) {
		$eventPools[] = array(
			'pool_id'=> $eventPool->pe->pool_id,
			'pool_type_id'=> $eventPool->p->pool_type_id,
			'pool_type_name'=> $eventPool->pt->name,
			'substitute' => $eventPool->pe->substitute,
			'source_id'=> $eventPool->p->source_id,
			'pool_status'=> $eventPool->p->pool_status,
			'total'=> $eventPool->p->total,
			'source_id' => $eventPool->s->source_id,
			'source_name'=> $eventPool->s->name


		);

	}


	//get pool details

	$data['markets'] = array_unique($markets, SORT_NUMERIC);
	$data['contestants'] = $contestants;
	$data['all_pool_types'] = $poolTypes;
	$data['event_pool_types'] = $eventPools;
	$response->send($code, $responseText, $data);
});


/**
 * get all the price sources
 */
$app->get('/api/price/sources', function($event_id) use ($app, $response) {

	$code = 200;
	$responseText = 'ok';
	$data = array();
	$sql = "SELECT * FROM source";
	$results = $app->modelsManager->executeQuery($sql);
	foreach ($results as $row) {
		$data[] = array(
			'source_id' => $row->source_id,
			'source_name'=> $row->name,
			'pool_status'=> $row->pool_status
		);
	}

	$response->send($code, $responseText, $data);

});




$app->notFound(function () use ($app, $response) {
	$response->send(404, 'Not Found', array('error'=> 'Not Supported Operation'));

});



$app->handle();
