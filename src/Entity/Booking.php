<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookingRepository")
 */
class Booking
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     */
    private $registrationDate;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Customer", mappedBy="booking", cascade={"persist"})
     */
    private $customer;

    /**
     * @ORM\Column(type="integer")
     */
    private $counter;

    /**
     * @return mixed
     */
    public function getCounter()
    {
        return $this->counter;
    }

    /**
     * @param mixed $counter
     */
    public function setCounter($counter): void
    {
        $this->counter = $counter;
    }

    public function __construct()
    {
        $this->customer = new ArrayCollection();
    }


    public function getId()
    {
        return $this->id;
    }

    public function getRegistrationDate()
    {
        return $this->registrationDate;
    }

    public function setRegistrationDate($registrationDate): self
    {
        $this->registrationDate = $registrationDate;

        return $this;
    }
    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @return Collection|customer[]
     */
    public function getcustomer(): Collection
    {
        return $this->customer;
    }

    public function addcustomer(Customer $customer): self
    {
        if (!$this->customer->contains($customer)) {
            $this->customer[] = $customer;
            $customer->setBooking($this);
        }

        return $this;
    }

    public function removecustomer(Customer $customer): self
    {
        if ($this->customer->contains($customer)) {
            $this->customer->removeElement($customer);
            // set the owning side to null (unless already changed)
            if ($customer->getBooking() === $this) {
                $customer->setBooking(null);
            }
        }

        return $this;
    }
}
