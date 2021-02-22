<?php
require_once(__DIR__ . "/BaseRepository.php");

class EmployeeRepository extends BaseRepository
{
    public function insertData(array $data): array
    {
        $response = array('status' => false, 'message' => '');
        if (is_array($data) && count($data) > 0) {
            $query = 'INSERT IGNORE INTO employee (name,email) VALUES ';
            $insertValues = array();
            foreach ($data as $row) {
                $insertValues[] = '("' . $row['name'] . '", "' . $row['email'] . '")';
            }
            try {
                $sqlStatement = $this->connectionManager->prepare($query . implode(',', $insertValues));
                $response['status'] = $sqlStatement->execute();
            } catch (Exception $exception) {
                $response['message'] = $exception->getMessage();
            }
        }
        return $response;
    }
}