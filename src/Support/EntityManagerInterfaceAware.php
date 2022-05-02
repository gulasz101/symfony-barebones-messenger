<?php

declare(strict_types=1);

namespace App\Support;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Trait EntityManagerInterfaceAware
 * @package App\Support
 */
trait EntityManagerInterfaceAware
{
    #[Required]
    public EntityManagerInterface $em;
}
