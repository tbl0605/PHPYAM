<?php
namespace PHPYAM\core;

/**
 * This is the "base controller class".
 * All other "real" controllers extend this class.
 *
 * @package PHPYAM.core
 * @author Thierry BLIND
 * @version 1.0.0
 * @since 01/01/2014
 * @copyright 2014 Thierry BLIND
 */
abstract class Controller
{

    /**
     *
     * @var mixed database connection(s)
     */
    private $databaseConnections = null;

    /**
     *
     * @var \PHPYAM\core\interfaces\IRouter Routeur
     */
    private $router = null;

    /**
     *
     * @return \PHPYAM\core\interfaces\IRouter router attached to this controller
     */
    public final function getRouter()
    {
        return $this->router;
    }

    /**
     * Whenever a controller is created, also open the database connection(s).
     * The idea behind is to have connections
     * that can be used by multiple models (there are frameworks that open one connection per model).
     *
     * @param \PHPYAM\core\interfaces\IRouter $router
     *            routeur
     */
    public final function __construct(\PHPYAM\core\interfaces\IRouter $router)
    {
        $this->router = $router;
        $this->openDatabaseConnections();
        $this->init();
    }

    /**
     * For developers, to add custom controller initialization.
     */
    protected function init()
    {
    }

    /**
     * Open the database connections with the credentials from {SYS_APP}/security/{SECURITY_POLICY}.php
     */
    protected function openDatabaseConnections()
    {
        $this->databaseConnections = $this->getRouter()
            ->getAuthentication()
            ->newDatabaseConnections();
    }

    /**
     * Close the database connections with the credentials from {SYS_APP}/security/{SECURITY_POLICY}.php
     */
    protected function closeDatabaseConnections()
    {
        $this->getRouter()
            ->getAuthentication()
            ->destroyDatabaseConnections($this->databaseConnections);
    }

    /**
     * Load the model with the given name ($modelName) and the database connection(s) from $this->databaseConnections.
     * $this->loadModel("SongModel") would include {SYS_APP}/models/songmodel.php and create the object in the controller, like this:
     * <code>$songsModel = $this->loadModel('SongsModel');</code>
     *
     * Note that $modelName is case-insensitive (e.g. SongModel) but the model's filename should be written
     * in lowercase letters (e.g. models/songmodel.php). This behavior can be redefined
     * in {@link \PHPYAM\core\interfaces\IRouter::loadResource($pathName, $resourceName)}.
     *
     * @param string $modelName
     *            The model name (case-insensitive)
     * @return object model
     * @throws \PHPYAM\libs\AssertException exception thrown on invalid resource $modelName
     */
    protected function loadModel($modelName)
    {
        $modelClassName = $this->getRouter()->loadResource('models', $modelName, true);
        return new $modelClassName($this->databaseConnections);
    }

    /**
     * Close database connections
     */
    public final function __destruct()
    {
        $this->closeDatabaseConnections();
    }
}
