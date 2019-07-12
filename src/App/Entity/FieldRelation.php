<?php

/*
 * This file is part of API as a Service.
 *
 * Copyright (c) 2019 Christian Siewert <christian@sieware.international>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Bundle\MakerBundle\Doctrine\EntityRelation;

/**
 * A Field relation relates a service field to another service field. We use it to
 * map One-To-One, One-To-Many and Many-To-One relations in our database.
 *
 * @ApiResource(routePrefix="/aaas")
 * @ORM\Entity()
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *         "whitelist" : {
 *             "relation"
 *         }
 *     }
 * )
 * @ORM\Table(name="App_Field_Relation")
 */
class FieldRelation
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("relation")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, options={"default" : "OneToMany"})
     * @Groups("relation")
     */
    private $type = EntityRelation::ONE_TO_MANY;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("relation")
     */
    private $targetEntity;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("relation")
     */
    private $mappedBy = null;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("relation")
     */
    private $inversedBy = null;

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     * @Groups("relation")
     */
    private $orphanRemoval = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("relation")
     */
    private $joinColumnName = null;

    /**
     * @ORM\Column(type="string", length=255, options={"default" : "id"})
     * @Groups("relation")
     */
    private $joinColumnReferencedColumnName = 'id';

    /**
     * @ORM\Column(type="boolean", options={"default" : false})
     * @Groups("relation")
     */
    private $joinColumnIsUnique = false;

    /**
     * @ORM\Column(type="boolean", options={"default" : true})
     * @Groups("relation")
     */
    private $joinColumnIsNullable = true;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\ServiceField", mappedBy="relation", cascade={"persist", "remove"})
     */
    private $serviceField;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RelationCascade", mappedBy="fieldRelation", orphanRemoval=true)
     * @Groups("relation")
     */
    private $cascades;

    public function __construct()
    {
        $this->cascades = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        if (!in_array($type, EntityRelation::getValidRelationTypes())) {
            throw new InvalidArgumentException("Invalid type");
        }

        $this->type = $type;

        return $this;
    }

    public function getTargetEntity(): ?string
    {
        return $this->targetEntity;
    }

    public function setTargetEntity(string $targetEntity): self
    {
        $this->targetEntity = $targetEntity;

        return $this;
    }

    public function getMappedBy(): ?string
    {
        return $this->mappedBy;
    }

    public function setMappedBy(?string $mappedBy): self
    {
        $this->mappedBy = $mappedBy;

        return $this;
    }

    public function getInversedBy(): ?string
    {
        return $this->inversedBy;
    }

    public function setInversedBy(?string $inversedBy): self
    {
        $this->inversedBy = $inversedBy;

        return $this;
    }

    public function getOrphanRemoval(): ?bool
    {
        return $this->orphanRemoval;
    }

    public function setOrphanRemoval(bool $orphanRemoval): self
    {
        $this->orphanRemoval = $orphanRemoval;

        return $this;
    }

    public function getServiceField(): ?ServiceField
    {
        return $this->serviceField;
    }

    public function setServiceField(?ServiceField $serviceField): self
    {
        $this->serviceField = $serviceField;

        // set (or unset) the owning side of the relation if necessary
        $newRelation = $serviceField === null ? null : $this;
        if ($newRelation !== $serviceField->getRelation()) {
            $serviceField->setRelation($newRelation);
        }

        return $this;
    }

    public function getJoinColumnName(): ?string
    {
        return $this->joinColumnName;
    }

    public function setJoinColumnName(?string $joinColumnName): self
    {
        $this->joinColumnName = $joinColumnName;

        return $this;
    }

    public function getJoinColumnReferencedColumnName(): ?string
    {
        return $this->joinColumnReferencedColumnName;
    }

    public function setJoinColumnReferencedColumnName(string $joinColumnReferencedColumnName): self
    {
        $this->joinColumnReferencedColumnName = $joinColumnReferencedColumnName;

        return $this;
    }

    public function getJoinColumnIsUnique(): ?bool
    {
        return $this->joinColumnIsUnique;
    }

    public function setJoinColumnIsUnique(bool $joinColumnIsUnique): self
    {
        $this->joinColumnIsUnique = $joinColumnIsUnique;

        return $this;
    }

    public function getJoinColumnIsNullable(): ?bool
    {
        return $this->joinColumnIsNullable;
    }

    public function setJoinColumnIsNullable(bool $joinColumnIsNullable): self
    {
        $this->joinColumnIsNullable = $joinColumnIsNullable;

        return $this;
    }

    /**
     * @return Collection|RelationCascade[]
     */
    public function getCascades(): Collection
    {
        return $this->cascades;
    }

    public function addCascade(RelationCascade $cascade): self
    {
        if (!$this->cascades->contains($cascade)) {
            $this->cascades[] = $cascade;
            $cascade->setFieldRelation($this);
        }

        return $this;
    }

    public function removeCascade(RelationCascade $cascade): self
    {
        if ($this->cascades->contains($cascade)) {
            $this->cascades->removeElement($cascade);
            // set the owning side to null (unless already changed)
            if ($cascade->getFieldRelation() === $this) {
                $cascade->setFieldRelation(null);
            }
        }

        return $this;
    }
}
