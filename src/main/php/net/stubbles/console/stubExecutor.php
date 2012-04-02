<?php
/**
 * Interface for command executors.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubExecutor.php 2298 2009-08-24 09:25:34Z mikey $
 */
stubClassLoader::load('net::stubbles::streams::stubOutputStream');
/**
 * Interface for command executors.
 *
 * @package     stubbles
 * @subpackage  console
 */
interface stubExecutor extends stubObject
{
    /**
     * sets the output stream to write data outputted by executed command to
     *
     * @param   stubOutputStream  $out
     * @return  stubExecutor
     */
    public function streamOutputTo(stubOutputStream $out);

    /**
     * returns the output stream to write data outputted by executed command to
     *
     * @return  stubOutputStream
     */
    public function getOutputStream();

    /**
     * executes given command
     *
     * @param   string        $command
     * @return  stubExecutor
     */
    public function execute($command);

    /**
     * executes given command asynchronous
     *
     * The method starts the command, and returns an input stream which can be
     * used to read the output of the command manually.
     *
     * @param   string           $command
     * @return  stubInputStream
     */
    public function executeAsync($command);

    /**
     * executes command directly and returns output as array (each line as one entry)
     *
     * @param   string         $command
     * @return  array<string>
     */
    public function executeDirect($command);
}
?>