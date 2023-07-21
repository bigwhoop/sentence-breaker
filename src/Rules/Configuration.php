<?php

declare(strict_types=1);

namespace Bigwhoop\SentenceBreaker\Rules;

interface Configuration
{
    /**
     * @throws ConfigurationException
     */
    public function getRules(): Rules;
}
