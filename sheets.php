<?php
class GSheets{
	private $driver;
	private $sid = '15aY_miJjzgyZ5bN6r-660DUbeGQIqr5f0iCv7Tn3w5E';
	
	public function __construct(){
		$client = new Google_Client();
		$client->useApplicationDefaultCredentials();
		$client->addScope('https://www.googleapis.com/auth/spreadsheets');
		 
		$service = new Google_Service_Sheets($client);
		$this->driver = $service;
	}
	
	public function getSheets(): array{
		$response = $this->driver->spreadsheets->get($this->sid);
		return $response->getSheets();
	}
	
	public function getTableContent(string $range): array{
		$response = $this->driver->spreadsheets_values->get($this->sid, $range);
		if(isset($response['values'])) return $response['values'];
		return [];
	}
	
	public function addRows(array $values): void{
		$body = new Google_Service_Sheets_ValueRange(['values' => $values]);
		$options = array('valueInputOption' => 'RAW');
		
		$content = $this->getTableContent('Users!A:D');
		$row = count($content) + 1;
		
		$this->driver->spreadsheets_values->update($this->sid, 'Users!A' . $row, $body, $options);
	}
}
?>