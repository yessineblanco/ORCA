<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    /**
    *  @Groups({"post:read"})
    */
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    /**
    *  @Groups({"post:read"})
    */
    private ?string $CategoryName = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank()]
    /**
    *  @Groups({"post:read"})
    */
    private ?string $CategoryType = null;

    #[ORM\OneToMany(mappedBy: 'Category', targetEntity: Product::class)]

    private Collection $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategoryName(): ?string
    {
        return $this->CategoryName;
    }

    public function setCategoryName(string $CategoryName): self
    {
        $this->CategoryName = $CategoryName;

        return $this;
    }

    public function getCategoryType(): ?string
    {
        return $this->CategoryType;
    }

    public function setCategoryType(string $CategoryType): self
    {
        $this->CategoryType = $CategoryType;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
            $product->setCategory($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->products->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCategory() === $this) {
                $product->setCategory(null);
            }
        }

        return $this;
    }
}
