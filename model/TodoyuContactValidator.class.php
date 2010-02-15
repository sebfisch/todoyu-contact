<?php

class TodoyuContactValidator {

	public static function isCurrentPassword($value, array $validatorConfig, TodoyuFormElement $formElement, array $formData) {
		return Todoyu::user()->get('password') === md5(trim($value));
	}

}

?>