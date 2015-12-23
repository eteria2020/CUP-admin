<?php

namespace Reports\Service;

use Doctrine\DBAL\Connection;
use PDO;

class ReportsService
{
	private $database;
	
	public function __construct(Connection $database) 
	{
		$this->database = $database;
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
}
