<?php
require_once (__DIR__."/BaseRepository.php");

class ParticipationRepository extends BaseRepository
{
    public function insertData(array $data): array
    {
        $response = array('status' => false, 'message' => '');
        if(is_array($data) && count($data) > 0){
            $query = 'INSERT IGNORE INTO participation (employee_id,event_id,fee,version) ';
            $insertValues = array();
            foreach ($data as $row) {
                if(is_null($row['version'])){
                    $insertValues[] = '(SELECT emp.id, eve.id,"' . $row['fee'].'", NULL FROM employee emp, events eve WHERE emp.email like "'.$row['emp_email'].'" and eve.name like "'.$row['event_name'].'")';
                }else{
                    $insertValues[] = '(SELECT emp.id, eve.id,"' . $row['fee'].'", "' . $row['version'] . '" FROM employee emp, events eve WHERE emp.email like "'.$row['emp_email'].'" and eve.name like "'.$row['event_name'].'")';
                }
            }
            try{
                $sqlStatement = $this->connectionManager->prepare($query.implode(' UNION ALL ',$insertValues));
                $response['status'] = $sqlStatement->execute();
            }catch (Exception $exception){
                $response['message'] = $exception->getMessage();
            }
        }
        return $response;
    }
}