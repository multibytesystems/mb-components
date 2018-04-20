<?php
namespace MBComponents\Validator;

use Zend\Validator\AbstractValidator;

/**
 *
 * @author omanshardt
 *
 */
class Dependency extends AbstractValidator
{
    const ERROR_DEPENDENCY = 1;

    protected $value;
    protected $validator;


    public function __construct($options = null)
    {
        if (isset($options['validator']) && isset($options['validator']['name'])) {
            $this->setValidator(new $options['validator']['name']());
            if (isset($options['validator']['options'])) {
                $this->getValidator()->setOptions($options['validator']['options']);
            }
        }
        if (isset($options['value'])) {
            $this->setValue($options['value']);
        }
        parent::__construct();
    }

    protected $messageTemplates = array(
        self::ERROR_DEPENDENCY => 'error_dependency',
    );

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get validator
     *
     * @return string
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * Set validator
     *
     * @param string $validator
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
     *
     */
    public function isValid($value)
    {
        if($this->getValidator() && $this->getValue()) {
            $ret = $this->getValidator()->isValid($this->getValue());
            if ($ret === true) {
                return true;
            }
            else {
                $this->error(self::ERROR_DEPENDENCY, $value);
                return false;
            }
        }
        return true;
    }
}

?>