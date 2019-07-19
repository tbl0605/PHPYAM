<?php
use \PHPYAM\core\Controller as Controller;
use \PHPYAM\libs\IntelliForm as IntelliForm;
use \PHPYAM\libs\Assert as Assert;

/**
 * Class Form1
 *
 * Please note:
 * Don't use the same name for class and method, as this might trigger an (unintended) __construct of the class.
 * This is really weird behaviour, but documented here: http://php.net/manual/en/language.oop5.decon.php
 *
 * Also use "require" and not "require_once" to insert templates, or PHP
 * we will not be able to insert a second time templates used before
 * redirection (on error, for example).
 */
class Form1 extends Controller
{

    /**
     *
     * @var \ModeleForm1
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

    private function checkFormValues()
    {
        return true;
    }

    private function processForm()
    {
        /*
         * DEVELOPERS CAN PROCESS THE FORM HERE...
         */
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
            if ($this->checkFormValues()) {
                $goToCreate = true;
            }
        }

        $_logs = ob_get_contents();
        ob_end_clean();

        if ($goToCreate) {
            $this->getRouter()->call('form1', 'create');
            return;
        }

        //Core::htmlize($_logs);

        // load views.
        $_pageTitle = 'DEMO FORM 1';
        require __DIR__ . '/../views/__templates/header.php';
        require __DIR__ . '/../views/form1/index.php';
        require __DIR__ . '/../views/__templates/footer.php';
    }

    public function create()
    {
        Assert::isTrue(IntelliForm::submitted(true), 'The form was not submitted.');
        Assert::isTrue($this->checkFormValues() && $this->processForm(), 'The form data has not been processed correctly.');

        // Back to homepage...
        $this->getRouter()->forward(DEFAULT_CONTROLLER, DEFAULT_ACTION);
    }
}
