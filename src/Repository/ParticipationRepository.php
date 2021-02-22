<?php
require_once(__DIR__ . "/BaseRepository.php");

class ParticipationRepository extends BaseRepository
{
    public function insertData(array $data): array
    {
        $response = array('status' => false, 'message' => '');
        if (is_array($data) && count($data) > 0) {
            $query = 'INSERT IGNORE INTO participation (employee_id,event_id,fee,version) ';
            $insertValues = array();
            foreach ($data as $row) {
                if (is_null($row['version'])) {
                    $insertValues[] = '(SELECT emp.id, eve.id,"' . $row['fee'] . '", NULL FROM employee emp, events eve WHERE emp.email like "' . $row['emp_email'] . '" and eve.name like "' . $row['event_name'] . '")';
                } else {
                    $insertValues[] = '(SELECT emp.id, eve.id,"' . $row['fee'] . '", "' . $row['version'] . '" FROM employee emp, events eve WHERE emp.email like "' . $row['emp_email'] . '" and eve.name like "' . $row['event_name'] . '")';
                }
            }
            try {
                $sqlStatement = $this->connectionManager->prepare($query . implode(' UNION ALL ', $insertValues));
                $response['status'] = $sqlStatement->execute();
            } catch (Exception $exception) {
                $response['message'] = $exception->getMessage();
            }
        }
        return $response;
    }

    public function prepareFilterQuery(array $searchData)
    {
        $query = 'SELECT e.name as empName,e.email,ev.name as eventName,ev.event_date,p.fee,p.version FROM participation p JOIN employee e ON (e.id = p.employee_id) JOIN events ev ON (ev.id = p.event_id)';
        $filterQuery = array();
        foreach ($searchData as $key => $value) {
            switch ($key) {
                case "emp_name":
                    $filterQuery[] = 'e.name like "%' . $value . '%"';
                    break;
                case "emp_email":
                    $filterQuery[] = 'e.email like "%' . $value . '%"';
                    break;
                case "event_name":
                    $filterQuery[] = 'ev.name like "%' . $value . '%"';
                    break;
                case "event_date":
                    $filterQuery[] = 'ev.event_date like "%' . $value . '%"';
                    break;
            }
        }
        if (count($filterQuery) > 0) {
            $query .= ' WHERE ' . implode(' AND ', $filterQuery);
        }
        return $query;
    }

    public function getFilterData(array $searchData): array
    {
        $response = array('status' => false, 'message' => '', 'data' => array());
        $query = $this->prepareFilterQuery($searchData);
        try {
            $sqlStatement = $this->connectionManager->prepare($query);
            $sqlStatement->execute();
            $data = $sqlStatement->fetchAll(PDO::FETCH_ASSOC);
            if (($data !== false) && (count($data) > 0)) {
                $response['status'] = true;
                $response['data'] = $data;
            }
        } catch (Exception $exception) {
            $response['message'] = $exception->getMessage();
        }
        return $response;
    }
}