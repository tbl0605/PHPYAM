<?php
namespace PHPYAM\demo\application\models;

/**
 * GOOD PRACTICE FOR MODELS:
 *
 * A model is responsible for reading and writing data ("data persistence").
 * A model must:
 * - be as generic as possible to eventually be reused in other projects (batch or Web)
 *   For the most successful models, they could very well be a common basis (as libraries) to access databases.
 * - be testable as "standalone", without use of controllers or views
 * A model MUST NOT:
 * - manage the database connections ("ready to use" connection objects must be transmitted to the model's constructor)
 * - contain functions like echo, print, etc... to display web data, it is the duty of the views!
 * - directly access to the context of the web application (variables $_GET, $_POST, $_SESSION, $GLOBALS, etc...),
 *   because the calling controller is responsible for that!
 */
?>
<?php

/**
 * Model used to retrieve/store data for the form "Form1".
 *
 * @author Thierry BLIND
 */
class ModeleForm1
{

    private $db = null;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function foo()
    {
    }
}
?>