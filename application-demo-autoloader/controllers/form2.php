<?php
namespace PHPYAM\demo\application\controllers;

use PHPYAM\core\Controller as Controller;
use PHPYAM\libs\IntelliForm as IntelliForm;
use PHPYAM\libs\Assert as Assert;
use PHPYAM\demo\application\models\ModeleForm2;
use PHPYAM\demo\application\views\__templates\Header;
use PHPYAM\demo\application\views\form2\Index;
use PHPYAM\demo\application\views\__templates\Footer;
use PHPYAM\demo\application\views\form2\Confirm;

/**
 * Class Form2
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 */
class Form2 extends Controller
{

    /**
     *
     * @var ModeleForm2
     */
    private $myModel;

    /*
     * (non-PHPdoc)
     * @see \PHPYAM\core\Controller::init()
     */
    protected function init()
    {
        $this->myModel = $this->loadModel('ModeleForm2');
    }

    private function checkFormValues(array &$formValues)
    {
        return true;
    }

    private function processForm(array &$formValues)
    {
        /*
         * DEVELOPERS CAN PROCESS THE FORM HERE...
         */
        return true;
    }

    /**
     * PAGE: index
     * This method handles what happens when you move to http://yourproject/form2/index
     */
    public function index()
    {
        ob_start();

        $goToConfirm = false;
        $formKey = __CLASS__ . '::index';

        if (IntelliForm::submitted(false)) {
            // form has been submitted
            // save the data in case they navigate away then come back to the page
            IntelliForm::save($formKey);
            try {
                $formValues = array();
                if ($this->checkFormValues($formValues)) {
                    $goToConfirm = true;
                    // delete the form data because we have finished with it
                    IntelliForm::clear($formKey);
                } else {
                    // form not submitted, restore a previous form
                    IntelliForm::restore($formKey);
                }
            } catch (\Exception $ex) {
                IntelliForm::restore($formKey);
            }
        }

        $_logs = ob_get_contents();
        ob_end_clean();

        if ($goToConfirm) {
            $this->getRouter()->call('form2', 'confirm');
            return;
        }

        //Core::htmlize($_logs);

        Header::render([
            'pageTitle' => 'DEMO FORM 2'
        ]);
        Index::render([
            'logs' => $_logs
        ]);
        Footer::render([]);
    }

    public function ajaxValidate()
    {
        $formValues = array();
        $this->checkFormValues($formValues);
    }

    public function confirm()
    {
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted.');
        $formValues = $_POST;
        Assert::isTrue($this->checkFormValues($formValues), 'The form data is invalid.');

        Header::render([
            'pageTitle' => 'CONFIRMATION OF DATA ENTRY'
        ]);
        Confirm::render([
            'formValues' => $formValues
        ]);
        Footer::render([]);
    }

    public function create()
    {
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted.');
        $formValues = array();
        Assert::isTrue($this->checkFormValues($formValues) && $this->processForm($formValues), 'The form data has not been processed correctly.');

        // Back to homepage...
        $this->getRouter()->forward(DEFAULT_CONTROLLER, DEFAULT_ACTION);
    }
}
