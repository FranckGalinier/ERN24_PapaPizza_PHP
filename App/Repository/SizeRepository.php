<?php

namespace App\Repository;

use App\Model\Size;
use App\AppRepoManager;
use Core\Repository\Repository;

class SizeRepository extends Repository
{
  public function getTableName(): string
  {
    return 'size';
  }

  /**
   * méthode pour récupérer les tailles actives
   * @return array
   */
  public function getAllSize():array
  {
  return $this->readAll(Size::class);
  }
}