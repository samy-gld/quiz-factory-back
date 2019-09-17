<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $questionPosition;

    /**
     * @ORM\Column(type="boolean")
     */
    private $success;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Execution", inversedBy="answers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $execution;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Proposition")
     */
    private $propositions;

    public function __construct()
    {
        $this->propositions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestionPosition(): ?int
    {
        return $this->questionPosition;
    }

    public function setQuestionPosition(int $questionPosition): self
    {
        $this->questionPosition = $questionPosition;

        return $this;
    }

    public function getSuccess(): ?bool
    {
        return $this->success;
    }

    public function setSuccess(bool $success): self
    {
        $this->success = $success;

        return $this;
    }

    public function getExecution(): ?Execution
    {
        return $this->execution;
    }

    public function setExecution(?Execution $execution): self
    {
        $this->execution = $execution;

        return $this;
    }

    /**
     * @return Collection|Proposition[]
     */
    public function getPropositions(): Collection
    {
        return $this->propositions;
    }

    public function addProposition(Proposition $proposition): self
    {
        if (!$this->propositions->contains($proposition)) {
            $this->propositions[] = $proposition;
        }

        return $this;
    }

    public function removeProposition(Proposition $proposition): self
    {
        if ($this->propositions->contains($proposition)) {
            $this->propositions->removeElement($proposition);
        }

        return $this;
    }
}
