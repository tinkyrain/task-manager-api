<?php

namespace App\Controllers\TagsControllers;

use App\Controllers\AbstractController\Controller;
use Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use RedBeanPHP\R;

class TagController extends Controller
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
    public function getAll(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $page = empty($request->getQueryParams()['page']) ? 1 : (int)$request->getQueryParams()['page']; // get page
            $limit = empty($request->getQueryParams()['limit']) ? 25 : (int)$request->getQueryParams()['limit']; // get limit data per page
            $offset = ($page - 1) * $limit; // offset

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
    public function getOne(RequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        try {
            $tagId = (int)$request->getAttribute('id');
            $tag = R::load('tags', $tagId); //load tag

            //check task exist
            if (!$tag->id) return $this->createErrorResponse($response, 404, 'Tag not found');

            $result['data'] = $tag;
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
    public function create(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
            $requestData = json_decode($request->getBody()->getContents(), true); //get request data

            //validate
            if (empty($requestData['name']))
                return $this->createErrorResponse($response, 400, 'Tag name is required');

            $tagTable = R::dispense('tags');
            $tagTable->name = $requestData['name'];

            $newTagId = R::store($tagTable);

            $result['data'] = isset($newTagId) ? R::load('tags', $newTagId) : [];
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
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
     * @param $args
     * @return ResponseInterface
     */
    public function delete(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
            $id = $request->getAttribute('id');
            $tag = R::load('tags', (int)$id);

            // check tag
            if (!$tag->id) {
                return $this->createErrorResponse($response, 404, 'Tag not found');
            }

            // delete tag
            R::trash($tag);
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
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
     * @param $args
     * @return ResponseInterface
     */
    public function update(RequestInterface $request, ResponseInterface $response, $args): ResponseInterface
    {
        try {
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

            R::store($tag);
            $result['data'] = $tag;
        } catch (Exception $e) {
            return $this->createErrorResponse($response, 500, $e->getMessage());
        }

        return $this->createSuccessResponse($response, $result, 200);
    }
}