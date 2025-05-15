<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Serializer\Attribute\MaxDepth;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[ApiResource(
    order: ['fullName' => 'ASC'],
    filters: ['client.search_filter', 'client.order_filter'],
    operations: [
        new Get(security: "object.getManager() == user"),
        new Patch(security: "object.getManager() == user"),
        new Post(),
        new GetCollection(
            normalizationContext: [
                AbstractNormalizer::GROUPS => ['api:user:read', 'api:client:read'],
                AbstractObjectNormalizer::ENABLE_MAX_DEPTH => true,
            ],
            paginationItemsPerPage: 10,         // default 10 items per page
            paginationMaximumItemsPerPage: 50,  // max items per page
            paginationClientItemsPerPage: true, // enable client-side to send custom number of items using `_show` parameter already configured
        ),
    ]
)]
#[ApiResource(
    operations: [new Get()],
    uriTemplate: '/invoices/{id}/client',
    uriVariables: [
        'id' => new Link(
            fromClass: Invoice::class,
            fromProperty: 'client'
        )
    ],
    security: "object.getManager() == user",
)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api:client:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['api:client:read'])]
    private ?string $fullName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['api:client:read'])]
    private ?string $companyName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['api:client:read'])]
    private ?string $officePhoneNumber = null;

    /**
     * @var Collection<int, Invoice>
     */
    #[ORM\OneToMany(targetEntity: Invoice::class, mappedBy: 'client', orphanRemoval: true)]
    private Collection $invoices;

    #[ORM\ManyToOne(inversedBy: 'clients')]
    #[ORM\JoinColumn(nullable: true)]
    #[MaxDepth(1)]
    #[Groups(['api:user:read', 'api:client:read'])]
    private ?User $manager = null;

    public function __construct()
    {
        $this->invoices = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullName(): ?string
    {
        return $this->fullName;
    }

    public function setFullName(string $fullName): static
    {
        $this->fullName = $fullName;

        return $this;
    }

    public function getCompanyName(): ?string
    {
        return $this->companyName;
    }

    public function setCompanyName(string $companyName): static
    {
        $this->companyName = $companyName;

        return $this;
    }

    public function getOfficePhoneNumber(): ?string
    {
        return $this->officePhoneNumber;
    }

    public function setOfficePhoneNumber(string $officePhoneNumber): static
    {
        $this->officePhoneNumber = $officePhoneNumber;

        return $this;
    }

    /**
     * @return Collection<int, Invoice>
     */
    public function getInvoices(): Collection
    {
        return $this->invoices;
    }

    public function addInvoice(Invoice $invoice): static
    {
        if (!$this->invoices->contains($invoice)) {
            $this->invoices->add($invoice);
            $invoice->setClient($this);
        }

        return $this;
    }

    public function removeInvoice(Invoice $invoice): static
    {
        if ($this->invoices->removeElement($invoice)) {
            // set the owning side to null (unless already changed)
            if ($invoice->getClient() === $this) {
                $invoice->setClient(null);
            }
        }

        return $this;
    }

    public function getManager(): ?User
    {
        return $this->manager;
    }

    public function setManager(?User $manager): static
    {
        $this->manager = $manager;

        return $this;
    }
}
