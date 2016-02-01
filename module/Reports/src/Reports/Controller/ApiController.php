<?php

namespace Reports\Controller;

// Internal Modules
use Reports\Service\ReportsService;
use Reports\Service\ReportsCsvService;
use Reports\Exception\InvalidParameter;
use Reports\Exception\MissingParameter;
use Reports\Exception\CsvParsingException;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Http\Response;
use DateTime;
use Exception;
use DateInterval;

class ApiController extends AbstractActionController
{
    /**
     * @var ReportsService
     */
    private $reportsService;

    /**
     * @var ReportsService
     */
    private $reportsCsvService;

    public function __construct(ReportsService $reportsService, ReportsCsvService $reportsCsvService)
    {
        $this->reportsService = $reportsService;
        $this->reportsCsvService = $reportsCsvService;
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getCitiesAction()
    {
        // Get the cities, in JSON format
        $cities = $this->reportsService->getCities();

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($cities);

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (CSV Format)
     */
    public function getAllTripsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get $startDate and $endDate from POST action
            list($startDate, $endDate) = $this->getDateInterval($postData, new DateInterval('P30D'));

            // Get the trips, in CSV string format
            $output = $this->reportsCsvService->getAllTripsCsv($startDate, $endDate);
        } catch (CsvParsingException $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo nel parsing dei dati CSV.');

            return false;
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->tripsCsvResponse($output);
    }

    /**
     * @return \Zend\Http\Response (CSV Format)
     */
    public function getCityTripsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get $startDate and $endDate from POST action
            list($startDate, $endDate) = $this->getDateInterval($postData, new DateInterval('P30D'));

            // Get $city from POST action
            $city = $this->getCity($postData);

            // Get the trips, in CSV string format
            $output = $this->reportsCsvService->getCityTripsCsv($startDate, $endDate, $city);
        } catch (MissingParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dalla mancanza di uno o pi&ugrave; parametri.');

            return false;
        } catch (InvalidParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dall\'invalidit&agrave; di uno o pi&ugrave; parametri.');

            return false;
        } catch (CsvParsingException $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo nel parsing dei dati CSV.');

            return false;
        } catch (Exception $e) {
            //error_log('Errore: '.$e->getMessage());
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->tripsCsvResponse($output);
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getUrbanAreasAction()
    {
        $city = $this->params()->fromRoute('city', 0);

        if (!isset($city)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the cities, in JSON format
        $urbanareas = $this->reportsService->getUrbanAreas($city);

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($urbanareas);

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getTripsGeoDataAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get $startDate and $endDate from POST action
            list($startDate, $endDate) = $this->getDateInterval($postData, new DateInterval('P30D'));

            // Get $begend from POST action
            $begend = $this->getBegEnd($postData);

            // Get the trips geo data, in JSON format
            $tripsgeodata = $this->reportsService->getTripsGeoData($startDate, $endDate, $begend);

            // So, we don't need to use a JsonModel,but simply use an Http Response
            $this->getResponse()->setContent($tripsgeodata);
        } catch (Exception $e) {
            //error_log('Errore: '.$e->getMessage());
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getCarsGeoDataAction()
    {
        try {
            // Get the trips geo data, in JSON format
            $carsgeodata = $this->reportsService->getCarsGeoData();

            // So, we don't need to use a JsonModel,but simply use an Http Response
            $this->getResponse()->setContent($carsgeodata);
        } catch (Exception $e) {
            //error_log('Errore: '.$e->getMessage());
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getTripAction()
    {
        $tripId = $this->params()->fromRoute('id', 0);

        if (!isset($tripId)) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            // Get the trips data, in JSON format
            $tripdata = $this->reportsService->getTrip($tripId);

            // So, we don't need to use a JsonModel,but simply use an Http Response
            $this->getResponse()->setContent($tripdata);
        } catch (Exception $e) {
            //error_log('Errore: '.$e->getMessage());
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getTripsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get vars from post action
            list($startDate, $endDate) = $this->getDateInterval($postData, new DateInterval('P1D'));
            $tripsNumber = $this->getTripsNumber($postData);
            $maintainer = $this->getMaintainer($postData);

            // Get the trips data, in JSON format
            $tripsdata = $this->reportsService->getTrips($startDate, $endDate, $tripsNumber, $maintainer);

            // So, we don't need to use a JsonModel,but simply use an Http Response
            $this->getResponse()->setContent($tripsdata);
        } catch (MissingParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dalla mancanza di uno o pi&ugrave; parametri.');

            return false;
        } catch (InvalidParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dall\'invalidit&agrave; di uno o pi&ugrave; parametri.');

            return false;
        } catch (Exception $e) {
            //error_log('Errore: '.$e->getMessage());
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (JSON Format)
     */
    public function getTripsFromLogsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get vars from post action
            list($startDate, $endDate) = $this->getDateInterval($postData, new DateInterval('P1D'));
            $tripsNumber = $this->getTripsNumber($postData);

            // Get the trips data, in JSON format
            $tripsdata = $this->reportsService->getTripsFromLogs($startDate, $endDate, $tripsNumber);

            // So, we don't need to use a JsonModel,but simply use an Http Response
            $this->getResponse()->setContent($tripsdata);
        } catch (MissingParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dalla mancanza di uno o pi&ugrave; parametri.');

            return false;
        } catch (InvalidParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dall\'invalidit&agrave; di uno o pi&ugrave; parametri.');

            return false;
        } catch (Exception $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->getResponse();
    }

    /**
     * @return \Zend\Http\Response (GPX Format)
     */
    public function getTripPointsFromLogsAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        try {
            $postData = $this->getRequest()->getPost();

            // Get vars from post action
            $getTripsId = $this->getTripsId($postData);

            // Get the trips data, in GPX format
            $tripsdata = $this->reportsService->getTripPointsFromLogs($getTripsId);
        } catch (MissingParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dalla mancanza di uno o pi&ugrave; parametri.');

            return false;
        } catch (InvalidParameter $e) {
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo causato dall\'invalidit&agrave; di uno o pi&ugrave; parametri.');

            return false;
        } catch (Exception $e) {
            //error_log('errore: '.$e, 0);
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo.');

            return false;
        }

        return $this->tripsGpxResponse($tripsdata);
    }

    /**
     * $this method generate two valid DateTime from a given $postData assoc array that
     * MUST contain a 'endDate' property.
     * In case ther's a 'startDate' the returned Interval is calculated between those
     * dates, otherwise the 'startDate' is the day before 'endDate'.
     *
     * @param array<string,mixed> $postData     Post data array.
     * @param \DateInterval       $dateInterval The Interval between the two date
     *                                          (used if there isn't a 'startDate')
     *
     * @return [\DateTime,\DateTime]
     */
    private function getDateInterval($postData, DateInterval $dateInterval)
    {
        $endDate = new DateTime($postData['end_date']);
        $endDate = $endDate->format('H:i:s') == '00:00:00' ? $endDate->setTime(23, 59, 59) : $endDate;

        // If the startDate is not set, we set it according to the $dateInterval
        if (empty($postData['start_date'])) {
            $date = new DateTime($postData['end_date']);
            $startDate = $date->sub($dateInterval);
        } else {
            $startDate = new DateTime($postData['start_date']);
        }
        //error_log('start: '.$startDate->format('Y-m-d H:i:s').'  ---  end: '.$endDate->format('Y-m-d H:i:s'), 0);

        return [$startDate, $endDate];
    }

    /**
     * $this method get a city (fleet_id) from a given $postData assoc array that
     * MUST contain a 'city' property.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return int
     *
     * @throws Exception\MissingParameter if $postData['city'] is not set
     * @throws Exception\InvalidParameter if $postData['city'] is not int
     */
    private function getCity($postData)
    {
        if (!isset($postData['city'])) {
            throw new MissingParameter('Missing city parameter.');
        }
        if (!is_int((int) $postData['city'])) {
            throw new InvalidParameter('The parameter city is not valid.');
        }

        return (int) $postData['city'];
    }

    /**
     * $this method get a tripsnumber from a given $postData assoc array that
     * MUST contain a 'trips_number' property.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return int
     *
     * @throws Exception\MissingParameter if $postData['trips_number'] is not set
     * @throws Exception\InvalidParameter if $postData['trips_number'] is not int
     */
    private function getTripsNumber($postData)
    {
        if (!isset($postData['trips_number'])) {
            throw new MissingParameter('Missing trips_id parameter.');
        }
        if (!is_int((int) $postData['trips_number'])) {
            throw new InvalidParameter('The parameter trips_number is not valid.');
        }

        return (int) $postData['trips_number'];
    }

    /**
     * $this method get a trip id from a given $postData assoc array that
     * MUST contain a 'id' property.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return int
     *
     * @throws Exception\MissingParameter if $postData['id'] is not set
     * @throws Exception\InvalidParameter if $postData['id'] is not int
     */
    private function getTripId($postData)
    {
        if (!isset($postData['id'])) {
            throw new MissingParameter('Missing trips_id parameter.');
        }
        if (!is_int((int) $postData['id'])) {
            throw new InvalidParameter('The parameter trips_number is not valid.');
        }

        return (int) $postData['id'];
    }

    /**
     * $this method get the trips ids from a given $postData assoc array that
     * MUST contain a 'trips_id' property.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return array<string,mixed>
     *
     * @throws Exception\MissingParameter if $postData['trips_id'] is not set
     * @throws Exception\InvalidParameter if $postData['trips_id'] is not an Array
     */
    private function getTripsId($postData)
    {
        if (!isset($postData['trips_id'])) {
            throw new MissingParameter('Missing trips_id parameter.');
        }
        if (!is_array($postData['trips_id'])) {
            throw new InvalidParameter('The parameter trips_id is not valid.');
        }

        return $postData['trips_id'];
    }

    /**
     * $this method get the bool value of mainteinter from a given $postData assoc array
     * that MUST contain a 'maintainer' property.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return true|false
     *
     * @throws Exception\MissingParameter if $postData['maintainer'] is not set
     * @throws Exception\InvalidParameter if $postData['maintainer'] is not Boolean, Int( 0 || 1 ) or Char( 't' || 'f' )
     */
    private function getMaintainer($postData)
    {
        if (!isset($postData['maintainer'])) {
            throw new MissingParameter('Missing trips_id parameter.');
        }
        if (preg_match('/^(1|t)/i', $postData['maintainer'])) {
            return true;
        } elseif (!preg_match('/^(0|f)/i', $postData['maintainer'])) {
            throw new InvalidParameter('The parameter maintainer is not valid.');
        }

        return false;
    }

    /**
     * $this method get a  (fleet_id) from a given $postData assoc array that
     * MUST contain a 'begend' property, that could be int, char or string.
     *
     * @param array<string,mixed> $postData Post data array.
     *
     * @return int
     *
     * @throws Exception\MissingParameter if $postData['begend'] is not set
     * @throws Exception\InvalidParameter if $postData['begend'] is not Boolean, Int( 0 || 1 )
     *                                    or Char( 'b' || 'e' ) or String starting with 'b' || 'e'
     */
    private function getBegEnd($postData)
    {
        if (!isset($postData['begend'])) {
            throw new MissingParameter('Missing begend parameter.');
        }

        if (preg_match('/^(0|b+e+g)/i', $postData['begend'])) {
            // Beginning Case
            return 0;
        } elseif (preg_match('/^(1|e+n+d)/i', $postData['begend'])) {
            // Ending Case
            return 1;
        }

        throw new InvalidParameter('The parameter begend is not valid.');
    }

    /**
     * $this method set the header to return an http csv response.
     *
     * @param string $csvData String containing CSV data
     *
     * @return Zend\Http\Response
     */
    private function tripsCsvResponse($csvData)
    {
        // Setting the header to return a CSV file
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type', 'text/csv')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="trips.csv"')
            ->addHeaderLine('Accept-Ranges', 'bytes')
            ->addHeaderLine('Content-Length', strlen($csvData));

        $response->setContent($csvData);

        return $response;
    }

    /**
     * $this method set the header to return an http gpx response.
     *
     * @param string $gpxData String containing GPX data
     *
     * @return Zend\Http\Response
     */
    private function tripsGpxResponse($gpxData)
    {
        // Setting the header to return a GPX file
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Accept-Ranges', 'bytes')
            ->addHeaderLine('Content-Length', strlen($gpxData))
            ->addHeaderLine('Content-Type', 'application/gpx')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="phpGPX.gpx"')
            ->addHeaderLine('Content-Transfer-Encoding', 'binary');

        $response->setContent($gpxData);

        return $response;
    }
}
