<?php
namespace PHPYAM\demo\application\controllers;

use PHPYAM\core\Controller as Controller;

/**
 * Class Error
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 * Also use "require" and not "require_once" to insert templates, or PHP
 * we will not be able to insert a second time templates used before
 * redirection (on error, for example).
 */
class Error extends Controller
{

    protected function openDatabaseConnections()
    {
    }

    protected function closeDatabaseConnections()
    {
    }

    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/error/index
     */
    public function index($_listOfErrors = array())
    {
        $_pageTitle = 'ERROR';
        // load views.
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/__templates/error.php';
        require __DIR__ . '/../views/__templates/footer.php';
    }

    /**
     * PAGE: indexAjax
     * This method handles what happens when you move to http://yourproject/error/indexAjax
     */
    public function indexAjax($_listOfErrors = array())
    {
        // load view.
        require __DIR__ . '/../views/__templates/error.php';
    }
}
