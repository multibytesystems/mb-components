<?php
namespace MBComponents\Validator;

use Zend\Validator\AbstractValidator;

/**
 *
 * @author omanshardt
 *
 */
class DateRange extends AbstractValidator
{

    const ERROR_DATE_NOT_SAME = 1;
    const ERROR_DATE_NOT_AFTER = 1;
    const ERROR_DATE_NOT_BEFORE = 1;
    const ERROR_DATE_NOT_BETWEEN = 1;

    const NOT_SAME = 'notSame';
    const NOT_AFTER = 'notAfter';
    const NOT_BEFORE = 'notBefore';
    const NOT_BETWEEN = 'notBetween';

    protected $rangeStart;
    protected $rangeEnd;
    protected $date;
    protected $type;

    public function __construct($options = null)
    {
        if (isset($options['rangeStart'])) {
            $this->setRangeStart($options['rangeStart']);
        }
        if (isset($options['rangeEnd'])) {
            $this->setRangeEnd($options['rangeEnd']);
        }
        if (isset($options['date'])) {
            $this->setDate($options['date']);
        }
        if (isset($options['type'])) {
            $this->setType($options['type']);
        }
        parent::__construct();
    }

    protected $messageTemplates = array(
        self::ERROR_DATE_NOT_SAME => 'error_date_not_same',
        self::ERROR_DATE_NOT_AFTER => 'error_date_not_after',
        self::ERROR_DATE_NOT_BEFORE => 'error_date_not_before',
        self::ERROR_DATE_NOT_BETWEEN => 'error_date_not_between'
    );

    /**
     * Get rangeStart
     *
     * @return string
     */
    public function getRangeStart()
    {
        return $this->rangeStart;
    }

    /**
     * Set rangeStart
     *
     * @param string $rangeStart
     */
    public function setRangeStart($rangeStart)
    {
        $this->rangeStart = $rangeStart;
    }

    /**
     * Get rangeEnd
     *
     * @return string
     */
    public function getRangeEnd()
    {
        return $this->rangeEnd;
    }

    /**
     * Set rangeEnd
     *
     * @param string $rangeEnd
     */
    public function setRangeEnd($rangeEnd)
    {
        $this->rangeEnd = $rangeEnd;
    }

    /**
     * Get date
     *
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param string $date
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set type
     *
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Zend\Validator\ValidatorInterface::isValid()
     *
     */
    public function isValid($value)
    {
        $val = new \DateTime($value);
        switch ($this->getType()) {
            case self::NOT_SAME:
                $exactDate = new \DateTime($this->getDate());
                if ($val != $exactDate) {
                    $this->error(self::ERROR_DATE_NOT_SAME, $value);
                    return false;
                }
                break;
            case self::NOT_AFTER:
                $rangeEnd = new \DateTime($this->getRangeEnd());
                if ($val > $rangeEnd) {
                    $this->error(self::ERROR_DATE_NOT_AFTER, $value);
                    return false;
                }
                break;
            case self::NOT_BEFORE:
                $rangeStart = new \DateTime($this->getRangeStart());
                if ($val < $rangeStart) {
                    $this->error(self::ERROR_DATE_NOT_BEFORE, $value);
                    return false;
                }
                break;
            case self::NOT_BETWEEN:
                $rangeStart = new \DateTime($this->getRangeStart());
                $rangeEnd = new \DateTime($this->getRangeEnd());
                if ($val < $rangeStart || $val > $rangeEnd) {
                    $this->error(self::ERROR_DATE_NOT_BETWEEN, $value);
                    return false;
                }
                break;
        }
        return true;
    }
}

?>