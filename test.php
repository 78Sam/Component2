<?php

class Test extends Exception
{
	public ?string $x;

	public function setErrorCode(int $errorCode): self
	{
		$this->code = $errorCode;

		return $this;
	}
}

$x = new Test('Hello');
$x->setErrorCode(112);
var_dump($x);
throw $x;