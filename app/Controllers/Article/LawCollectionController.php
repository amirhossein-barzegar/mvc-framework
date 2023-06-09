<?php

namespace App\Controllers\Article;

use App\Controllers\BaseController;
use App\Models\Article\LawCollection;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class LawCollectionController extends BaseController
{
    /**
     * Get list of all Law collections (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $lawCollections = LawCollection::all();
        if ($lawCollections) {
            $response->setBody([
                'state' => 'success',
                'data' => $lawCollections
            ]);
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No records found!'
            ]);
        }
        return $response;
    }

    /**
     * Create new Law collection (POST)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->body();
        foreach ($data as $field=>$value) {
            $data[$field] = htmlspecialchars(trim($value),ENT_QUOTES,'UTF-8');
        }
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'title' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest('lc_');
            $lawCollection = LawCollection::create($validatedData);
            if ($lawCollection instanceof Response) {
                return $lawCollection;
            } elseif ($lawCollection) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Law collection created successfully!',
                    'law_id' => $lawCollection->getLcId()
                ]);
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 30,
                    'message' => 'Something went\'s wrong on creating!'
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 1,
                'message' => 'Invalid parameters passed!'
            ]);
        }
        return $response;
    }

    /**
     * Show specific law collection (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $lawCollection = LawCollection::findById($request->params['id']);
        if ($lawCollection) {
            $response->setBody([
                'state' => 'success',
                'data' => $lawCollection
            ]);
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No record found!'
            ]);
        }
        return $response;
    }

    /**
     * Update specific law collection (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $lawCollection = LawCollection::findById($request->params['id']);
        if ($lawCollection) {
            $data = $request->body();
            if (count($data)) {
                foreach ($data as $field=>$value) {
                    $data[$field] = htmlspecialchars(trim($value),ENT_QUOTES,'UTF-8');
                }
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 2,
                    'message' => 'No parameters passed!'
                ]);
                return $response;
            }
            $formRequest = new FormRequest($data);
            $formRequest->rules = [
                'title' => 'optional|notnull'
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('lc_');
                $update = LawCollection::update($lawCollection->getLcId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Law collection successfully updated!',
                        'law_id' => $update->getLcId()
                    ]);
                } else {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 31,
                        'message' => 'Something went\'s wrong on updating!'
                    ]);
                }
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 1,
                    'message' => 'Invalid parameters passed!'
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No record found!'
            ]);
        }
        return $response;
    }

    /**
     * Delete specific law collection (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = LawCollection::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Law collection deleted successfully!',
                'law_id' => $deleted->getLcId()
            ]);
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No record found!'
            ]);
        }
        return $response;
    }

    public function getSections(Request $request, Response $response): Response
    {
        $lawCollection = LawCollection::findById($request->params['id'], ['sections']);
        if ($lawCollection instanceof Response) {
            return $lawCollection;
        } elseif ($lawCollection) {
            if ($lawCollection->sections) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $lawCollection->sections
                ]);
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 5,
                    'message' => 'No record found!'
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No record found!'
            ]);
        }
        return $response;
    }
}