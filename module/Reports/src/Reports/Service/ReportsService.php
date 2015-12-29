<?php

namespace Reports\Service;

use Doctrine\DBAL\Connection;
use PDO;
use MongoDB;

class ReportsService
{
	// Doctrine Database
	private $database;
	
	// MongoDB Database (settings in the ReportsServiceFactory)
	private $mongodb;
	
	public function __construct(Connection $database,$mongodb) 
	{
		$this->database = $database;
		$this->mongodb	= $mongodb;
	}
	
	public function getCities()
	{
		$query = "
			SELECT row_to_json(fc)
			FROM (
				SELECT
					array_to_json(array_agg(f)) 	As city
				FROM (
					SELECT		id					AS fleet_id,
								code				AS fleet_code,
								name				AS fleet_name,
								choropleth_params	AS params
					FROM		fleets
					ORDER BY	name
				) as f
			) AS fc
		";
		
		// Fetch all rows (but in this case will always fetch just one row)
		$cities = $this->database->fetchAll($query);
		
		//var_dump($mongodb);
		
		// Return the undecoded JSON
		return $cities[0]['row_to_json'];
	}
	
	public function getAllTrips($start_date,$end_date)
	{
		$query = "
			SELECT		*,
						to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS') 			as time_beginning_parsed,
						to_char(time_end, 'YYYY-MM-DD HH24:MI:SS') 					as time_end_formatted,
						EXTRACT(HOUR	FROM time_beginning)						as time_beginning_hour,
						EXTRACT(MINUTE	FROM time_beginning)						as time_beginning_minute,
						EXTRACT(SECOND	FROM time_beginning)						as time_beginning_second,
						EXTRACT(DAY		FROM time_beginning)						as time_beginning_day,
						TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60) 	as time_total_minute,
						EXTRACT(ISODOW from time_beginning) 						as time_dow
	
			FROM		view_bi_trips
			WHERE		time_beginning >= '".$start_date ."' AND time_beginning	<= '". $end_date ."' AND area_id IS NOT NULL
			ORDER BY	time_beginning
		";
		
		$trips = $this->database->fetchAll($query);
		
		return $trips;
	}
	
	public function getCityTrips($start_date,$end_date,$city)
	{
		$query = "
			SELECT		*,
						to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS') 			as time_beginning_parsed,
						to_char(time_end, 'YYYY-MM-DD HH24:MI:SS') 					as time_end_formatted,
						EXTRACT(HOUR	FROM time_beginning)						as time_beginning_hour,
						EXTRACT(MINUTE	FROM time_beginning)						as time_beginning_minute,
						EXTRACT(SECOND	FROM time_beginning)						as time_beginning_second,
						EXTRACT(DAY		FROM time_beginning)						as time_beginning_day,
						TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60) 	as time_total_minute,
						EXTRACT(ISODOW from time_beginning) 						as time_dow
	
			FROM		view_bi_trips
			
			WHERE		time_beginning 	>= '".$start_date ."' 
				AND		time_beginning	<= '". $end_date ."'  
				AND		fleet_id 		= ".$city."
				AND		area_id 		IS NOT NULL

			ORDER BY	time_beginning
		";
		
		$trips = $this->database->fetchAll($query);
		
		return $trips;
	}
	
	public function getUrbanAreas($city)
	{
		$query = "
			SELECT row_to_json(fc)
			FROM ( 	SELECT 'FeatureCollection'	 			As type,
							array_to_json(array_agg(f)) 	As features
			
					FROM (	SELECT 'Feature' 				As type,
							ST_AsGeoJSON(ua.area)::json 	As geometry,
							row_to_json(lp) 				As properties
			
							FROM urban_areas 		As ua
			
							INNER JOIN (
								SELECT 	to_char(id_area,'FM999MI'),
										name ,
										id_area			as id
								FROM urban_areas
							) As lp
							ON ua.id_area = lp.id
							WHERE id_fleet = ".$city."
					) As f
			)  As fc
		";

		// Fetch all rows (but in this case will always fetch just one row)
		$urbanareas = $this->database->fetchAll($query);
		
		// Return the undecoded JSON
		return $urbanareas[0]['row_to_json'];
	}
	
	/**
	 * @param $start_date 	The start date to filter data
	 * @param $end_date 	The end date to filter data
	 * @param $begend		Determinate if return the trips beginning data, or the trips end data
	 *						0 ==> Beginning ||  1 ==> End
	 */
	public function getTripsGeoData($start_date,$end_date,$begend)
	{
		$begend = $begend == 1 ? 'beginning' : 'end';
		
		$query = "
			SELECT row_to_json(fc)
			FROM (
				SELECT 	'FeatureCollection' 		As type,
					array_to_json(array_agg(f)) 	As features   
				FROM (
					SELECT 		'Feature' 								As type ,
								ST_AsGeoJSON(ua.geo_".$begend.")::json 	As geometry
								
					FROM 		trips As ua
		
					LEFT JOIN 	customers c ON c.id = ua.customer_id
		
					WHERE 		ua.payable 						= true 	AND
								c.gold_list 					= false AND
								c.maintainer 					= false AND
								ua.timestamp_end 				IS NOT NULL	AND
								ua.timestamp_".$begend."		>= '".$start_date."'  	AND
								ua.timestamp_".$begend."		<= '".$end_date."' 
					ORDER BY 	ua.id DESC
				) As f
			)  As fc";
		
		// Fetch all rows (but in this case will always fetch just one row)
		$geodata = $this->database->fetchAll($query);
		
		// Return the undecoded JSON
		return $geodata[0]['row_to_json'];
	}
	
	/**
	 * @param $start_date 	The start date to filter data
	 * @param $end_date 	The end date to filter data
	 *
	 */
	public function getTripsFromLogs($start_date,$end_date)
	{
		
		// Converting Dates
		$end	= new MongoDB\BSON\UTCDateTime(strtotime($end_date)*1000);
		$start	= new MongoDB\BSON\UTCDateTime(strtotime($start_date)*1000);
		
		$filter = [
			
			'id_trip' 	=> ['$ne' => 0],
            'begin_trip'=> ['$ne' => 'null'],
            'end_trip' 	=> ['$ne' => 'null'],
			'lon'		=> ['$gt' => 0],
			'lat'		=> ['$gt' => 0],
		];

		$pipeline = [
			// STAGE 1
	      	['$match' => [
	        	'log_time'	=> ['$gte' => $start, '$lte' => $end],
	        	'id_trip' 	=> array('$ne' => 0),
	            'begin_trip'=> array('$ne' => 'null'),
	            'end_trip' 	=> array('$ne' => 'null'),
				'lon'		=> array('$gt' => 0),
				'lat'		=> array('$gt' => 0),
			]],
	
	        // STAGE 2
	      	['$group' => [
				'VIN'			=> array('$last' => '$VIN'),
				'_id' 			=> '$id_trip' ,
				'begin_trip' 	=> array('$first' => '$log_time'),
				'end_trip' 		=> array('$last' => '$log_time'),
				'points' 		=> array('$sum' => 1),
		   	]],
	
			// STAGE 3
			['$sort' => ['_id' => -1]],
	
			// STAGE 4
		   	['$project' => [
				'_id' => 1,
	            'VIN' => 1,
				'begin_trip' => 1,
	            'end_trip' => 1,
				'points' => 1,
	        ]],
	        
	        ['$limit' => 1000000]
        ];
		
		try {
			$logs	= new MongoDB\Collection($this->mongodb,'sharengo.logs');
			$cursor = $logs->aggregate($pipeline);

		} catch(MongoDB\Driver\Exception $e) {
		    return $e->getMessage();
		}
			
		return json_encode($cursor->toArray());
	}
}
