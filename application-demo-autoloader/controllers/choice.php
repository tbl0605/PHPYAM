<?php
namespace PHPYAM\demo\application\controllers;

use PHPYAM\core\Controller;
use PHPYAM\demo\application\views\__templates\Footer;
use PHPYAM\demo\application\views\__templates\Header;
use PHPYAM\demo\application\views\choice\Index;

/**
 * Class Choice
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 */
class Choice extends Controller
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
        Header::render([
            'pageTitle' => 'DEMOS PHPYAM'
        ]);
        Index::render([]);
        Footer::render([]);
    }
}
