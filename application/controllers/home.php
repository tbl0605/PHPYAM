<?php
use \PHPYAM\core\Controller as Controller;

/**
 * Class Home
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 * Also use "require" and not "require_once" to insert templates, or PHP
 * we will not be able to insert a second time templates used before
 * redirection (on error, for example).
 */
class Home extends Controller
{

    // This controller does not need to access the database(s),
    // so don't open or close the database connection(s).
    protected function openDatabaseConnections()
    {
    }

    // This controller does not need to access the database(s),
    // so don't open or close the database connection(s).
    protected function closeDatabaseConnections()
    {
    }

    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/
     */
    public function index(array $infos)
    {
        $this->getRouter()->forward('form2', 'index', $infos);
    }
}
