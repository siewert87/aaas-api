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
use InvalidArgumentException;

/**
 * Field Constraint Option.
 *
 * @ORM\Entity
 * @ApiResource(
 *     routePrefix="/aaas/field",
 *     attributes={
 *         "normalization_context"={
 *             "groups"={"constraintOption"},
 *             "enable_max_depth" = true
 *         },
 *         "denormalization_context"={
 *             "groups"={"constraintOption"},
 *             "enable_max_depth" = true
 *         }
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *         "name": "word_start"
 *     }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *         "whitelist" : {
 *             "constraint",
 *         }
 *     }
 * )
 * @ORM\Table(name="App_Constraint_Option")
 * @author Christian Siewert <christian@sieware.international>
 */
class ConstraintOption
{
    /**
     * @see https://symfony.com/doc/current/reference/constraints.html#supported-constraints
     *
     * @todo add more constraint options
     */
    const VALID_OPTIONS = array(
        'message',
        'value',
        'mode',
        'version',
        'normalizer',
        'min',
        'max',
        'minMessage',
        'maxMessage',
        'allowEmptyString',
        'charset',
        'charsetMessage',
        'exactMessage',
        'type',
        'isbn10Message',
        'isbn13Message',
        'bothIsbnMessage'
    );

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @Groups("constraintOption")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("constraintOption")
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("constraintOption")
     * @Assert\NotBlank
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Constraint", inversedBy="constraintOptions")
     * @Groups("constraintOption")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     * @MaxDepth(1)
     */
    private $constraint;

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
        if (!in_array($name, self::VALID_OPTIONS)) {
            throw new InvalidArgumentException("Invalid type");
        }

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

    public function getConstraint(): ?Constraint
    {
        return $this->constraint;
    }

    public function setConstraint(?Constraint $constraint): self
    {
        $this->constraint = $constraint;

        return $this;
    }
}
