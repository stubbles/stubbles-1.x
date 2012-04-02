<?php
/**
 * Class for operations on bsd-style sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 * @version     $Id: stubBSDSocket.php 2435 2010-01-04 22:10:32Z mikey $
 */
stubClassLoader::load('net::stubbles::lang::exceptions::stubIllegalArgumentException',
                      'net::stubbles::lang::exceptions::stubIllegalStateException',
                      'net::stubbles::peer::stubSocket',
                      'net::stubbles::peer::stubConnectionException'
);
/**
 * Class for operations on bsd-style sockets.
 *
 * @package     stubbles
 * @subpackage  peer
 */
class stubBSDSocket extends stubSocket
{
    /**
     * switch whether end of file was reached or not
     *
     * @var  bool
     */
    protected $eof            = true;
    /**
     * host to open socket to
     *
     * @var  string
     */
    protected $domain         = AF_INET;
    /**
     * port to use for opening the socket
     *
     * @var  int
     */
    protected $type           = SOCK_STREAM;
    /**
     * timeout
     *
     * @var  int
     */
    protected $protocol       = SOL_TCP;
    /**
     * list of options for the socket
     *
     * @var  array<int,array<int,mixed>>
     */
    protected $options        = array();
    /**
     * list of available domains
     *
     * @var  array<int,string>
     */
    protected static $domains = array(AF_INET  => 'AF_INET',
                                      AF_INET6 => 'AF_INET6',
                                      AF_UNIX  => 'AF_UNIX'
                                );
    /**
     * list of available socket types
     *
     * @var  array<int,string>
     */
    protected static $types   = array(SOCK_STREAM    => 'SOCK_STREAM',
                                      SOCK_DGRAM     => 'SOCK_DGRAM',
                                      SOCK_RAW       => 'SOCK_RAW',
                                      SOCK_SEQPACKET => 'SOCK_SEQPACKET',
                                      SOCK_RDM       => 'SOCK_RDM'
                                );

    /**
     * sets the domain
     *
     * @param   int            $domain  one of AF_INET, AF_INET6 or AF_UNIX
     * @return  stubBSDSocket
     * @throws  stubIllegalArgumentException
     * @throws  stubIllegalStateException
     */
    public function setDomain($domain)
    {
        if (in_array($domain, array_keys(self::$domains)) === false) {
            throw new stubIllegalArgumentException('Domain must be one of AF_INET, AF_INET6 or AF_UNIX.');
        }
        
        if ($this->isConnected() === true) {
            throw new stubIllegalStateException('Can not change domain on already connected socket.');
        }
        
        $this->domain = $domain;
        return $this;
    }

    /**
     * returns the domain
     *
     * @return  int
     */
    public function getDomain()
    {
        return $this->domain;
    }

    /**
     * sets the socket type
     *
     * @param   int            $type  one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM
     * @return  stubBSDSocket
     * @throws  stubIllegalArgumentException
     * @throws  stubIllegalStateException
     */
    public function setType($type)
    {
        if (in_array($type, array_keys(self::$types)) === false) {
            throw new stubIllegalArgumentException('Type must be one of SOCK_STREAM, SOCK_DGRAM, SOCK_RAW, SOCK_SEQPACKET or SOCK_RDM.');
        }
        
        if ($this->isConnected() === true) {
            throw new stubIllegalStateException('Can not change type on already connected socket.');
        }
        
        $this->type = $type;
        return $this;
    }

    /**
     * returns the socket type
     *
     * @return  int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * sets the protocol for the socket
     *
     * @param   int            $protocol  one of SOL_TCP or SOL_UDP
     * @return  stubBSDSocket
     * @throws  stubIllegalArgumentException
     * @throws  stubIllegalStateException
     */
    public function setProtocol($protocol)
    {
        if (0 !== $protocol && in_array($protocol, array(SOL_TCP, SOL_UDP)) === false) {
            throw new stubIllegalArgumentException('Protocol must be one of SOL_TCP or SOL_UDP.');
        }
        
        if ($this->isConnected() === true) {
            throw new stubIllegalStateException('Can not change protocol on already connected socket.');
        }
        
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * returns the protocol used by the socket
     *
     * @return  int
     */
    public function getProtocol()
    {
        return $this->protocol;
    }

    /**
     * sets an option
     *
     * @param   int            $level  protocol level of option
     * @param   int            $name   option name
     * @param   mixed          $value  option value
     * @return  stubBSDSocket
     * @throws  stubConnectionException
     */
    public function setOption($level, $name, $value)
    {
        if (isset($this->options[$level]) === false) {
            $this->options[$level] = array();
        }
        
        $this->options[$level][$name] = $value;
        if ($this->isConnected() === true) {
            if (socket_set_option($this->fp, $level, $name, $value) === false) {
                throw new stubConnectionException('Failed to set option ' . $name . ' on level ' . $level . ' to value ' . $value);
            }
        }

        return $this;
    }

    /**
     * returns an option
     *
     * @param   int    $level  protocol level of option
     * @param   int    $name   option name
     * @return  mixed
     * @throws  stubConnectionException
     */
    public function getOption($level, $name)
    {
        if ($this->isConnected() === true) {
            $option = socket_get_option($this->fp, $level, $name);
            if (false === $option) {
                throw new stubConnectionException('Failed to retrieve option ' . $name . ' on level ' . $level);
            }
            
            if (isset($this->options[$level]) === false) {
                $this->options[$level] = array();
            }
            
            $this->options[$level][$name] = $option;
        }
        
        if (isset($this->options[$level]) === true && isset($this->options[$level][$name]) === true) {
            return $this->options[$level][$name];
        }
        
        return null;
    }

    /**
     * opens a socket connection
     *
     * @return  bool    true if connect was successful
     * @throws  stubConnectionException
     */
    public function connect()
    {
        if ($this->isConnected() === true) {
            return true;
        }
        
        $this->fp = @socket_create($this->domain, $this->type, $this->protocol);
        if (false === $this->fp) {
            $this->fp = null;
            throw new stubConnectionException(sprintf('Create of %s socket (type %s, protocol %s) failed.',
                                                      self::$domains[$this->domain],
                                                      self::$types[$this->type],
                                                      getprotobynumber($this->protocol)
                                              )
                      );
        }
        
        foreach ($this->options as $level => $pairs) {
            foreach ($pairs as $name => $value) {
                socket_set_option($this->fp, $level, $name, $value);
            }
        }
      
        switch ($this->domain) {
            case AF_INET:
                $result = socket_connect($this->fp, gethostbyname($this->host), $this->port);
                break;
            
            case AF_UNIX:
                $result = socket_connect($this->fp, $this->host);
                break;
            
            default:
                throw new stubConnectionException('Connect to ' . $this->host . ':' .$this->port . ' failed: Illegal domain type ' . $this->domain . ' used.');
        }
        
        if (false === $result) {
            $errorMessage = $this->lastError();
            $this->fp     = null;
            throw new stubConnectionException('Connect to ' . $this->host . ':' .$this->port . ' failed: ' . $errorMessage);
        }
        
        $this->eof = false;
        return true;
    }

    /**
     * closes a connection
     *
     * @return  stubBSDSocket
     */
    public function disconnect()
    {
        if ($this->isConnected() === true) {
            socket_close($this->fp);
        }

        return $this;
    }

    /**
     * returns last error
     *
     * @return  string
     */
    public function lastError()
    {
        $e = socket_last_error($this->fp);
        return $e . ': ' . socket_strerror($e);
    }

    /**
     * read from socket
     *
     * @param   int     $length  optional  length of data to read
     * @return  string
     * @throws  stubIllegalStateException
     */
    public function read($length = 4096)
    {
        if ($this->isConnected() == false) {
            throw new stubIllegalStateException('Can not read on unconnected socket.');
        }
        
        return $this->doRead($length, PHP_NORMAL_READ);
    }

    /**
     * read a whole line from socket
     *
     * @param   int     $length  optional  length of data to read
     * @return  string
     */
    public function readLine($length = 4096)
    {
        return rtrim($this->read($length));
    }

    /**
     * read binary data from socket
     *
     * @param   int     $length  optional  length of data to read
     * @return  string
     * @throws  stubIllegalStateException
     */
    public function readBinary($length = 1024)
    {
        if ($this->isConnected() == false) {
            throw new stubIllegalStateException('Can not read on unconnected socket.');
        }
        
        return $this->doRead($length, PHP_BINARY_READ);
    }

    /**
     * write data to socket and returns the amount of written bytes
     *
     * @param   string  $data  data to write
     * @return  int
     * @throws  stubConnectionException
     * @throws  stubIllegalStateException
     */
    public function write($data)
    {
        if ($this->isConnected() == false) {
            throw new stubIllegalStateException('Can not write on unconnected socket.');
        }
        
        $length = socket_write($this->fp, $data, strlen($data));
        if (false === $length) {
            throw new stubConnectionException('"Writing of ' . strlen($data) . ' bytes failed.');
        }

        return $length;
    }

    /**
     * helper method to do the actual reading
     *
     * @param   int    $length  length of data to read
     * @param   int    $type    one of PHP_BINARY_READ or PHP_NORMAL_READ
     * @return  string
     * @throws  stubConnectionException
     */
    protected function doRead($length, $type)
    {
        $result = socket_read($this->fp, $length, $type);
        if (false === $result) {
            throw new stubConnectionException('Read failed: ' . $this->lastError());
        }
        
        if (empty($result) === true) {
            $this->eof = true;
            $result = null;
        }
        
        return $result;
    }

    /**
     * check if we reached end of data
     *
     * @return  bool
     */
    public function eof()
    {
        return $this->eof;
    }
}
?>