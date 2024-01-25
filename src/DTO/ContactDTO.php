<?php
namespace App\DTO;

use App\Entity\Contact;
use Symfony\Component\Validator\Constraints as Assert;

class ContactDTO
{
    public function __construct(

        #[Assert\NotNull]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $name = null,
    
        #[Assert\Email()]
        public ?string $email = null,
    
        #[Assert\NotNull]
        #[Assert\Length(min: 3, max: 255)]
        public ?string $message = null,
    ) {}

    public static function createByEntity(Contact $contact): self
    {
        return new ContactDTO(
            $contact->getName(),
            $contact->getEmail(),
            $contact->getMessage(),
        );
    }

    public function fillEntity(Contact $contact): Contact
    {
        $contact->setName($this->name);
        $contact->setEmail($this->email);
        $contact->setMessage($this->message);

        return $contact;
    }
}