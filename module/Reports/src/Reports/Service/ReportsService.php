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
		//$this->database->setFetchMode(PDO::FETCH_OBJ);
		
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
		
		
		return $this->database->fetchAll($query);
	}
}
