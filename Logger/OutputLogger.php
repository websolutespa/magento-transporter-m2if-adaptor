<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterM2ifAdaptor\Logger;

use Monolog\Logger;
use Symfony\Component\Console\Formatter\OutputFormatterInterface;
use Symfony\Component\Console\Output\Output;

class OutputLogger extends Output
{
    /**
     * @var Logger
     */
    private $logger;

    /**
     * @param Logger $logger
     * @param int|null $verbosity
     * @param bool $decorated
     * @param OutputFormatterInterface|null $formatter
     */
    public function __construct(
        Logger $logger,
        ?int $verbosity = self::VERBOSITY_NORMAL,
        bool $decorated = false,
        OutputFormatterInterface $formatter = null
    ) {
        parent::__construct($verbosity, $decorated, $formatter);
        $this->logger = $logger;
    }

    /**
     * @param string $message
     * @param bool $newline
     */
    protected function doWrite($message, $newline)
    {
        if ($message == '' || $message == "\n") {
            return;
        }
        $this->logger->info($message);
    }
}
