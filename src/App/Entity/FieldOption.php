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

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * Key-value pairs of options that get passed to the underlying
 * database platform when generating DDL statements.
 *
 * @ORM\Entity
 * @ApiResource(
 *     routePrefix="/aaas",
 *     shortName="Field/Options",
 *     normalizationContext={
 *         "groups"={"fieldOption"},
 *         "enable_max_depth" = true
 *     },
 *     denormalizationContext={
 *         "groups"={"fieldOption"},
 *         "enable_max_depth" = true
 *     }
 * )
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *         "name": "word_start",
 *         "field" : "exact"
 *     }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *         "whitelist" : {
 *             "field"
 *         }
 *     }
 * )
 * @ORM\Table(name="App_Field_Option")
 * @author Christian Siewert <christian@sieware.international>
 */
class FieldOption
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups("fieldOption")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("fieldOption")
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("fieldOption")
     * @Assert\NotBlank()
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="Field", inversedBy="fieldOptions")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("fieldOption")
     * @Assert\NotBlank()
     * @MaxDepth(1)
     */
    private $field;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getfield(): ?Field
    {
        return $this->field;
    }

    public function setfield(?Field $field): self
    {
        $this->field = $field;

        return $this;
    }
}
