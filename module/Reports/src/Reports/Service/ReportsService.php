<?php

namespace Reports\Service;

use Doctrine\DBAL\Connection;
use MongoDB;
use DateTime;
use PDO;

use phpGpx;

class ReportsService
{
    // Doctrine Database
    private $database;

    // MongoDB Database (settings in the ReportsServiceFactory)
    private $mongodb;

    public function __construct(Connection $database, $mongodb)
    {
        $this->database = $database;
        $this->mongodb = $mongodb;
    }

    public function getCities()
    {
        $query = '
            SELECT row_to_json(fc)
            FROM (
                SELECT
                    array_to_json(array_agg(f))     As city
                FROM (
                    SELECT        id                AS fleet_id,
                                code                AS fleet_code,
                                name                AS fleet_name,
                                choropleth_params   AS params
                    FROM        fleets
                    ORDER BY    name
                ) as f
            ) AS fc
        ';

        // Fetch all rows (but in this case will always fetch just one row)
        $cities = $this->database->fetchAll($query);

        //var_dump($mongodb);

        // Return the undecoded JSON
        return $cities[0]['row_to_json'];
    }

    public function getAllTrips($startDate, $endDate)
    {
        $query = "
            SELECT        *,
                        to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS')             as time_beginning_parsed,
                        to_char(time_end, 'YYYY-MM-DD HH24:MI:SS')                     as time_end_formatted,
                        EXTRACT(HOUR    FROM time_beginning)                        as time_beginning_hour,
                        EXTRACT(MINUTE    FROM time_beginning)                        as time_beginning_minute,
                        EXTRACT(SECOND    FROM time_beginning)                        as time_beginning_second,
                        EXTRACT(DAY        FROM time_beginning)                        as time_beginning_day,
                        TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60)     as time_total_minute,
                        EXTRACT(ISODOW from time_beginning)                         as time_dow
    
            FROM        view_bi_trips
            WHERE        time_beginning >= '".$startDate."' AND time_beginning    <= '".$endDate."' AND area_id IS NOT NULL
            ORDER BY    time_beginning
        ";

        $trips = $this->database->fetchAll($query);

        return $trips;
    }

    public function getCityTrips($startDate, $endDate, $city)
    {
        $query = "
            SELECT        *,
                        to_char(time_beginning, 'YYYY-MM-DD HH24:MI:SS')             as time_beginning_parsed,
                        to_char(time_end, 'YYYY-MM-DD HH24:MI:SS')                     as time_end_formatted,
                        EXTRACT(HOUR    FROM time_beginning)                        as time_beginning_hour,
                        EXTRACT(MINUTE    FROM time_beginning)                        as time_beginning_minute,
                        EXTRACT(SECOND    FROM time_beginning)                        as time_beginning_second,
                        EXTRACT(DAY        FROM time_beginning)                        as time_beginning_day,
                        TRUNC(EXTRACT(EPOCH from  (time_end - time_beginning))/60)     as time_total_minute,
                        EXTRACT(ISODOW from time_beginning)                         as time_dow
    
            FROM        view_bi_trips
            
            WHERE        time_beginning     >= '".$startDate."' 
                AND        time_beginning    <= '".$endDate."'  
                AND        fleet_id         = ".$city.'
                AND        area_id         IS NOT NULL

            ORDER BY    time_beginning
        ';

        $trips = $this->database->fetchAll($query);

        return $trips;
    }

    public function getUrbanAreas($city)
    {
        $query = "
            SELECT row_to_json(fc)
            FROM (     SELECT 'FeatureCollection'                 As type,
                            array_to_json(array_agg(f))     As features
            
                    FROM (    SELECT 'Feature'                 As type,
                            ST_AsGeoJSON(ua.area)::json     As geometry,
                            row_to_json(lp)                 As properties
            
                            FROM urban_areas         As ua
            
                            INNER JOIN (
                                SELECT     to_char(id_area,'FM999MI'),
                                        name ,
                                        id_area            as id
                                FROM urban_areas
                            ) As lp
                            ON ua.id_area = lp.id
                            WHERE id_fleet = ".$city.'
                    ) As f
            )  As fc
        ';

        // Fetch all rows (but in this case will always fetch just one row)
        $urbanareas = $this->database->fetchAll($query);

        // Return the undecoded JSON
        return $urbanareas[0]['row_to_json'];
    }

    /**
     * @param $startDate     The start date to filter data
     * @param $endDate      The end date to filter data
     * @param $begend        Determinate if return the trips beginning data, or the trips end data
     *                        0 ==> Beginning ||  1 ==> End
     */
    public function getTripsGeoData($startDate, $endDate, $begend)
    {
        $begend = $begend == 1 ? 'beginning' : 'end';

        $query = "
            SELECT row_to_json(fc)
            FROM (
                SELECT     'FeatureCollection'         As type,
                    array_to_json(array_agg(f))     As features   
                FROM (
                    SELECT         'Feature'                                 As type ,
                                ST_AsGeoJSON(ua.geo_".$begend.')::json     As geometry
                                
                    FROM         trips As ua
        
                    LEFT JOIN     customers c ON c.id = ua.customer_id
        
                    WHERE         ua.payable                         = true     AND
                                c.gold_list                     = false AND
                                c.maintainer                     = false AND
                                ua.timestamp_end                 IS NOT NULL    AND
                                ua.timestamp_'.$begend."        >= '".$startDate."'      AND
                                ua.timestamp_".$begend."        <= '".$endDate."' 
                    ORDER BY     ua.id DESC
                ) As f
            )  As fc";

        // Fetch all rows (but in this case will always fetch just one row)
        $geodata = $this->database->fetchAll($query);

        // Return the undecoded JSON
        return $geodata[0]['row_to_json'];
    }

    public function getCarsGeoData()
    {
        $query = "
            SELECT row_to_json(fc)
            FROM (
                SELECT     'FeatureCollection'         As type,
                    array_to_json(array_agg(f))     As features
                FROM (
                    SELECT         'Feature'                             As type ,
                                ST_AsGeoJSON(ua.location)::json     As geometry,
                                   row_to_json(lp)                     As properties
                    
                    FROM         cars                                 As ua
        
                    INNER JOIN (
                            SELECT     plate
                            FROM    cars
                    ) As lp
        
                    ON ua.plate = lp.plate
        
                    WHERE         ua.active                     = true     AND
                                ua.status                     = 'operative' AND
                                ua.running                  = 'true'
                ) As f
            )  As fc";

        // Fetch all rows (but in this case will always fetch just one row)
        $geodata = $this->database->fetchAll($query);

        // Return the undecoded JSON
        return $geodata[0]['row_to_json'];
    }

    /**
     * @param $startDate     The start date to filter data
     * @param $endDate      The end date to filter data    (DateTime)
     * @param $tripsNumber  The number of trips to         (DateTime)
     * @param $maintainer   The flag to get the mainteiner trips (true | false)
     */
    public function getTrips($startDate, $endDate, $tripsNumber, $maintainer)
    {
        // Limit the trips number
        $limit = (intval($tripsNumber) > 0 && intval($tripsNumber) <= 300) ? intval($tripsNumber) : 100;

        $maint = ($maintainer === true) ? 't' : 'f';

        $query = "
            SELECT array_to_json(array_agg(fc))
            FROM (
                SELECT    
                    t.id                                 as _id,
                    t.car_plate                            as VIN,
                    t.timestamp_beginning::timestamp    as begin_trip,
                    t.timestamp_end::timestamp            as end_trip
                    
                FROM            trips        t    
                LEFT JOIN        customers     c        ON    t.customer_id = c.id
                            
                
                WHERE        timestamp_beginning        >= '".$startDate."' 
                    AND        timestamp_beginning        <= '".$endDate."'
                    AND        timestamp_end        IS NOT NULL
                    AND        c.gold_list = '$maint'
                    AND        c.maintainer = '$maint'
                
                ORDER BY    timestamp_beginning    DESC
                            
                LIMIT         $limit
                
            ) as fc
        ";

        // Fetch all rows (but in this case will always fetch just one row)
        $trips = $this->database->fetchAll($query);

        // Return the undecoded JSON
        return $trips[0]['array_to_json'];
    }

    /**
     * @param $startDate     The start date to filter data
     * @param $endDate      The end date to filter data
     * @param $tripsNumber  The number of trips to 
     */
    public function getTripsFromLogs($startDate, $endDate, $tripsNumber)
    {
        // Converting Dates
        $end = new MongoDB\BSON\UTCDateTime(strtotime($endDate) * 1000);
        $start = new MongoDB\BSON\UTCDateTime(strtotime($startDate) * 1000);

        // Limit the trips number
        $limit = (intval($tripsNumber) > 0 && intval($tripsNumber) <= 300) ? intval($tripsNumber) : 100;

        $pipeline = [
            // STAGE 1
              ['$match' => [
                'log_time' => ['$gt' => $start, '$lte' => $end],
                'id_trip' => array('$ne' => 0),
                'begin_trip' => array('$ne' => 'null'),
                'end_trip' => array('$ne' => 'null'),
                'lon' => array('$gt' => 0),
                'lat' => array('$gt' => 0),
            ]],

            // STAGE 2
              ['$group' => [
                'VIN' => ['$last' => '$VIN'],
                '_id' => '$idTrip',
                'begin_trip' => ['$first' => '$log_time'],
                'end_trip' => ['$last' => '$log_time'],
                'points' => ['$sum' => 1],
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

            ['$limit' => $limit],
        ];

        try {
            $logs = new MongoDB\Collection($this->mongodb, 'sharengo.logs');
            $cursor = $logs->aggregate($pipeline);
        } catch (MongoDB\Driver\Exception $e) {
            return $e->getMessage();
        }

        $json = [];

        foreach ($cursor as $object) {
            array_push($json, [
                '_id' => $object->_id,
                'VIN' => $object->VIN,
                'begin_trip' => $object->begin_trip->toDateTime(), //->format('Y-m-d H:i:s'),
                'end_trip' => $object->end_trip->toDateTime(), //->format('Y-m-d H:i:s'),
                'points' => $object->points,
            ]);
        }

        return json_encode($json);//json_encode($cursor->toArray());
    }

    /**
     * @param $idTrip     The id of the trip
     */
    public function getTripPointsFromLogs($idTrip)
    {
        // Set the memory limit
        ini_set('memory_limit', '256M');

        $idTrip = is_array($idTrip) ? $idTrip : array($idTrip);

        $gpx = new phpGpx();

        foreach ($idTrip as $trip) {
            //error_log ("Trip\t\t$i di $total -> $trip", 0);

            $filter = [
                'id_trip' => (int) $trip,
                'begin_trip' => array('$ne' => 'null'),
                'end_trip' => array('$ne' => 'null'),
                'lon' => array('$gt' => 0),
                'lat' => array('$gt' => 0),
            ];

            $options = [
                '$sort' => ['_id' => -1],
                '$limit' => 10000,
            ];

            try {
                $logs = new MongoDB\Collection($this->mongodb, 'sharengo.logs');
                $cursor = $logs->find($filter, $options);
            } catch (MongoDB\Driver\Exception $e) {
                return $e->getMessage();
                error_log("\tErrore MongoQuery -> $e->getMessage()", 0);
            }

            $gpx->StartTrack($trip);

            $result = $cursor->toArray();

            // Controllo che latidutine e longitudine siano compresi
            // all'interno dell'intervallo di estremi dell'Italia
            $north = 47.08333;
            $west = 6.61666;
            $east = 18.51666;
            $south = 35.48333;

            foreach ($result as $object) {

                // Check if the object have all the needed properties
                if (isset($object->log_time) && isset($object->lat) && isset($object->lon)) {
                    $time = $object->log_time->toDateTime()->format("Y-m-d\Th:i:s+0000");
                    $lat = $object->lat;
                    $lon = $object->lon;

                    $mapurl = "<a href='http://maps.google.com/maps?q=$lat,$lon&z=16'>$lat , $lon</a>";

                    if ($lat != 0 && $lon != 0) {
                        //echo "OK  i= $i         --->       $lat - $lon";
                        if ($lat <= $north && $lat >= $south &&    $lon <= $east && $lon >= $west) {
                            $gpx->addTrackPoint($time, $lat, $lon);
                        }
                    }
                }
            }
            $gpx->EndTrack();
        }

        return $gpx->GetContentToString();
    }
}
