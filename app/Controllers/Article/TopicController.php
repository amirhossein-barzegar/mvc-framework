<?php

namespace App\Controllers\Article;

use App\Models\Article\Topic;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class TopicController
{
    /**
     * Get list of all Topics (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $topics = Topic::all();
        if ($topics) {
            $response->setBody([
                'state' => 'success',
                'data' => $topics
            ]);
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 5,
                'message' => 'No records found!'
            ]);
        }        return $response;
    }

    /**
     * Create new Topic (POST)
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
            'chapter_id' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest('t_');
            $topic = Topic::create($validatedData);
            if ($topic instanceof Response) {
                return $topic;
            } elseif ($topic) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Topic created successfully!',
                    'topic_id' => $topic->getTId()
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
     * Show specific topic (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $topic = Topic::findById($request->params['id']);
        if ($topic) {
            $response->setBody([
                'state' => 'success',
                'data' => $topic
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
     * Update specific Topic (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $topic = Topic::findById($request->params['id']);
        if ($topic) {
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
                'chapter_id' => 'optional|notnull'
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('t_');
                $update = Topic::update($topic->getTId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Topic successfully updated!',
                        'topic_id' => $update->getTId()
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
     * Delete specific Topic (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = Topic::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Topic successfully deleted!',
                'section_id' => $deleted->getTId()
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

    public function getChapter(Request $request, Response $response): Response
    {
        $topic = Topic::findById($request->params['id'],'chapter');
        if ($topic) {
            if ($topic->chapter) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $topic->chapter
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

    public function getArticles(Request $request, Response $response): Response
    {
        $topic = Topic::findById($request->params['id'],'articles');
        if ($topic) {
            if ($topic->articles) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $topic->articles
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