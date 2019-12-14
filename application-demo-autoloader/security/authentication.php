<?php
namespace PHPYAM\demo\application\security;

use PHPYAM\core\interfaces\IAuthentication as IAuthentication;

/**
 * Implementation of interface IAuthentication to manage:
 * - database connections
 * - user identification
 * - user habilitations
 * - properties depending on above informations
 *
 * @author Thierry BLIND
 */
class Authentication implements IAuthentication
{

    const ANONYMOUS = 'anonymous';

    const ANONYMOUS_LOCATION = null;

    const DEFAULT_LOCATION = 'west';

    private $locationId = null;

    private $userId = null;

    /**
     * Returns the user location
     *
     * @param string $userId
     * @return string|null user location or null when not found
     */
    private function getLocationIdFor($userId)
    {
        if (isset($_SESSION[$userId]) && isset($_SESSION[$userId]['userLocation'])) {
            return $_SESSION[$userId]['userLocation'];
        }

        // TODO: retrieve REAL infos about the user trying to access this web application.
        // For our demo ALL users will be handled as anonymous users.
        $_SESSION[$userId] = array(
            'userLocation' => self::DEFAULT_LOCATION
        );
        return self::DEFAULT_LOCATION;
    }

    /**
     * Returns the user location.
     *
     * @return string|null user location, null if not available
     */
    public function getLocationId()
    {
        return isset(\PHPYAM\demo\confs\AppConfig::$locations[$this->locationId]) ? $this->locationId : null;
    }

    /**
     * Returns location infos where the user belongs.
     *
     * @return array|null location infos where the user belongs, null if not available
     */
    public function getLocationInfos()
    {
        return isset(\PHPYAM\demo\confs\AppConfig::$locations[$this->locationId]) ? \PHPYAM\demo\confs\AppConfig::$locations[$this->locationId] : null;
    }

    /**
     * Returns the user ID.
     * Only available when user was authenticated and authorized through function
     * {@link Authentication::authenticate()}).
     *
     * @return string|null user ID, null if nonexistent or unauthorized
     */
    public function getIdUtilisateur()
    {
        return $this->userId;
    }

    private function getNTLMUser()
    {
        return isset($_SERVER['REMOTE_USER']) ? mb_strtolower(trim(substr($_SERVER['REMOTE_USER'], strrpos($_SERVER['REMOTE_USER'], "\\") + 1))) : self::ANONYMOUS;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \PHPYAM\core\interfaces\IAuthentication::authenticate($controllerName, $actionName, $parameters)
     */
    public function authenticate(&$controllerName, &$actionName, array &$parameters)
    {
        $this->userId = $this->getNTLMUser();
        $this->locationId = $this->getLocationIdFor($this->userId);
        if ($this->locationId === self::ANONYMOUS_LOCATION) {
            unset($_SESSION[$this->userId]);
            $this->userId = null;
            return false;
        }
        if (! array_key_exists($this->locationId, \PHPYAM\demo\confs\AppConfig::$locations)) {
            if (constant('USE_LOG4PHP')) {
                \Logger::getLogger(__CLASS__)->debug('User: ' . $this->userId . ', undefined location: ' . $this->locationId);
            }
            $this->locationId = null;
            unset($_SESSION[$this->userId]);
            $this->userId = null;
            return false;
        }

        return true;
    }

    public function newDatabaseConnections()
    {
        return null;
    }

    public function destroyDatabaseConnections($db)
    {
    }
}
