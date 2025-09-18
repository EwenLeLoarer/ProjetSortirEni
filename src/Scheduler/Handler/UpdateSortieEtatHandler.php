<?php

namespace App\Scheduler\Handler;

use App\Scheduler\Message\UpdateSortieEtatMessage;
use App\Service\SortieService;  // your service with updateEtatSortie()
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class UpdateSortieEtatHandler
{
    public function __construct(private SortieService $sortieService)
    {
    }

    public function __invoke(UpdateSortieEtatMessage $message): void
    {
        $sortie = $this->sortieService->updateEtatSortie();
    }
}

?>