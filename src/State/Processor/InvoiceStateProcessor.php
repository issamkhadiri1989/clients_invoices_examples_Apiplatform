<?php

declare(strict_types=1);

namespace App\State\Processor;

use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Invoice;
use App\Enum\InvoiceStatus;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class InvoiceStateProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly ProcessorInterface $processor,
    ) {
    }

    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = [])
    {
        if ($data instanceof Invoice) {
            $data->setInvoiceDate(new \DateTime());
            $data->setStatus(InvoiceStatus::CREATED);
        }

        return $this->processor->process($data, $operation, $uriVariables, $context);
    }
}
