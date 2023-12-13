<?php
class Logger{
	public static function log(string $message): void{
		$formated = '[' . date('Y-m-d H:i:s') . ']' . $message . PHP_EOL;
		
		$f = fopen('log.txt', 'a');
		fwrite($f, $formated);
		fclose($f);
	}
}
?>