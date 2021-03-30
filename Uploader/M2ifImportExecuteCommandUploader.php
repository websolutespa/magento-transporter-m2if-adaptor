<?php
/*
 * Copyright © Websolute spa. All rights reserved.
 * See LICENSE and/or COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Websolute\TransporterM2ifAdaptor\Uploader;

use Exception;
use Magento\Framework\Exception\LocalizedException;
use Websolute\M2ifWrapper\Model\Wrapper;
use Websolute\TransporterBase\Api\UploaderInterface;
use Websolute\TransporterBase\Exception\TransporterException;
use Websolute\TransporterEntity\Api\EntityRepositoryInterface;
use Websolute\TransporterM2ifAdaptor\Logger\OutputLogger;

class M2ifImportExecuteCommandUploader implements UploaderInterface
{
    /**
     * @var Wrapper
     */
    private $wrapper;

    /**
     * @var OutputLogger
     */
    private $outputLogger;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var EntityRepositoryInterface
     */
    private $entityRepository;

    /**
     * @param Wrapper $wrapper
     * @param OutputLogger $outputLogger
     * @param EntityRepositoryInterface $entityRepository
     * @param array $parameters
     */
    public function __construct(
        Wrapper $wrapper,
        OutputLogger $outputLogger,
        EntityRepositoryInterface $entityRepository,
        array $parameters = []
    ) {
        $this->wrapper = $wrapper;
        $this->outputLogger = $outputLogger;
        $this->parameters = $parameters;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @param int $activityId
     * @param string $uploaderType
     * @throws TransporterException
     */
    public function execute(int $activityId, string $uploaderType): void
    {
        $allActivityEntities = $this->entityRepository->getAllDataManipulatedByActivityIdGroupedByIdentifier($activityId);

        if (count($allActivityEntities) === 0) {
            $this->outputLogger->write(__(
                'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ END ~ No ActivityEntities found',
                $activityId,
                $uploaderType
            ));
            return;
        }

        try {
            $this->parameters[] = '--source-dir=var/importexport/' . $activityId . '/';
            $exitCode = $this->wrapper->execute($this->parameters, $this->outputLogger);
            if ($exitCode > 0) {
                throw new LocalizedException(__(
                    'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ ERROR ~ M2if command error returned',
                    $activityId,
                    $uploaderType
                ));
            }
        } catch (Exception $e) {
            throw new TransporterException(__(
                'activityId:%1 ~ Uploader ~ uploaderType:%2 ~ ERROR ~ error:%3',
                $activityId,
                $uploaderType,
                $e->getMessage()
            ));
        }
    }
}
