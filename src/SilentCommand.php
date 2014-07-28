<?php namespace DCarbone\Helpers;

/**
 * Class SilentCommand
 * @package DCarbone\Helpers
 */
abstract class SilentCommand
{
    /** @var string */
    public static $tmpDir = __DIR__;

    /** @var string */
    public static $scriptRootDir = null;

    /**
     * @param string|array $commands
     * @param bool $cdToScriptRoot
     * @return void
     */
    public static function execute($commands, $cdToScriptRoot = false)
    {
        if (stripos(php_uname(), 'windows') === 0)
            static::executeWindows($commands, $cdToScriptRoot);
        else
            static::executeLinux($commands, $cdToScriptRoot);
    }

    /**
     * @param string|array $commands
     * @param bool $cdToScriptRoot
     * @return void
     */
    public static function executeWindows($commands, $cdToScriptRoot = false)
    {
        $bat_filename = static::$tmpDir.DIRECTORY_SEPARATOR.uniqid().'-silent_command.bat';
        $bat_file = fopen($bat_filename, "w");
        if($bat_file)
        {
            fwrite($bat_file, "@echo off \n");

            if ($cdToScriptRoot)
                fwrite($bat_file, 'cd '.static::$scriptRootDir."\n");

            if (is_string($commands))
            {
                fwrite($bat_file, $commands."\n");
            }
            else if (is_array($commands))
            {
                foreach($commands as $c)
                {
                    fwrite($bat_file, $c."\n");
                }
            }

            fwrite($bat_file, 'DEL "'.$bat_filename.'"'."\n");
            fclose($bat_file);

            $exe = 'start /b '.$bat_filename;

            pclose(popen($exe, 'r'));
        }
    }

    /**
     * @param string|array $commands
     * @param bool $cdToScriptRoot
     * @return void
     */
    public static function executeLinux($commands, $cdToScriptRoot = false)
    {
        $sh_filename = static::$tmpDir.'/'.uniqid().'-silent_command.sh';
        $sh_file = fopen($sh_filename, 'w');
        if ($sh_file)
        {
            fwrite($sh_file, '#!/bin/bash'."\n");

            if ($cdToScriptRoot)
                fwrite($sh_file, 'cd '.static::$scriptRootDir."\n");

            if (is_string($commands))
            {
                fwrite($sh_file, $commands."\n");
            }
            else if (is_array($commands))
            {
                foreach($commands as $c)
                {
                    fwrite($sh_file, $c."\n");
                }
            }

            fwrite($sh_file, 'rm -- "'.$sh_filename.'"'."\n");
            fclose($sh_file);

            $sh = 'nohup nice sh "'.$sh_filename.'" > /dev/null 2>/dev/null &';

            exec($sh, $output, $return);
        }
    }
}