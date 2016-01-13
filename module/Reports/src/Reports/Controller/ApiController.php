<?php

namespace Reports\Controller;

// Internal Modules
use Reports\Service\ReportsService;

// External Modules
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\Http\Response;
use DateTime;
use Zend\Form\Form;
use DateInterval;

class ApiController extends AbstractActionController
{
    /**
     * @var ReportsService
     */
    private $reportsService;

    public function __construct(ReportsService $reportsService)
    {
        $this->reportsService = $reportsService;
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
        $startDate = '';
        $endDate = '';

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['end_date'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $endDate = $postData['end_date'];
            }

            // If the start_date is null, will set it with 30 days before the end_date
            if (!isset($postData['start_date']) || $postData['start_date'] == '') {
                $date = new DateTime($endDate);
                $interval = new DateInterval('P30D');

                $date->sub($interval);
                $startDate = $date->format('d-m-Y 23:59:59');
            } else {
                $startDate = $postData['start_date'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

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
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');

            return false;
        }

        // Setting the header to return a CSV file
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type', 'text/csv')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="trips.csv"')
            ->addHeaderLine('Accept-Ranges', 'bytes')
            ->addHeaderLine('Content-Length', strlen($output));

        $response->setContent($output);

        return $response;
    }

    public function getCityTripsAction()
    {
        $startDate = '';
        $endDate = '';
        $city = '';

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['end_date']) && !isset($postData['city'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $endDate = $postData['end_date'];
                $city = $postData['city'];
            }

            // If the start_date is null, will set it with 30 days before the end_date
            if (!isset($postData['start_date']) || $postData['start_date'] == '') {
                $date = new DateTime($endDate);
                $interval = new DateInterval('P30D');

                $date->sub($interval);
                $startDate = $date->format('d-m-Y 23:59:59');
            } else {
                $startDate = $postData['start_date'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

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
            $this->flashMessenger()->addErrorMessage('Si è verificato un errore applicativo. L\'assistenza tecnica è già al corrente, ci scusiamo per l\'inconveniente');

            return false;
        }

        // Setting the header to return a CSV file
        $response = $this->getResponse();
        $response->getHeaders()
            ->addHeaderLine('Content-Type', 'text/csv')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="citytrips.csv"')
            ->addHeaderLine('Accept-Ranges', 'bytes')
            ->addHeaderLine('Content-Length', strlen($output));

        $response->setContent($output);

        return $response;
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

    public function getTripsGeoDataAction()
    {
        $startDate = '';
        $endDate = '';
        $begend = -1;

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['end_date']) && !isset($postData['begend'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $endDate = $postData['end_date'];
                $begend = $postData['begend'];
            }

            // If the start_date is null, will set it with 30 days before the end_date
            if (!isset($postData['start_date']) || $postData['start_date'] == '') {
                $date = new DateTime($endDate);
                $interval = new DateInterval('P30D');

                $date->sub($interval);
                $startDate = $date->format('d-m-Y 23:59:59');
            } else {
                $startDate = $postData['start_date'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the trips geo data, in JSON format
        $tripsgeodata = $this->reportsService->getTripsGeoData($startDate, $endDate, $begend);

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($tripsgeodata);

        return $this->getResponse();
    }

    public function getCarsGeoDataAction()
    {
        if (!$this->getRequest()->isPost()) {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the trips geo data, in JSON format
        $carsgeodata = $this->reportsService->getCarsGeoData($startDate, $endDate, $begend);

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($carsgeodata);

        return $this->getResponse();
    }

    public function getTripsAction()
    {
        $startDate = '';//"2016-01-01";
        $endDate = '';//"2016-01-02";
        $tripsNumber = 15;
        $maintainer = false;

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['end_date'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $endDate = $postData['end_date'];
            }

            if (isset($postData['trips_number'])) {
                $tripsNumber = $postData['trips_number'];
            }

            if (isset($postData['maintainer'])) {
                $maintainer = ($postData['maintainer'] === 'true');
            }

            // If the start_date is null, will set it with 30 days before the end_date
            if (!isset($postData['start_date']) || $postData['start_date'] == '') {
                $date = new DateTime($endDate);
                $interval = new DateInterval('P1D');

                $date->sub($interval);
                $startDate = $date->format('d-m-Y 23:59:59');
            } else {
                $startDate = $postData['start_date'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the trips data, in JSON format
        $tripsdata = $this->reportsService->getTrips((new DateTime($startDate))->format('Y-m-d H:i:s'), (new DateTime($endDate))->format('Y-m-d H:i:s'), $tripsNumber, $maintainer);

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($tripsdata);

        return $this->getResponse();
    }

    public function getTripsFromLogsAction()
    {
        $startDate = '';//"2016-01-01";
        $endDate = '';//"2016-01-02";
        $tripsNumber = 15;

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['end_date'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $endDate = $postData['end_date'];
            }

            if (isset($postData['trips_number'])) {
                $tripsNumber = $postData['trips_number'];
            }

            // If the start_date is null, will set it with 30 days before the end_date
            if (!isset($postData['start_date']) || $postData['start_date'] == '') {
                $date = new DateTime($endDate);
                $interval = new DateInterval('P1D');

                $date->sub($interval);
                $startDate = $date->format('d-m-Y 23:59:59');
            } else {
                $startDate = $postData['start_date'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the trips data, in JSON format
        $tripsdata = $this->reportsService->getTripsFromLogs($startDate, $endDate, $tripsNumber);

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($tripsdata);

        return $this->getResponse();
    }

    public function getTripPointsFromLogsAction()
    {
        $tripsId = '';// ["113893","114005","113994","113927"];

        // Getting Post vars
        if ($this->getRequest()->isPost()) {
            $postData = $this->getRequest()->getPost()->toArray();

            if (!isset($postData['trips_id'])) {
                $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

                return false;
            } else {
                $tripsId = $postData['trips_id'];
            }
        } else {
            $this->getResponse()->setStatusCode(Response::STATUS_CODE_404);

            return false;
        }

        // Get the trips data, in JSON format
        $tripsdata = $this->reportsService->getTripPointsFromLogs($tripsId);

        $this->getResponse()->getHeaders()
            ->addHeaderLine('Accept-Ranges', 'bytes')
            ->addHeaderLine('Content-Length', strlen($tripsdata))
            ->addHeaderLine('Content-Type', 'application/gpx')
            ->addHeaderLine('Content-Disposition', 'attachment; filename="phpGPX.gpx"')
            ->addHeaderLine('Content-Transfer-Encoding', 'binary');

        // So, we don't need to use a JsonModel,but simply use an Http Response
        $this->getResponse()->setContent($tripsdata);

        return $this->getResponse();
    }
}
