<?php

declare(strict_types=1);

namespace App\Support;

use Doctrine\ORM\EntityManagerInterface;

/**
 * Trait EntityManagerInterfaceAware
 * @package App\Support
 */
trait EntityManagerInterfaceAware
{
    /** @required */
    public EntityManagerInterface $em;
}
