<?php
use \PHPYAM\core\Controller as Controller;
use \PHPYAM\core\Core as Core;
use \PHPYAM\libs\IntelliForm as IntelliForm;
use \PHPYAM\libs\Assert as Assert;

/**
 * Class Form2
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 * Also use "require" and not "require_once" to insert templates, or PHP
 * we will not be able to insert a second time templates used before
 * redirection (on error, for example).
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

        // load views.
        $_pageTitle = 'DEMO FORM 2';
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/form2/index.php';
        require __DIR__ . '/../views/__templates/footer.php';
    }

    public function ajaxValidate()
    {
        $formValues = array();
        $this->checkFormValues($formValues);
    }

    public function confirm()
    {
        $formValues = array();
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted.');
        Assert::isTrue($this->checkFormValues($formValues), 'The form data is invalid.');

        $_htmlFormValues = $formValues;
        Core::htmlize($_htmlFormValues);

        // load views.
        $_pageTitle = 'CONFIRMATION OF DATA ENTRY';
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/form2/confirm.php';
        require __DIR__ . '/../views/__templates/footer.php';
    }

    public function create()
    {
        $formValues = array();
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted.');
        Assert::isTrue($this->checkFormValues($formValues) && $this->processForm($formValues), 'The form data has not been processed correctly.');

        // Back to homepage...
        $this->getRouter()->forward(DEFAULT_CONTROLLER, DEFAULT_ACTION);
    }
}
