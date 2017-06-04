<?php
class RestfulResponse extends Exception{

	protected $status;
	protected $message;
	protected  $data;
	protected  $code;

	/**
	 * @return int
	 */
	public function getTotal()
	{
		return $this->total;
	}

	/**
	 * @param int $total
	 */
	public function setTotal($total)
	{
		$this->total = $total;
	}
	private  $total=0;

	/**
	 * @return mixed
	 */
	public function getStatus()
	{
		return $this->status;
	}

	/**
	 * @param mixed $status
	 */
	public function setStatus(int $status)
	{
		$this->status = $status;
	}

	/**
	 * @return mixed
	 */
	public function getUserFriendlyMessage()
	{
		return $this->message;
	}

	/**
	 * @param mixed $message
	 */
	public function setUserFriendlyMessage(String $message)
	{
		$this->message = $message;
	}

	/**
	 * @return mixed
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * @param mixed $data
	 */
	public function setData($data)
	{
		$this->data = $data;
	}

	/**
	 * @return mixed
	 */
	public function getCodes()
	{
		return $this->code;
	}

	/**
	 * @param mixed $code
	 */
	public function setCodes(bool $code)
	{
		$this->code = $code;
	}

	/**
	 * RestfulResponse constructor.
	 * @param int $status
	 * @param String $message
	 * @param Object $data
	 * @param int $code
	 */
	public  function __construct(int $status, String $message,array $data, bool $code, int $total=0) {
		$this->setStatus($status);
		$this->setUserFriendlyMessage($message);
		$this->setData($data);
		$this->setCodes($code);
		$this->setTotal($total);
	}

	/**
	 * @return string
	 * Convert  array of key and value to class Object 
	 */
public function expose() {
	// TODO Auto-generated method stub
return json_encode(get_object_vars($this));
}

}
