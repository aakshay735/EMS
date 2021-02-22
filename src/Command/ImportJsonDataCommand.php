<?php
require_once(__DIR__.'/../Repository/EmployeeRepository.php');
require_once(__DIR__.'/../Repository/EventRepository.php');
require_once(__DIR__.'/../Repository/ParticipationRepository.php');

class ImportJsonDataCommand
{
    private $empRepo;
    private $eventRepo;
    private $participationRepo;

    public function __construct()
    {
        $this->empRepo = new EmployeeRepository();
        $this->eventRepo = new EventRepository();
        $this->participationRepo = new ParticipationRepository();
    }

    /**
     * @param $jsonFilePath
     * @return array
     */
    private function getJsonData($jsonFilePath): array
    {
        $data = array();
        if(!is_null($jsonFilePath) && $jsonFilePath != ''){
            $data = file_get_contents($jsonFilePath);
            $data = json_decode($data, true);
        }
        return $data;
    }

    /**
     * @param array $empData
     * @param array $eventData
     * @param array $participationArrayData
     */
    private function insertDataToDB(array $empData, array $eventData, array $participationArrayData)
    {
        $this->empRepo->insertData(array_unique($empData, SORT_REGULAR));
        $this->eventRepo->insertData(array_unique($eventData, SORT_REGULAR));
        $this->participationRepo->insertData(array_unique($participationArrayData, SORT_REGULAR));
    }

    /**
     * @param array $data
     * @return array
     */
    private function getEmployeeData(array $data): array
    {
        $empDetails = array();
        $empDetails['name'] = isset($data['employee_name'])?$data['employee_name']:'';
        $empDetails['email'] = isset($data['employee_mail'])?$data['employee_mail']:'';
        return $empDetails;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getEventData(array $data): array
    {
        $eventDetails = array();
        $eventDetails['id'] = isset($data['event_id'])?$data['event_id']:null;
        $eventDetails['name'] = isset($data['event_name'])?$data['event_name']:'';
        $eventDetails['date'] = isset($data['event_date'])?date('Y-m-d',strtotime($data['event_date'])):'';
        return $eventDetails;
    }

    /**
     * @param array $data
     * @return array
     */
    private function getParticipationData(array $data): array
    {
        $participationDetails = array();
        $participationDetails['emp_email'] = isset($data['employee_mail'])?$data['employee_mail']:'';
        $participationDetails['event_name'] = isset($data['event_name'])?$data['event_name']:'';
        $participationDetails['fee'] = isset($data['participation_fee'])?$data['participation_fee']:0;
        $participationDetails['version'] = isset($data['version'])?$data['version']:null;
        return $participationDetails;
    }

    private function parseJsonData()
    {
        $participationJsonData = $this->getJsonData(__DIR__.'/../../assets/code.json');
        $count = 0;
        $empArray = $eventArray = $participationArray = array();
        foreach ($participationJsonData as $data){
            $count++;
            $empArray[] = $this->getEmployeeData($data);
            $eventArray[] = $this->getEventData($data);
            $participationArray[] = $this->getParticipationData($data);

            if($count % 50 == 0){
                $this->insertDataToDB($empArray,$eventArray,$participationArray);
                $empArray = $eventArray = $participationArray = array();
            }
        }
        $this->insertDataToDB($empArray,$eventArray,$participationArray);
        echo "process completed at ".time()."\n";
    }

    public function execute()
    {
        echo "process started at ".time()." \n";
        return $this->parseJsonData();
    }
}

$importCmd = new ImportJsonDataCommand();
$importCmd->execute();