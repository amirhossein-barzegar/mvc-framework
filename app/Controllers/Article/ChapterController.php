<?php

namespace App\Controllers\Article;

use App\Controllers\BaseController;
use App\Models\Article\Chapter;
use App\Models\Article\Section;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class ChapterController extends BaseController
{
    /**
     * Get list of all Chapters (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $chapter = Chapter::all();
        if ($chapter) {
            $response->setBody([
                'state' => 'success',
                'data' => $chapter
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
     * Create new Chapter (POST)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->body();
        if (count($data)) {
            foreach ($data as $field=>$value) {
                $data[$field] = htmlspecialchars(trim($value),ENT_QUOTES,'UTF-8');
            }
        }
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'title' => 'required',
            'section_id' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest('c_');
            $chapter = Chapter::create($validatedData);
            if ($chapter instanceof Response) {
                return $chapter;
            } elseif ($chapter) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Chapter created successfully!',
                    'chapter_id' => $chapter->getCId()
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
     * Show specific chapter (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $chapter = Chapter::findById($request->params['id']);
        if ($chapter) {
            $response->setBody([
                'state' => 'success',
                'data' => $chapter
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
     * Update specific Chapter (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $response->setHeader('Content-Type', 'application/json');
        $chapter = Chapter::findById($request->params['id']);
        if ($chapter) {
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
                'title' => 'optional|notnull',
                'section_id' => 'optional|notnull',
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('c_');
                $update = Chapter::update($chapter->getCId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Chapter successfully updated!',
                        'chpater_id' => $update->getCId()
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
     * Delete specific Chapter (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $response->setHeader('Content-Type', 'application/json');
        $deleted = Chapter::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Chapter successfully deleted!',
                'chapter_id' => $deleted->getCId()
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

    public function getSection(Request $request, Response $response): Response
    {
        $chapter = Chapter::findById($request->params['id'],'section');
        if ($chapter) {
            if ($chapter->section) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $chapter->section
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

    public function getTopics(Request $request, Response $response): Response
    {
        $chapter = Chapter::findById($request->params['id'],'topics');

        if ($chapter) {
            if ($chapter->topics) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $chapter->topics
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