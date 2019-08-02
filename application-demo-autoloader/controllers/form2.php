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
use PHPYAM\core\Core;

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

    private function checkFormValues(array $formValues)
    {
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
                // server-side form data check, in addition to client-side validation which
                // is never trustworthy
                if ($this->checkFormValues($_POST)) {
                    $goToConfirm = true;
                    // delete the form data because we have finished with it
                    IntelliForm::clear($formKey);
                } else {
                    // form not submitted, restore a previous form
                    IntelliForm::restore($formKey, true);
                }
            } catch (\Exception $ex) {
                IntelliForm::restore($formKey, true);
            }
        }

        $formValues = $_POST;
        // Remove hidden IntelliForm key from our available form values
        unset($formValues[IntelliForm::ANTZ_KEY]);

        $_logs = ob_get_contents();
        ob_end_clean();

        if ($goToConfirm) {
            // Same as: $this->confirm($formValues);
            $this->getRouter()->call('form2', 'confirm', $formValues);
            return;
        }

        //Core::htmlize($_logs);

        Header::render([
            'pageTitle' => 'DEMO FORM 2'
        ]);
        Index::render([
            'logs' => $_logs,
            'formValues' => $formValues // Not used yet
        ]);
        Footer::render([]);
    }

    public function ajaxValidate(/* $formValues */)
    {
        // NB: we decided to send Ajax data using a POST request, so there's no need
        // to look for GET values from parameter $formValues
        if (! $this->checkFormValues($_POST)) {
            echo '<h1 class="error">A server-side validation error occured !</h1>';
        }
    }

    public function confirm($formValues)
    {
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted. Page was probably reloaded.');
        Assert::isTrue($this->checkFormValues($formValues), 'The form data is invalid.');

        Header::render([
            'pageTitle' => 'CONFIRMATION OF DATA ENTRY'
        ]);
        Confirm::render([
            'formValues' => $formValues,
            'urlCreateAction' => Core::url('form2', 'create', $formValues) // Form values will be sent as "GET" values
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
