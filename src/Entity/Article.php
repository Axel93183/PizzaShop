<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\ArticleRepository;
use Gedmo\Mapping\Annotation\Timestampable;
use phpDocumentor\Reflection\Types\This;

#[ORM\Entity(repositoryClass: ArticleRepository::class)]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'articles', cascade: ['persist', 'remove'])]
    private ?Basket $basket = null;

    #[ORM\Column(nullable: true)]
    private ?int $quantity = null;

    #[ORM\ManyToOne(inversedBy: 'articles', cascade: ['persist', 'remove'])]
    private ?Pizza $pizza = null;

    #[Timestampable(on: 'create')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[Timestampable(on: 'update')]
    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $updatedAT = null;

    #[ORM\ManyToOne(inversedBy: 'Article')]
    private ?Order $orders = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBasket(): ?Basket
    {
        return $this->basket;
    }

    public function setBasket(?Basket $basket): self
    {
        $this->basket = $basket;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getPizza(): ?Pizza
    {
        return $this->pizza;
    }

    public function setPizza(?Pizza $pizza): self
    {
        $this->pizza = $pizza;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAT(): ?\DateTimeInterface
    {
        return $this->updatedAT;
    }

    public function setUpdatedAT(?\DateTimeInterface $updatedAT): self
    {
        $this->updatedAT = $updatedAT;

        return $this;
    }

    //fonction qui retourne le total d'articleselon le prix et la quantité de pizza voulu
    public function getTotal():float
    {
        return round($this->quantity * $this->pizza->getPrice(),2);
    }

    public function getOrders(): ?Order
    {
        return $this->orders;
    }

    public function setOrders(?Order $orders): self
    {
        $this->orders = $orders;

        return $this;
    }
}
