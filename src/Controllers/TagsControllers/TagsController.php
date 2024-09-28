<?php

namespace App\Controllers\TagsControllers;

use App\Controllers\AbstractController\Controller;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TagsController extends Controller
{
    /**
     * This method return all tags
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getAllTags(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $page = empty($request->getQueryParams()['page']) ? 1 : (int)$request->getQueryParams()['page']; // get page
        $limit = empty($request->getQueryParams()['limit']) ? 25 : (int)$request->getQueryParams()['limit']; // get limit data per page
        $offset = ($page - 1) * $limit; // offset

        try {
            $tags = R::find('tags', 'LIMIT ? OFFSET ?', [$limit, $offset]); // get data
            $totalTags = R::count('tags'); // get total task count

            // Формируем результат
            $result = [
                'data' => array_values($tags),
                'pagination' => [
                    'total' => $totalTags,
                    'page' => $page,
                    'limit' => $limit,
                ],
            ];
        } catch (Exception|\DivisionByZeroError $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result);
    }

    /**
     * This method return data for one task
     *
     * METHOD: GET
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public function getOneTag(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $tagId = (int)$request->getAttribute('id');
            $tag = R::load('tags', $tagId); //load tag
            $result = [];

            //check task exist
            if(!$tag->id) return $this->createErrorResponse($response, 404, 'Tag not found');

            $result['data'] = $tag;
            $result['success'] = true;
            $result['errors'] = [];

        } catch (Exception|\DivisionByZeroError $e) {
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
     * @param $args
     * @return ResponseInterface
     */
    public function createTag(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $requestData = json_decode($request->getBody()->getContents(), true); //get request data
        $errors = []; //error data

        //region validate
        if (empty($requestData['name'])) {
            $errors['name'] = 'Name is required';
        }
        //endregion

        //if validate success then add tag
        if (count($errors) === 0) {
            try {
                $tagTable = R::dispense('tags');
                $tagTable->name = $requestData['name'];

                $newTagId = R::store($tagTable);
            } catch (Exception $e) {
                return $this->createErrorResponse($response, 500, $e->getMessage());
            }
        } else {
            return $this->createErrorResponse($response, 400, $errors);
        }

        //json result
        $result = [
            'data' => isset($newTagId) ? R::load('tags', $newTagId) : [],
            'success' => count($errors) === 0,
            'errors' => $errors,
        ];

        return $this->createSuccessResponse($response, $result, 201);
    }

    /**
     * This method delete tag
     *
     * METHOD: DELETE
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function deleteTag(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        // get tag id
        $id = $request->getAttribute('id');

        // load tag
        $tag = R::load('tags', (int)$id);

        // check tag
        if (!$tag->id) {
            return $this->createErrorResponse($response, 404, 'Tag not found');
        }

        try {
            // delete tag
            R::trash($tag);

            $result = [
                'success' => true,
                'errors' => [],
            ];

            return $this->createSuccessResponse($response, $result, 204);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }
    }

    /**
     * This method update tag data
     *
     * METHOD: PUT
     *
     * @param RequestInterface $request
     * @param ResponseInterface $response
     * @param $args
     * @return ResponseInterface
     */
    public function updateTag(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        $tagId = $request->getAttribute('id');
        $requestData = json_decode($request->getBody()->getContents(), true);

        // Load tag from DB
        $tag = R::load('tags', $tagId);

        // Check if tag exists
        if (!$tag->id) {
            return $this->createErrorResponse($response, 404, 'Tag not found');
        }

        // Update tag fields from request data
        $tableColumns = array_keys(R::inspect('tags'));
        foreach ($tableColumns as $column) {
            if (isset($requestData[$column])) {
                $tag->$column = $requestData[$column];
            }
        }

        // Save changes and handle potential errors
        try {
            R::store($tag);
            $responseData = [
                'data' => $tag,
                'success' => true,
                'errors' => [],
            ];
            return $this->createSuccessResponse($response, $responseData, 200);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }
    }
}