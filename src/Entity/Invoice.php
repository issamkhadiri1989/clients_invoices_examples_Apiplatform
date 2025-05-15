<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enum\InvoiceStatus;
use App\Repository\InvoiceRepository;
use App\State\Processor\InvoiceStateProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: InvoiceRepository::class)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(processor: InvoiceStateProcessor::class),
        new Patch(),
        new Delete(),
    ]
)]
#[ApiResource(
    operations: [new GetCollection()],
    uriTemplate: '/clients/{clientId}/invoices',
    uriVariables: [
        'clientId' => new Link(fromClass: Client::class, toProperty: 'client'),
    ],
    filters: ['invoice.order_filter', 'invoice.status_filter'],
)]
class Invoice
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $number = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTime $dueDate = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $invoiceDate = null;

    #[ORM\ManyToOne(inversedBy: 'invoices')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Client $client = null;

    #[ORM\Column]
    private ?float $subTotal = null;

    #[ORM\Column]
    private ?float $totalTax = null;

    #[ORM\Column]
    private ?float $totalAmountDue = null;

    #[ORM\Column(enumType: InvoiceStatus::class)]
    private ?InvoiceStatus $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $notes = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): static
    {
        $this->number = $number;

        return $this;
    }

    public function getDueDate(): ?\DateTime
    {
        return $this->dueDate;
    }

    public function setDueDate(?\DateTime $dueDate): static
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    public function getInvoiceDate(): ?\DateTime
    {
        return $this->invoiceDate;
    }

    public function setInvoiceDate(\DateTime $invoiceDate): static
    {
        $this->invoiceDate = $invoiceDate;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): static
    {
        $this->client = $client;

        return $this;
    }

    public function getSubTotal(): ?float
    {
        return $this->subTotal;
    }

    public function setSubTotal(float $subTotal): static
    {
        $this->subTotal = $subTotal;

        return $this;
    }

    public function getTotalTax(): ?float
    {
        return $this->totalTax;
    }

    public function setTotalTax(float $totalTax): static
    {
        $this->totalTax = $totalTax;

        return $this;
    }

    public function getTotalAmountDue(): ?float
    {
        return $this->totalAmountDue;
    }

    public function setTotalAmountDue(float $totalAmountDue): static
    {
        $this->totalAmountDue = $totalAmountDue;

        return $this;
    }

    public function getStatus(): ?InvoiceStatus
    {
        return $this->status;
    }

    public function setStatus(InvoiceStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }
}
