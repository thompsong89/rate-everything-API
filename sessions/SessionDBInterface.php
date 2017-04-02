<?php
/**
 * Defines the interface that must be implemented by the
 * Database class that implements the session handling.
 */
interface SessionDBInterface {
     
  /**
   * Read the data corresponding to the $sessionId from the session database
   *
   * @param string $sessionId Id of the session to which data must be read
   * @return string|boolean
   */
  public function readSessionData($sessionId);

  /**
   * Writes the given session data to the session database.
   *
   * @param string $sessionId
   * @param string $sessionData
   */
  public function writeSessionData($sessionId, $sessionData, $accessTime);

  /**
   * Deletes the session data for the given sessionId from the database.
   *
   * @param string $sessionId
   * @return boolean
   */
  public function deleteSessionData($sessionId);

  /**
   * Check if a session for the current $sessionId exists.
   *
   * @param string $sessionId
   * @return boolean
   */
  public function checkSession($sessionId);
}
?>
