# Kohana 3.x Internationalization Module

**Internationalization can be a pain**: send translation files off to different people, import them, consolidate them, copy keys between files, add keys to the language files, etc-- this module eases some of those pains.

## How to use:

Here are a few quick examples to get you started in managing your localizations.  This is actually built into Kohana's command line interpreter.

### Generating a language file

This goes through your application directory and looks for the localizing helper function (found in system/base.php).  It generates a language file based off of all the occurances it finds.

		php index.php --uri=i18n/generate --lang=en

### Copying keys between files

You have one language file, and you need to update the other language file because you added / removed keys.  This will take the source file and overwrite it with values of the target file, and output the final result.

		php index.php --uri=i18n/copy_keys --source=en --target=fr
		
### Diff between language files

Find out which keys exist in the source, but not in the target file

		php index.php --uri=i18n/diff_keys --source=en --target=fr

### Copy language files to a database

Assuming you already have a database configured, it will automatically generate the table and put the language keys (from all language files) into the database.

		php index.php --uri=i18n/db_import

### Export database data to files

This will remove all of the files from the APPPATH/i18n directory and will replace it with what is in the database. NOTE: If you are using source control, be sure to take into account checked in files and conflicts.

		php index.php --uri=i18n/db_export

## Upcoming Features

I would like to add some of the following functionality to this module:

* A web-based language editor
* Excel import / export option