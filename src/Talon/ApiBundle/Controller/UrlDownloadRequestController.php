<?php

namespace Talon\ApiBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Talon\ApiBundle\Traits\ControllerTrait;
use Talon\ApiBundle\Entity\UrlDownloadRequest;
use Symfony\Component\HttpFoundation\Request;
use Talon\ApiBundle\Form\UrlDownloadRequestType;
use Talon\ApiBundle\Handler\UrlDownloadRequestHandler;

/**
 * UrlDownloadRequest controller.
 *
 */
class UrlDownloadRequestController
{
    use ControllerTrait;

    /**
     * @var UrlDownloadRequestHandler
     */
    private $handler;

    /**
     * @param UrlDownloadRequestHandler $handler
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    /**
     * Lists all urlDownloadRequest entities.
     *
     * @Rest\QueryParam(name="page", requirements="\d+", default="1", description="Page from which to start listing items.")
     * @Rest\QueryParam(name="limit", requirements="-?\d+", default="5", description="How many pages to return.")
     * @Rest\QueryParam(name="sort", requirements="[\w:,.]+", nullable=true, default="modifiedAt:desc", description="How do you want the items sorted in the list.")
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return \Hateoas\Representation\PaginatedRepresentation
     */
    public function index(ParamFetcherInterface $paramFetcher)
    {
        $page = $paramFetcher->get('page');
        $limit = $paramFetcher->get('limit');
        $sort = $this->convertToDoctrineOrderBy($paramFetcher->get('sort'));

        try {
            return $this->handler->all([], $sort, $limit, $page);
        } catch (\Exception $e) {
            throw new BadRequestHttpException($e->getMessage());
        }
    }

    /**
     * Creates a new urlDownloadRequest entity.
     * @param Request $request
     * @return View
     */
    public function create(Request $request)
    {
        $form = $this->handler->createForm(
            UrlDownloadRequestType::class,
            new Urldownloadrequest(),
            ['method' => 'POST']
        );
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UrlDownloadRequest $urlDownloadRequest */
            $urlDownloadRequest = $form->getData();
            $this->handler->save($urlDownloadRequest);

            return View::createRouteRedirect(
                'url_download_request_show',
                ['id' => $urlDownloadRequest->getId()]
            );
        }

        return View::create($form, Response::HTTP_CONFLICT);
    }

    /**
     * Finds and displays a urlDownloadRequest entity.
     * @param $id
     * @return UrlDownloadRequest
     */
    public function show($id)
    {
        $urlDownloadRequest = $this->handler->get(intval($id));

        if ($urlDownloadRequest instanceof UrlDownloadRequest) {
            return $urlDownloadRequest;
        }

        throw new NotFoundHttpException("URL Download Request with ID: {$id} not found.");
    }

    /**
     * Displays a form to edit an existing urlDownloadRequest entity.
     * @param Request $request
     * @param $id
     * @return View
     */
    public function edit(Request $request, $id)
    {
        $urlDownloadRequest = $this->handler->get(intval($id));

        if ($urlDownloadRequest instanceof UrlDownloadRequest) {

            $form = $this->handler->createForm(
                UrlDownloadRequestType::class,
                $urlDownloadRequest,
                ['method' => $request->getMethod()]
            );
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                /** @var UrlDownloadRequest $urlDownloadRequest */
                $urlDownloadRequest = $form->getData();
                $this->handler->save($urlDownloadRequest);

                return View::createRouteRedirect(
                    'url_download_request_show',
                    ['id' => $urlDownloadRequest->getId()]
                );
            }

            return View::create($form, Response::HTTP_CONFLICT);

        }

        throw new NotFoundHttpException("URL Download Request with ID: {$id} not found.");
    }

    /**
     * Deletes a urlDownloadRequest entity.
     * @param Request $request
     * @param $id
     * @return View
     */
    public function delete(Request $request, $id)
    {
        $urlDownloadRequest = $this->handler->get(intval($id));

        if ($urlDownloadRequest instanceof UrlDownloadRequest) {
            $form = $this->handler->createForm(
                UrlDownloadRequestType::class,
                $urlDownloadRequest,
                ['method' => 'DELETE']
            );
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $this->handler->delete($urlDownloadRequest);
            }

            return View::createRouteRedirect('url_download_request_index');
        }

        throw new NotFoundHttpException("URL Download Request with ID: {$id} not found.");
    }

}
