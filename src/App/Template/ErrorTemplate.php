<?php

namespace App\Template;

use App\Template\AbstractTemplate;
use Charcoal\App\Handler\AbstractError;
use Charcoal\App\Handler\HandlerAwareTrait;

/**
 * Template Controller: Error Handler
 */
class ErrorTemplate extends AbstractTemplate
{
    use HandlerAwareTrait;

    /**
     * Retrieve the application debug mode.
     *
     * @return boolean
     */
    public function getDebug()
    {
        if ($this->appHandler() instanceof AbstractError) {
            return parent::debug();
        }

        return false;
    }

    /**
     * Get the error code.
     *
     * @return integer
     */
    public function getErrorCode()
    {
        return $this->appHandler()->getCode();
    }

    /**
     * Get the error message.
     *
     * @return string
     */
    public function getErrorMessage()
    {
        return $this->appHandler()->getMessage();
    }

    /**
     * Get the error title.
     *
     * @return string
     */
    public function getErrorTitle()
    {
        return $this->appHandler()->getSummary();
    }

    /**
     * Get the error details as HTML.
     *
     * @return string|null
     */
    final public function getHtmlErrorDetails()
    {
        if ($this->debug()) {
            return $this->appHandler()->renderHtmlErrorDetails();
        } else {
            return null;
        }
    }
}
