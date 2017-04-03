<?php
include_once( __dir__ . '/SessionDBInterface.php');
include_once( __dir__ . '/MysqlSessionDB.php');
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, 'sessions');
if(mysqli_connect_errno() !== 0){
	throw new Exception("DB connection error : ".mysqli_connect_error());
}
$sessiondb = new MysqlSessionDB($mysqli);
/**
 * This class implements the session storage mechanism.
 * @implement SessionHandlerInterface
 */
class MySessionHandler implements SessionHandlerInterface {
        
  /**
   * The accessTime of the session
   */
  public $accessTime;
  
  /**
   * Holds the instance that handles the sessiondb functionality.
   */
  private $sessiondb;
  
  /**
   * Assings the database Connection.
   * Start the session.
   *
   * @param $sessiondb instance of the session database access class
   */
  public function __construct(SessionDBInterface $sessiondb) {
    $this->sessiondb = $sessiondb;
    $this->accessTime = time();
  }
   
  /**
   * Executed when the session is started automatically, or
   * manually with session_start();
   *
   * @param string $savePath
   * @param string $sessionId
   * @return boolean
   */
  public function open($savePath, $sessionName) {
    $savePath = '';
    $sessionName = '';
    return true;
  }

  /**
   * Reads the session data if one exists for the given sessionId,
   * else returns a empty data.
   *
   * @param string $sessionId - The session id to read.
   * @return string
   */
  public function read($sessionId) {
    $data = $this->sessiondb->readSessionData($sessionId);
    if ($data !== false) {
      return $data;
    }
    return '';
  }

  /**
   * Used to save the session and close.
   * close() is called after this function executes.
   *
   * @param string $sessionId Id of the current session
   * @param string $sessionData serialized session data
   */
  public function write($sessionId, $sessionData) {
	$this->accessTime = time();
    $this->sessiondb->writeSessionData($sessionId, $sessionData,$this->accessTime);
  }

  /**
   * The garbage collector callback is invoked internally by PHP periodically
   * in order to purge old session data. The frequency is controlled by
   * session.gc_probability and session.gc_divisor. The value of lifetime
   * which is passed to this callback can be set in session.gc_maxlifetime.
   * Return value should be TRUE for success, FALSE
   *
   * @param string $maxLifeTime
   * @return boolean
   */
  public function gc($maxLifeTime) {
    $maxLifeTime = '';
    return true;
  }

  /**
   * Closes the session.
   * Called after the write method is called.
   * @return boolean
   */
  public function close() {
    $this->sessiondb->close();
    return true;
  }

  /**
   * Removes all the data corresponding to the $sessionId.
   *
   * @param string $sessionId
   * @return boolean
   */
  public function destroy($sessionId) {
    if ($this->sessiondb->deleteSessionData($sessionId) !== false) {
      return true;
    }
    return false;
  }
}
?>
