<?php

namespace App\Controllers\Article;

use App\Models\Article\Article;
use App\Models\Article\Chapter;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class ArticleController
{
    /**
     * Get list of all Articles (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $articles = Article::all();
        if ($articles) {
            $response->setBody([
                'state' => 'success',
                'data' => $articles
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
     * Create new Article (POST)
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
            'name' => 'required',
            'description' => 'optional',
            'reference_name' => 'optional',
            'tags' => 'optional',
            'topic_id' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest();
            $article = Article::create([
                'a_name' => $validatedData['name'],
                'a_description' => $validatedData['description'],
                'a_reference_name' => $validatedData['reference_name'],
                'a_tags' => $validatedData['tags'],
                'a_topic_id' => $validatedData['topic_id'],
                'a_created_at' => time()
            ]);
            if ($article instanceof Response) {
                return $article;
            } elseif ($article) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Section created successfully!',
                    'article' => $article->getAId()
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
     * Show specific article (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $article = Article::findById($request->params['id']);
        if ($article) {
            $response->setBody([
                'state' => 'success',
                'data' => $article
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
     * Update specific Article (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $article = Article::findById($request->params['id']);
        if ($article) {
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
                'name' => 'optional|notnull',
                'description' => 'optional|notnull',
                'reference_name' => 'optional|notnull',
                'tags' => 'optional|notnull',
                'topic_id' => 'optional|notnull',
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('a_');
                $update = Article::update($article->getAId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Article successfully updated!',
                        'article_id' => $update->getAId()
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
     * Delete specific Article (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = Article::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Article successfully deleted!',
                'article_id' => $deleted->getAId()
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

    public function getTopic(Request $request, Response $response): Response
    {
        $article = Article::findById($request->params['id'],'topic');

        if ($article) {
            if ($article->topic) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $article->topic
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