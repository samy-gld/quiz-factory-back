<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExecutionRepository")
 */
class Execution
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Invitation", cascade={"persist", "remove"}, mappedBy="execution")
     * @ORM\JoinColumn(name="invitation_token", referencedColumnName="token", nullable=false, unique=true)
     */
    private $invitation;

    /**
     * @ORM\Column(type="boolean")
     */
    private $started;

    /**
     * @ORM\Column(type="boolean")
     */
    private $finished;

    /**
     * @ORM\Column(type="integer")
     */
    private $currentPosition;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Answer", mappedBy="execution", orphanRemoval=true)
     */
    private $answers;

    public function __construct()
    {
        $this->answers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInvitation(): ?Invitation
    {
        return $this->invitation;
    }

    public function setInvitation(Invitation $invitation): self
    {
        $this->invitation = $invitation;

        return $this;
    }

    public function getStarted(): ?bool
    {
        return $this->started;
    }

    public function setStarted(bool $started): self
    {
        $this->started = $started;

        return $this;
    }

    public function getFinished(): ?bool
    {
        return $this->finished;
    }

    public function setFinished(bool $finished): self
    {
        $this->finished = $finished;

        return $this;
    }

    public function getCurrentPosition(): ?int
    {
        return $this->currentPosition;
    }

    public function setCurrentPosition(int $currentPosition): self
    {
        $this->currentPosition = $currentPosition;

        return $this;
    }

    /**
     * @return Collection|Answer[]
     */
    public function getAnswers(): Collection
    {
        return $this->answers;
    }

    public function addAnswer(Answer $answer): self
    {
        if (!$this->answers->contains($answer)) {
            $this->answers[] = $answer;
            $answer->setExecution($this);
        }

        return $this;
    }

    public function removeAnswer(Answer $answer): self
    {
        if ($this->answers->contains($answer)) {
            $this->answers->removeElement($answer);
            // set the owning side to null (unless already changed)
            if ($answer->getExecution() === $this) {
                $answer->setExecution(null);
            }
        }

        return $this;
    }
}
