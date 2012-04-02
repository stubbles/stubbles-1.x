<?php
/**
 * Interface for commands to be executed on the command line.
 *
 * @package     stubbles
 * @subpackage  console
 */
/**
 * Interface for commands to be executed on the command line.
 *
 * @package     stubbles
 * @subpackage  console
 */
interface stubConsoleCommand extends stubObject
{
    /**
     * runs the command and returns an exit code
     *
     * @return  int
     */
    public function run();
}
?>