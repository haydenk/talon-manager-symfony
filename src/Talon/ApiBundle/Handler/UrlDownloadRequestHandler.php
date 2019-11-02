<?php

namespace Talon\ApiBundle\Handler;


use Doctrine\ORM\EntityManagerInterface;
use Hateoas\Configuration\Route;
use Hateoas\Representation\Factory\PagerfantaFactory;
use Hateoas\Representation\PaginatedRepresentation;
use Pagerfanta\Adapter\DoctrineCollectionAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Form\FormFactoryInterface;
use Talon\ApiBundle\Entity\UrlDownloadRequest;
use Talon\ApiBundle\Repository\UrlDownloadRequestRepository;

class UrlDownloadRequestHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var UrlDownloadRequestRepository
     */
    private $repository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param EntityManagerInterface $manager
     */
    public function setManager(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param UrlDownloadRequestRepository $repository
     */
    public function setRepository(UrlDownloadRequestRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param FormFactoryInterface $formFactory
     */
    public function setFormFactory(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * @param array $criteria
     * @param array $order
     * @param int $limit
     * @param int $page
     * @return PaginatedRepresentation
     */
    public function all(array $criteria, array $order, $limit = 20, $page = 1)
    {
        $queryBuilder = $this->repository->createQueryBuilder('udr');
        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);
        $pager->setMaxPerPage($limit);
        $pager->setCurrentPage($page);

        $pagerfantaFactory   = new PagerfantaFactory(); // you can pass the page,
        // and limit parameters name
        $paginatedCollection = $pagerfantaFactory->createRepresentation(
            $pager,
            new Route('url_download_request_index', [], true)
        );

        return $paginatedCollection;
    }

    /**
     * @param $id
     * @return UrlDownloadRequest|object|null
     */
    public function get($id)
    {
        return $this->repository->find($id);
    }

    public function createForm($type, $data = null, array $options = [])
    {
        return $this->formFactory->create($type, $data, $options);
    }

    public function save($entity)
    {
        $this->manager->persist($entity);
        $this->manager->flush();
        return true;
    }

    public function delete($entity)
    {
        $this->manager->remove($entity);
        $this->manager->flush();
        return true;
    }
}