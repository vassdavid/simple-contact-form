<?php
namespace App\DTO;

use App\Entity\User;
use App\Constant\UserRole;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserDTO
{
    public function __construct(

        #[Assert\NotBlank()]
        #[Assert\Length(min: 3, max: 30)]
        public ?string $username = null,

        /** @var array<int,string> */
        #[Assert\Type('array')]
        #[Assert\Choice(UserRole::ROLES, multiple: true)]
        public array $roles = [],

        #[Assert\NotBlank(groups: ['create'])]
        public ?string $password = null,
    ) {
    }

    public static function createByEntity(User $user): self
    {
        return new UserDTO(
            $user->getUsername(),
            $user->getRoles(),
            null,
        );
    }

    public function fillEntity(User $user, UserPasswordHasherInterface $passwordHasher): User
    {
        $user->setUsername($this->username);
        $user->setRoles($this->roles);
        if($this->password != null) {
            $user->setPassword(
                $passwordHasher->hashPassword($user, $this->password)
            );
        } 

        return $user;
    }
}