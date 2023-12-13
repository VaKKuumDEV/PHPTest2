<?php
class Validator{
	public static function validatePhone(string $phone): bool{
		$phone = self::cleanPhone($phone);
		return preg_match('^(?:\+7|8|7)\d{10}$^', $phone);
	}
	
	public static function cleanPhone(string $phone): string{
		return preg_replace('/[^0-9]+/', '', $phone);
	}
	
	public static function validateEmail(string $mail): bool{
		return filter_var($mail, FILTER_VALIDATE_EMAIL) !== false;
	}
	
	public static function validateName(string $name): bool{
		return !preg_match("/[^а-яёА-ЯЁ ]/u", $name);
	}
}
?>