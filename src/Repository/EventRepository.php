<?php
require_once(__DIR__ . "/BaseRepository.php");

class EventRepository extends BaseRepository
{
    public function insertData(array $data): array
    {
        $response = array('status' => false, 'message' => '');

        if (is_array($data) && count($data) > 0) {
            $query = 'INSERT IGNORE INTO events (id,name,event_date) VALUES ';
            $insertValues = array();
            foreach ($data as $row) {
                if (is_null($row['id'])) {
                    $insertValues[] = '(NULL,"' . $row['name'] . '", "' . $row['date'] . '")';
                } else {
                    $insertValues[] = '("' . $row['id'] . '","' . $row['name'] . '", "' . $row['date'] . '")';
                }
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