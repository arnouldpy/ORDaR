<?php
namespace search\model;


use Illuminate\Database\Eloquent\Model as model;

class LostPassword extends model{
	public $incrementing  = false;
	protected $table = 'lost_password';
	protected $primaryKey = 'mail';
	private $lost_password;



	
}
