<?php
namespace PHPYAM\demo\application\controllers;

use PHPYAM\core\Controller as Controller;
use PHPYAM\libs\IntelliForm as IntelliForm;
use PHPYAM\libs\Assert as Assert;
use PHPYAM\demo\application\models\ModeleForm1;
use PHPYAM\demo\application\views\__templates\Header;
use PHPYAM\demo\application\views\form1\Index;
use PHPYAM\demo\application\views\__templates\Footer;

/**
 * Class Form1
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 */
class Form1 extends Controller
{

    /**
     *
     * @var ModeleForm1
     */
    private $myModel;

    /*
     * (non-PHPdoc)
     * @see \PHPYAM\core\Controller::init()
     */
    protected function init()
    {
        $this->myModel = $this->loadModel('ModeleForm1');
    }

    private function checkFormValues(array $formValues)
    {
        return true;
    }

    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/form1/index
     */
    public function index()
    {
        ob_start();

        $goToCreate = false;

        if (IntelliForm::submitted(false)) {
            $formValues = $_POST;
            if ($this->checkFormValues($formValues)) {
                $goToCreate = true;
            }
        }

        $_logs = ob_get_contents();
        ob_end_clean();

        if ($goToCreate) {
            // Remove hidden IntelliForm key from our available form values
            unset($formValues[IntelliForm::ANTZ_KEY]);
            // Same as: $this->create($formValues);
            $this->getRouter()->call('form1', 'create', $formValues);
            return;
        }

        //Core::htmlize($_logs);

        Header::render([
            'pageTitle' => 'DEMO FORM 1'
        ]);
        Index::render([
            'logs' => $_logs
        ]);
        Footer::render([]);
    }

    public function create($formValues)
    {
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted. Page was probably reloaded.');
        // NB: the router analyzed the request URL and stored all GET values in $formValues.
        // At this point, we have only one POST value, i.e. $_POST[IntelliForm::ANTZ_KEY]
        Assert::isTrue($this->checkFormValues($formValues), 'The form data is invalid.');

        /*
         * DEVELOPERS CAN PROCESS THE FORM VALUES HERE...
         */

        // Back to homepage...
        $this->getRouter()->forward(DEFAULT_CONTROLLER, DEFAULT_ACTION);
    }
}
