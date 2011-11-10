<?php defined('SYSPATH') OR die('No direct access allowed.');

class Controller_I18n extends Controller {
	
	/**
	 * This copies keys from one language to another. It will
	 * take the language keys and overwrite any existing ones,
	 * and then write it out to the file again.
	 *
	 * You will lose keys that do not exist in --source
	 *
	 * @option  --source  string  Language en, us, fr-fr
	 * @option  --target  string  Language en, us, fr-fr
	 * @option  --clear  int  Optional: Does not overwrite with translated keys
	 */
	public function action_copy_keys()
	{
		$options = CLI::options('source', 'target', 'clear');
		
		// Get subdirectories
		$source = str_replace('-', '/', $options['source']);
		$target = str_replace('-', '/', $options['target']);
		
		$source_file = APPPATH."i18n/$source".EXT;
		$target_file = APPPATH."i18n/$target".EXT;
		
		// Grab the files, unless they dont exist yet
		$source = is_file($source_file) ? include $source_file : array();
		$target = is_file($target_file) ? include $target_file : array();
		
		echo "Keys: [source=".count($source)." target=".count($target)."]\n\n";
		
		// If you do not want to use already translated keys
		if (! isset($options['clear'])) $source = Arr::overwrite($source, $target);
		
		// Format it for the view
		$data_formatted = array();
		
		foreach ($source as $key => $value)
		{
			$class = new stdclass();
			$class->key = $key;
			$class->text = $value;
			
			$data_formatted[] = $class;
		}
		
		$content = View::factory('i18n/lang_file')->set('langs', $data_formatted);
		
		$this->__write_file($options['target'], $content);
		
		echo "Complete.\n";
	}
	
	/**
	 * Sync all of the translation files and keys
	 * to the database, any that exist will be ignored.
	 */
	public function action_db_import()
	{
		// This will check to see if the table exists
		$model = ORM::factory('translation');
		
		$langs = Kohana::list_files('i18n', array(APPPATH));
		$langs = Arr::flatten($langs);
		
		foreach ($langs as $file => $paths)
		{
			$lang_key = $this->__file_to_lang($file);
			$lang_data = include APPPATH.$file;
			
			foreach ($lang_data as $key => $value)
			{
				// Check to see if it exists
				$exists = (bool) DB::select(DB::expr('COUNT(*) AS Count'))
					->from('translations')
					->where('key', '=', $key)
					->where('language', '=', $lang_key)
					->execute()
					->get('Count');
					
				if (! $exists)
				{
					$model = ORM::factory('translation');
					
					$model->key = $key;
					$model->language = $lang_key;
					$model->text = $value;
					$model->save();
				}
			}
		}
	}
	
	/**
	 * This will remove all of the files from the APPPATH/i18n
	 * directory and will replace it with what is in the database.
	 * If you are using git/subversion be sure to take into account
	 * checked in files and conflicts.
	 */
	public function action_db_export()
	{
		$path = APPPATH.'i18n';
		
		if (! is_writable($path)) throw new Kohana_Exception(':path must be writable', array(':path' => $path));
		
		$model = ORM::factory('translation');
		
		$files = Kohana::list_files('i18n', array(APPPATH));
		
		// Remove old files
		foreach ($files as $file => $path) unlink($path);
		
		$langs = DB::select('language')
			->from('translations')
			->group_by('language')
			->execute();
		
		foreach ($langs as $lang)
		{
			$file = $this->__lang_to_file($lang['language']);
			
			$langs = $model->where('language', '=', $lang['language'])->find_all();
			
			$content = View::factory('i18n/lang_file')->set('langs', $langs);
		
			$this->__write_file($lang['language'], $content);
		}
	}
	
	private function __write_file($lang, $content)
	{
		$content = mb_convert_encoding($content, 'UTF-8');
		
		// Write the contents to the file
		$file = APPPATH.$this->__lang_to_file($lang);
		
		file_put_contents($file, $content);
		
		chmod($file, 0755);
	}
	
	private function __file_to_lang($file)
	{
		$key_file = str_replace(EXT, '', $file);
		$key_parts = explode('/', $key_file);
		
		array_shift($key_parts);
		
		return implode('-', $key_parts);
	}
	
	private function __lang_to_file($lang)
	{
		$key_parts = explode('-', $lang);
		
		return 'i18n/'.implode('/', $key_parts).EXT;
	}
	
}