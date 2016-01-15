<?php

namespace Reports\Service;

class ReportsCsvService
{
    /** @var ReportsService */
    private $reportsService;

    public function __construct(ReportsService $reportsService)
    {
        $this->reportsService = $reportsService;
    }

    /**
     * This method return a string containign the CSV data records of trips between 
     * the given dates $startDate and $endDate.
     *
     * @param \DateTime $startDate The start date.
     * @param \DateTime $endDate   The end date.
     *
     * @return string
     */
    public function getAllTripsCsv($startDate, $endDate)
    {
        // Get the trips, in array format
        $trips = $this->reportsService->getAllTrips($startDate, $endDate);

        // Try to convert the array in CSV using a temp file in memory
        try {
            // Generate CSV in Memory
            $file = fopen('php://temp/maxmemory:'.(12 * 1024 * 1024), 'r+'); // 128mb

            // Write CSV to memory
            fputcsv($file, array_keys(call_user_func_array('array_merge', $trips)));

            foreach ($trips as $row) {
                fputcsv($file, $row);
            }

            // Fetch CSV contents
            rewind($file);
            $output = stream_get_contents($file);
            fclose($file);
        } catch (\Exception $e) {
            throw new Exception('Error trying to parse CSV data.');

            return false;
        }

        return $output;
    }

    /**
     * This method return a string containign the CSV data records of trips between 
     * the given dates $startDate and $endDate of a particular city.
     *
     * @param \DateTime $startDate The start date.
     * @param \DateTime $endDate   The end date.
     * @param int       $city      The fleet_id of the city
     *
     * @return array<string,mixed>
     */
    public function getCityTripsCsv($startDate, $endDate, $city)
    {
        // Get the trips, in array format
        $trips = $this->reportsService->getCityTrips($startDate, $endDate, $city);

        // Try to convert the array in CSV using a temp file in memory
        try {
            // Generate CSV in Memory
            $file = fopen('php://temp/maxmemory:'.(12 * 1024 * 1024), 'r+'); // 128mb

            // Write CSV to memory
            fputcsv($file, array_keys(call_user_func_array('array_merge', $trips)));

            foreach ($trips as $row) {
                fputcsv($file, $row);
            }

            // Fetch CSV contents
            rewind($file);
            $output = stream_get_contents($file);
            fclose($file);
        } catch (\Exception $e) {
            throw new Exception('Error trying to parse CSV data.');

            return false;
        }

        return $output;
    }
}
