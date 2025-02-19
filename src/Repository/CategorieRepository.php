<?php
// src/Repository/CategorieRepository.php

namespace App\Repository;

use App\Entity\Categorie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategorieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Categorie::class);
    }

    // Ajoutez une fonction pour vérifier l'unicité du nom
    public function findByNom(string $nom): ?Categorie
    {
        return $this->findOneBy(['nom' => $nom]);
    }
}
