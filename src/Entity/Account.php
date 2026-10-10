<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 100)]
    private ?string $name = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    private ?string $balance = null;

    #[ORM\ManyToOne(inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $owner = null;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'sourceAccount')]
    private Collection $transactionsSent;

    /**
     * @var Collection<int, Transaction>
     */
    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'targetAccount')]
    private Collection $transactionsReceived;

    public function __construct()
    {
        $this->transactionsSent = new ArrayCollection();
        $this->transactionsReceived = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getBalance(): ?string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsSent(): Collection
    {
        return $this->transactionsSent;
    }

    public function addTransactionsSent(Transaction $transaction): static
    {
        if (!$this->transactionsSent->contains($transaction)) {
            $this->transactionsSent->add($transaction);
            $transaction->setSourceAccount($this);
        }

        return $this;
    }

    public function removeTransactionsSent(Transaction $transaction): static
    {
        if ($this->transactionsSent->removeElement($transaction)) {
            if ($transaction->getSourceAccount() === $this) {
                $transaction->setSourceAccount(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Transaction>
     */
    public function getTransactionsReceived(): Collection
    {
        return $this->transactionsReceived;
    }

    public function addTransactionsReceived(Transaction $transaction): static
    {
        if (!$this->transactionsReceived->contains($transaction)) {
            $this->transactionsReceived->add($transaction);
            $transaction->setTargetAccount($this);
        }

        return $this;
    }

    public function removeTransactionsReceived(Transaction $transaction): static
    {
        if ($this->transactionsReceived->removeElement($transaction)) {
            if ($transaction->getTargetAccount() === $this) {
                $transaction->setTargetAccount(null);
            }
        }

        return $this;
    }
}