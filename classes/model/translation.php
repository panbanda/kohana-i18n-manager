<?php defined('SYSPATH') OR die('No direct access allowed.');

class Model_Translation extends ORM {
	
	public function __construct($id = NULL)
	{
		try
		{
			// Check to see if the table exists
			return parent::__construct($id);
		}
		
		catch(Kohana_Exception $e)
		{
			// The table does not exist, lets create it
			self::create_table();
			
			return parent::__construct($id);
		}
	}
	
	public function save($id = NULL)
	{
		// This is the date it was added to database
		if (empty($this->date_created)) $this->date_created = date('Y-m-d H:i:s');
		
		// Update the current update time
		$this->date_modified = date('Y-m-d H:i:s');
		
		parent::save($id);
	}
	
	public static function create_table()
	{
		$config = Kohana::config('database')->default;
		
		// Lets create the table
		$a = DB::query(Database::UPDATE, 'CREATE TABLE IF NOT EXISTS `'.$config['table_prefix'].'translations` (
			`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			`key` varchar(255) NOT NULL,
			`language` varchar(32) NOT NULL,
			`text` text NOT NULL,
			`date_created` datetime NOT NULL,
			`date_modified` datetime NOT NULL,
			PRIMARY KEY (`id`),
			KEY `key` (`key`),
			KEY `language` (`language`)
		      ) ENGINE=MyISAM CHARACTER SET utf8 COLLATE utf8_general_ci AUTO_INCREMENT=1;')->execute();
	}
	
}