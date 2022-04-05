<?php
use PHPYAM\core\Controller;
use PHPYAM\core\Core;
use PHPYAM\libs\Assert;
use PHPYAM\libs\IntelliForm;
use PHPYAM\libs\Store;

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
     * @var \ModeleForm2
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

        // load views.
        $_pageTitle = 'DEMO FORM 2';
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/form2/index.php';
        require __DIR__ . '/../views/__templates/footer.php';
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

        // load views.
        $_pageTitle = 'CONFIRMATION OF DATA ENTRY';
        // Form values will be sent as "GET" values
        $_urlCreateAction = Core::url('form2', 'create', $formValues);
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/form2/confirm.php';
        require __DIR__ . '/../views/__templates/footer.php';
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
        $this->getRouter()->forward(Store::getRequired('DEFAULT_CONTROLLER'), Store::getRequired('DEFAULT_ACTION'));
    }
}
