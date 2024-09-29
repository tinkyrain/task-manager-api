<?php

namespace App\Controllers\TagControllers;

use App\Controllers\AbstractController\AbstractController;
use App\Repositories\TagRepositories\TagRepository;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class TagController extends AbstractController
{
    protected TagRepository $tagRepository;

    public function __construct()
    {
        $this->tagRepository = new TagRepository();
    }

    /**
     * This method return all tags
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $page = $request->getQueryParams()['page'] ?? 1;
            $limit = $request->getQueryParams()['limit'] ?? 25;

            $tags = $this->tagRepository->getAllTags($page, $limit);
            $totalCounts = $this->tagRepository->getTotalTags();

            $result = [
                'data' => $tags,
                'pagination' => [
                    'total' => $totalCounts,
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result);
    }

    /**
     * This method return data for one tag
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getOne(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $tagId = $request->getAttribute('id');
            $tag = $this->tagRepository->getTagById($tagId);

            $result['data'] = $tag;
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result);
    }

    /**
     * This method create tag
     *
     * METHOD: POST
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function create(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $requestData = json_decode($request->getBody()->getContents(), true);

            //validate
            if (empty($requestData['title'])) {
                throw new \Exception('Tag title is required', 400);
            }

            $newTag = $this->tagRepository->createTag($requestData);

            $result['data'] = $newTag;
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, $e->getCode(), $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 201);
    }

    /**
     * This method delete tag
     *
     * METHOD: DELETE
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function delete(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $id = $request->getAttribute('id');
            $this->tagRepository->deleteTag($id);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, $e->getCode(), $e->getMessage());
        }

        return $this->createSuccessResponse($response, [], 204);
    }

    /**
     * This method update tag data
     *
     * METHOD: PUT
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $tagId = $request->getAttribute('id');
            $requestData = json_decode($request->getBody()->getContents(), true);

            // Update tag fields
            $this->tagRepository->updateTag($tagId, $requestData);

            $result['data'] = $this->tagRepository->getTagById($tagId);
        } catch (\Exception $e) {
            return $this->createErrorResponse($response, $e->getCode(), $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 200);
    }
}