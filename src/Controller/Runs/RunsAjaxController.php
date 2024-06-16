<?php

namespace App\Controller\Runs;

use App\Entity\Categories\Categories;
use App\Entity\Games\Games;
use App\Entity\Runs\Runs;
use App\Entity\Status\Status;
use App\Form\Runs\RunsSubmitType;
use App\Form\Runs\RunsType;
use App\Repository\Runs\RunsRepository;
use App\Service\Categories\CategoriesService;
use App\Service\Mailer\MailerService;
use App\Service\Runs\RunsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

#[Route('/runs')]
class RunsAjaxController extends AbstractController
{
    public function __construct(
        private CategoriesService $categoriesService,
        private EntityManagerInterface $entityManager,
        private RunsService $runsService,
        private MailerService $mailerService
    ) {}

    #[Route('/fields/call', name: 'app_runs_fields_call', methods: ['POST'])]
    public function loadFieldsCall(Request $request): Response
    {
        $category = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => $request->request->all()['runs']['refCategories']]);

        if (!$category instanceof Categories){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }

        $game = $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $request->request->all()['runs']['game']]);
        if (!$game instanceof Games){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }

        //Gestion d'erreur si le user change l'uuid par un autre correspondant
        if ($category->getRefGames() !== $game){
            return new JsonResponse(['message' => 'An error occurred'], 400);
        }


        return $this->categoriesService->getFieldsFromCategory($category);
        //Return les fields en fonction de la category donc category->getFields ?
        /*return $this->categoriesService->getFieldsFromCategory()*/
    }
    #[Route('/submit/call', name: 'app_runs_submit_call', methods: ['POST'])]
    public function submitCall(Request $request): Response
    {


        $category = $this->entityManager->getRepository(Categories::class)->findOneBy(['uuid' => $request->request->all()['runs']['refCategories']]);

        $game = $this->entityManager->getRepository(Games::class)->findOneBy(['uuid' => $request->request->all()['runs']['game']]);

        if (!$game instanceof Games){
            return new JsonResponse(['An error occurred'], 400);
        }

        $run = new Runs();
        $form = $this->createForm(RunsType::class,$run , ['game' => $game]);
        $form->handleRequest($request);


        return $this->runsService->runsValidation($run, $request);
    }


    #[Route('/validation/call', name: 'app_runs_validation_call', methods: ['POST'])]
    public function validationCall(Request $request): Response
    {


        $run = $this->entityManager->getRepository(Runs::class)->findOneBy(['uuid' => $request->request->all()['runs']['uuid']]);

        $modnote = trim(htmlspecialchars($request->request->all()['runs']['modNotes']));

        if ($modnote === ""){
            $run->setModNotes(null);
        }else{
            $run->setModNotes($modnote);
        }


        $this->runsService->checkOldAndNewRun($run);

        $run->setDateCheck(now());
        $run->setVerifiedBy($this->getUser());

        $emailTo = [];
        foreach ($run->getRefUsers() as $user){
            $emailTo[] = $user->getEmail();
        }

        $this->mailerService->sendMailRunChecked($emailTo,$run);

        $this->entityManager->persist($run);
        $this->entityManager->flush();

        return new JsonResponse(['message' => 'Run validated successfully', 'url' => '/' .$run->getRefGame()->getRewrite() .'/runs/verifications'], 200);

    }

    #[Route('/reject/call', name: 'app_runs_reject_call', methods: ['POST'])]
    public function rejectCall(Request $request): Response
    {

        $run = $this->entityManager->getRepository(Runs::class)->findOneBy(['uuid' => $request->request->all()['runs']['uuid']]);

        $modnote = $request->request->all()['runs']['modNotes'];


        if ($modnote === null || empty(trim($modnote))){
            return new JsonResponse(['message' => 'Please fill the reason of the reject'], 400);
        }

        $run->setModNotes($modnote);

        $run->setRefStatus($this->entityManager->getRepository(Status::class)->find(3));

        $run->setDateCheck(now());
        $run->setVerifiedBy($this->getUser());

        $emailTo = [];
        foreach ($run->getRefUsers() as $user){
            $emailTo[] = $user->getEmail();
        }

        $this->mailerService->sendMailRunChecked($emailTo,$run);

        $this->entityManager->persist($run);
        $this->entityManager->flush();


        return new JsonResponse(['message' => 'Run rejected successfully', 'url' => '/' .$run->getRefGame()->getRewrite() .'/runs/verifications'], 200);
    }

}