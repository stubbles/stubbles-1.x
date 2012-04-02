<?php
/**
 * Class to execute commands on the command line.
 *
 * @package     stubbles
 * @subpackage  console
 * @version     $Id: stubConsoleExecutor.php 2572 2010-06-24 11:50:25Z bensch $
 */
stubClassLoader::load('net::stubbles::console::stubCommandInputStream',
                      'net::stubbles::console::stubExecutor',
                      'net::stubbles::lang::exceptions::stubRuntimeException'
);
/**
 * Class to execute commands on the command line.
 *
 * @package     stubbles
 * @subpackage  console
 */
class stubConsoleExecutor extends stubBaseObject implements stubExecutor
{
    /**
     * output stream to write data outputted by executed command to
     *
     * @var  stubOutputStream
     */
    protected $out;
    /**
     * redirect direction
     *
     * @var  string
     */
    protected $redirect = '2>&1';

    /**
     * sets the output stream to write data outputted by executed command to
     *
     * @param   stubOutputStream  $out
     * @return  stubExecutor
     */
    public function streamOutputTo(stubOutputStream $out)
    {
        $this->out = $out;
        return $this;
    }

    /**
     * returns the output stream to write data outputted by executed command to
     *
     * @return  stubOutputStream
     */
    public function getOutputStream()
    {
        return $this->out;
    }

    /**
     * sets the redirect
     *
     * @param   string        $redirect
     * @return  stubExecutor
     */
    public function redirectTo($redirect)
    {
        $this->redirect = $redirect;
        return $this;
    }

    /**
     * executes given command
     *
     * @param   string        $command
     * @return  stubExecutor
     * @throws  stubRuntimeException
     */
    public function execute($command)
    {
        $pd = popen($command . ' ' . $this->redirect, 'r');
        if (false === $pd) {
            throw new stubRuntimeException('Can not execute ' . $command);
        }

        while (feof($pd) === false && false !== ($line = fgets($pd, 4096))) {
            $line = chop($line);
            if (null !== $this->out) {
                $this->out->writeLine($line);
            }
        }

        $returnCode = pclose($pd);
        if (0 != $returnCode) {
            throw new stubRuntimeException('Executing command ' . $command . ' failed: #' . $returnCode);
        }

        return $this;
    }

    /**
     * executes given command asynchronous
     *
     * The method starts the command, and returns an input stream which can be
     * used to read the output of the command manually.
     *
     * @param   string           $command
     * @return  stubInputStream
     * @throws  stubRuntimeException
     */
    public function executeAsync($command)
    {
        $pd = popen($command . ' ' . $this->redirect, 'r');
        if (false === $pd) {
            throw new stubRuntimeException('Can not execute ' . $command);
        }

        return new stubCommandInputStream($pd, $command);
    }

    /**
     * executes command directly and returns output as array (each line as one entry)
     *
     * @param   string         $command
     * @return  array<string>
     * @throws  stubRuntimeException
     */
    public function executeDirect($command)
    {
        $pd = popen($command . ' ' . $this->redirect, 'r');
        if (false === $pd) {
            throw new stubRuntimeException('Can not execute ' . $command);
        }

        $result = array();
        while (feof($pd) === false && false !== ($line = fgets($pd, 4096))) {
            $result[] = chop($line);
        }

        $returnCode = pclose($pd);
        if (0 != $returnCode) {
            throw new stubRuntimeException('Executing command ' . $command . ' failed: #' . $returnCode);
        }

        return $result;
    }
}
?>