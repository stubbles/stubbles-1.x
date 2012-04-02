<?php
/**
 * Runner for console commands.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubConsoleCommandRunner.php 2240 2009-06-16 21:50:52Z mikey $
 */
stubClassLoader::load('net::stubbles::console::stubConsoleCommand',
                      'net::stubbles::console::stubConsoleOutputStream',
                      'net::stubbles::ioc::stubApp'
);
/**
 * Runner for console commands.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubConsoleCommandRunner extends stubBaseObject
{
    /**
     * main method
     *
     * @param   string  $projectPath
     * @param   array   $argv
     * @return  int     exit code
     */
    public static function main($projectPath, array $argv)
    {
        return self::run($projectPath, $argv, stubConsoleOutputStream::forError());
    }

    /**
     * running method
     *
     * @param   string            $projectPath
     * @param   array             $argv
     * @param   stubOutputStream  $err
     * @return  int               exit code
     */
    public static function run($projectPath, array $argv, stubOutputStream $err)
    {
        if (isset($argv[2]) === false) {
            $err->writeLine('*** Missing classname of command class to execute');
            return 1;
        }
        
        array_shift($argv); // stubcli
        array_shift($argv); // project
        $commandClass = array_shift($argv);
        try {
            return (int) stubApp::createInstance($commandClass, $projectPath, array_values($argv))
                                ->run();
        } catch (Exception $e) {
            $classname = (($e instanceof stubException) ? ($e->getClassName()) : (get_class($e)));
            $err->writeLine('*** ' . $classname . ': ' . $e->getMessage());
            return 70;
        }
    }
}
?>