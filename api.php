<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

class ApiAnswer{
	private $code = 0;
	private $message = '';
	private $pars = [];
	
	public function __construct(int $code, string $message, array $pars = []){
		$this->code = $code;
		$this->message = $message;
		$this->pars = $pars;
	}
	
	public function getData(): array{
		$data = ['code' => $this->code, 'message' => $this->message];
		$data = array_merge($data, $this->pars);
		return $data;
	}
}

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/sheets.php';
require_once __DIR__ . '/validators.php';
require_once __DIR__ . '/logger.php';

$googleAccountKeyFilePath = __DIR__ . '/key.json';
putenv('GOOGLE_APPLICATION_CREDENTIALS=' . $googleAccountKeyFilePath);

$answer = new ApiAnswer(0, 'Не переданы параметры');
try{
	if(isset($_REQUEST['name'], $_REQUEST['phone'], $_REQUEST['mail'])){
		$name = $_REQUEST['name'];
		$phone = $_REQUEST['phone'];
		$mail = $_REQUEST['mail'];
		
		$errors = [];
		if(!Validator::validateName($name)) $errors[] = ['type' => 'name', 'name' => 'Имя', 'data' => $name];
		if(!Validator::validatePhone($phone)) $errors[] = ['type' => 'phone', 'name' => 'Номер телефона', 'data' => $phone];
		if(!Validator::validateEmail($mail)) $errors[] = ['type' => 'mail', 'name' => 'Почта', 'data' => $mail];
		
		if(count($errors) > 0){
			$answer = new ApiAnswer(0, 'Форма заполнена некорректно', ['errors' => $errors]);
			Logger::log('Данные не валидны: ' . json_encode($errors, JSON_UNESCAPED_UNICODE));
		}else{
			$sheetsDriver = new GSheets();
			$sheetsDriver->addRows([[$name, Validator::cleanPhone($phone), $mail, date('Y-m-d H:i')]]);
			$answer = new ApiAnswer(1, 'Данные успешно добавлены');
			
			Logger::log('Данные валидны');
		}
	}
}catch(Exception $ex){
	$answer = new ApiAnswer(0, 'Ошибка выполнения: ' . $ex->getMessage());
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode($answer->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
?>