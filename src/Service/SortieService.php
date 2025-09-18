<?php
    namespace App\Service;

    use App\Entity\Etat;
    use App\Entity\Sortie;
    use App\Repository\SortieRepository;
    use Doctrine\ORM\EntityManagerInterface;

    class SortieService
    {
        public function __construct(
            private SortieRepository $sortieRepository,
            private EntityManagerInterface $entityManager
        ) {}

        public function updateEtatSortie(): int{
            $now = new \DateTimeImmutable();

            $etatRepo = $this->entityManager->getRepository(Etat::class);

            $etatClose = $etatRepo->find(3);
            $etatStart = $etatRepo->find(4);
            $etatFinish = $etatRepo->find(5);
            $etatArchived = $etatRepo->find(7);

            $sortieToClose = $this->sortieRepository->GetNeedToCloseSortie();
            $sortieToStart = $this->sortieRepository->GetNeedToStartSortie();
            $sortieToFinish = $this->sortieRepository->GetNeedToFinishSortie();
            $sortieToArchived = $this->sortieRepository->GetNeedToBeArchived();
            foreach ($sortieToClose as $sortie) {
                $sortie->setEtat($etatClose);
            }

            foreach ($sortieToStart as $sortie) {
                $sortie->setEtat($etatStart);
            }

            foreach ($sortieToFinish as $sortie) {
                $sortie->setEtat($etatFinish);
            }

            foreach ($sortieToArchived as $sortie) {
                $sortie->setEtat($etatArchived);
            }

            $this->entityManager->flush();

            return 0;
        }
    }
?>