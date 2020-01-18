<?php
namespace PHPYAM\demo\application\controllers;

use PHPYAM\core\Controller;
use PHPYAM\demo\application\views\__templates\Error as ErrorView;
use PHPYAM\demo\application\views\__templates\Footer;
use PHPYAM\demo\application\views\__templates\Header;

/**
 * Class Error
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
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
        Header::render([
            'pageTitle' => 'ERROR'
        ]);
        ErrorView::render([
            'listOfErrors' => $_listOfErrors
        ]);
        Footer::render([]);
    }

    /**
     * PAGE: indexAjax
     * This method handles what happens when you move to http://yourproject/error/indexAjax
     */
    public function indexAjax($_listOfErrors = array())
    {
        ErrorView::render([
            'listOfErrors' => $_listOfErrors
        ]);
    }
}
