<?php 

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Coaster;
use App\Form\CoasterType;
use App\Repository\CategoryRepository;
use App\Repository\CoasterRepository;
use App\Repository\ParkRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class CoasterController extends AbstractController
{
    #[Route(path : 'coaster/add')]
    #[IsGranted('ROLE_USER')]
    public function add(EntityManagerInterface $em, Request $request): Response{

        $user = $this->getUser();
        
        $coaster = new Coaster();
        $coaster->setAuthor($user);
        /*$coaster->setName('MIKO')
            ->setLength(1056)
            ->setMaxSpeed(100)
            ->setMaxHeight(38)
            ->setOperating(2);
        ;*/

        $form = $this->createForm(CoasterType::class, $coaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->persist($coaster);
            $em->flush();

            return $this->redirectToRoute('app_app_index');
        }
        //return new Response("controller added successfully");  
        
        return $this->render('coaster/add.html.twig', [
            'coasterForm' => $form, 
        ]);
    }
    #[Route(path : '/coaster/')]

    public function index(CoasterRepository $coasterRepository, ParkRepository $parkRepository, CategoryRepository $categoryRepository, Request $request): Response
    {
        $parkId = (int) $request->get('Park','');
        $categoryId = (int) $request->get('category','');
        $search = $request->get('search','');

        $itemCount = 20;
        $page = $request->get('p',1);
        $begin = ($page-1) * $itemCount;


        $coaster = $coasterRepository->findAll();
       

        // Traitement possible des paramètres GET (facultatif)
       

        $coasters = $coasterRepository->findFiltered($parkId, $categoryId, $search, $begin, $itemCount);

        dump($coaster);

        $pageCount = max(ceil($coasters->count()/$itemCount), 1);

        return $this->render('coaster/index.html.twig', [
        'parks' => $parkRepository->findAll(),
        'categories' => $categoryRepository->findAll(),
        'coasters' => $coasters,
        'pageCount' => $pageCount,
        
    ]);


    }

    /*#[Route(path : '/coaster/')]
    public function findByFilters(?int $parkId, ?int $categoryId): array
{
    $qb = $this->createQueryBuilder('c');

    if ($parkId) {
        $qb->andWhere('c.park = :parkId')
           ->setParameter('parkId', $parkId);
    }

    if ($categoryId) {
        $qb->andWhere('c.category = :categoryId')
           ->setParameter('categoryId', $categoryId);
    }

    return $qb->getQuery()->getResult();
}*/

    #[Route(path : '/coaster/{id}/edit')]
    #[IsGranted('ROLE_USER')]
    public function edit(Coaster $coaster, Request $request, EntityManagerInterface $em) : Response
    {
        dump($coaster);


        $form = $this->createForm(CoasterType::class, $coaster);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em->flush();

            return $this->redirectToRoute('app_coaster_index');
        }
        //return new Response("controller added successfully");  
        
        return $this->render('coaster/edit.html.twig', [
            'coasterForm' => $form, 
        ]);

    }

    #[Route(path : '/coaster/{id}/delete')]
    #[IsGranted('ROLE_USER')]
    public function delete(Coaster $coaster, Request $request, EntityManagerInterface $em) : Response
    {

        if ($this->isCsrfTokenValid(
            'delete'.$coaster->getId(),
            $request->request->get('_token')
        )) {
            $em->remove($coaster);
            $em->flush();
        
            return $this->redirectToRoute('app_coaster_index');
        }
        
        
        return $this->render('coaster/delete.html.twig',[
            'coaster' => $coaster,
        ]);
    }


  
}

