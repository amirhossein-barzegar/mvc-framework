<?php

namespace App\Controllers\Question;

use App\Models\Question\Question;
use App\Models\Question\QuestionBox;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class QuestionController
{
    /**
     * Get list of all Questions (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $questions = Question::all();
        if ($questions) {
            $response->setBody([
                'state' => 'success',
                'data' => $questions
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
     * Create new Question (POST)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function create(Request $request, Response $response): Response
    {
        $data = $request->body();
        if (count($data)) {
            foreach ($data as $field=>$value) {
                if (in_array($field,['options','categories','articles','question_boxes'])) continue;
                $data[$field] = htmlspecialchars(trim($value),ENT_QUOTES,'UTF-8');
            }
        }
        $formRequest = new FormRequest($data);
        $formRequest->rules = [
            'body' => 'required',
            'level' => 'required',
            'desc_answer' => 'optional',
            'status' => 'required|enum:enabled,disabled,deleted',
            'options' => 'required',
            'categories' => 'optional',
            'articles' => 'required',
            'question_boxes' => 'optional'
        ];

        // Validating Options
        if (isset($data['options']) && count($data['options'])) {
            foreach($data['options'] as $key => $article) {
                $optionsFormRequest = new FormRequest((array)$article);
                $optionsFormRequest->rules = [
                    'body' => 'required',
                    'is_answer' => 'required'
                ];
                if (!$optionsFormRequest->validate()) {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 1,
                        'message' => 'Invalid parameters passed for options!'
                    ]);
                    return $response;
                }
                $data['options'][$key] = $optionsFormRequest->getRequest();
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 4,
                'message' => 'Parameter options missing!'
            ]);
        }

        // Validating Articles
        if (isset($data['articles']) && count($data['articles'])) {
            foreach($data['articles'] as $key => $article) {
                $articleFormRequest = new FormRequest((array) $article);
                $articleFormRequest->rules = [
                    'article_id' => 'required',
                    'law_id' => 'required',
                    'section_id' => 'required',
                    'chapter_id' => 'required',
                    'topic_id' => 'required',
                ];
                if (!$articleFormRequest->validate()) {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 1,
                        'message' => 'Invalid parameters passed for articles!'
                    ]);
                    return $response;
                }
                $data['articles'][$key] = $articleFormRequest->getRequest();
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 4,
                'message' => 'Parameter articles missing!'
            ]);
        }

        // Validating Question Boxes
        if (isset($data['question_boxes']) && count($data['question_boxes'])) {
            foreach($data['question_boxes'] as $key => $article) {
                $questionBoxRequest = new FormRequest((array) $article);
                $questionBoxRequest->rules = [
                    'title' => 'required',
                    'description' => 'optional',
                ];
                if (!$questionBoxRequest->validate()) {
                    $response->setBody([
                        'state' => 'error',
                        'error_code' => 1,
                        'message' => 'Invalid parameters passed for question boxes!'
                    ]);
                    return $response;
                }
                $data['question_boxes'][$key] = $questionBoxRequest->getRequest();
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 4,
                'message' => 'Parameter articles missing!'
            ]);
        }

        // Validating Whole Question form
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest();
            // Creating the question
            $question = Question::create([
                'q_body' => $validatedData['body'],
                'q_level' => $validatedData['level'],
                'q_desc_answer' => $validatedData['desc_answer'],
                'q_status' => $validatedData['status'],
                'q_options' => json_encode($validatedData['options']),
                'q_created_at' => time(),
                'q_modified_at' => time()
            ]);

            if ($question instanceof Response) {
                return $question;
            } elseif ($question) {
                // Attaching Categories
                if (isset($validatedData['categories']) && count($validatedData['categories'])) {
                    foreach($validatedData['categories'] as $category) {
                        $attaching = $question->attach([
                            'cq_category_id' => $category,
                            'cq_question_id' => $question->getQId()
                        ],'category_question');
                        if ($attaching instanceof Response) {
                            return $attaching;
                        } elseif (!$attaching) {
                            $response->setBody([
                                'state' => 'error',
                                'error_code' => 32,
                                'message' => 'Something went\'s wrong on attaching for categories'
                            ]);
                            return $response;
                        }
                    }
                }
                // Inserting Question Boxes
                if (isset($validatedData['question_boxes']) && count($validatedData['question_boxes'])) {
                    foreach($validatedData['question_boxes'] as $question_box) {
                        $creating = QuestionBox::create([
                            'qb_title' => $question_box->title,
                            'qb_description' => $question_box->description,
                            'qb_question_id' => $question->getQId()
                        ]);
                        if ($creating instanceof Response) {
                            return $creating;
                        } elseif (!$creating) {
                            $response->setBody([
                                'state' => 'error',
                                'error_code' => 30,
                                'message' => 'Something went\'s wrong on creating for question boxes'
                            ]);
                            return $response;
                        }
                    }
                }
                // Attaching Articles
                foreach ($validatedData['articles'] as $article) {
                    $attaching = $question->attach([
                        'qa_question_id' => $question->getQId(),
                        'qa_article_id' => $article->article_id,
                        'qa_law_id' => $article->law_id,
                        'qa_section_id' => $article->section_id,
                        'qa_chapter_id' => $article->chapter_id,
                        'qa_topic_id' => $article->topic_id
                    ],'question_articles');
                    if ($attaching instanceof Response) {
                        return $attaching;
                    } elseif (!$attaching) {
                        $response->setBody([
                            'state' => 'error',
                            'error_code' => 32,
                            'message' => 'Something went\'s wrong on attaching for articles'
                        ]);
                        return $response;
                    }
                }

                $response->setBody([
                    'state' => 'success',
                    'message' => 'Question created successfully!',
                    'question_id' => $question->getQId()
                ]);
            } else {
                $response->setBody([
                    'state' => 'error',
                    'error_code' => 30,
                    'message' => 'Something went\'s wrong on creating Question!'
                ]);
            }
        } else {
            $response->setBody([
                'state' => 'error',
                'error_code' => 1,
                'message' => 'Invalid parameters passed for Questions! valid parameters are : body, level, desc_answer, status, options, categories, articles, question_boxes'
            ]);
        }
        return $response;
    }

    /**
     * Show specific Question (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $question = Question::findById($request->params['id']);
        if ($question) {
            $response->setBody([
                'state' => 'success',
                'data' => $question
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
     * Update specific Question (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $question = Question::findById($request->params['id']);
        if ($question) {
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
                'body' => 'optional|notnull',
                'level' => 'optional|notnull',
                'desc_answer' => 'optional|notnull',
                'status' => 'optional|notnull|enum:enabled,disabled,deleted',
                'options' => 'optional|notnull'
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('q_');
                $update = Question::update($question->getQId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update instanceof Question) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Question successfully updated!',
                        'question_id' => $update->getQId()
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
     * Delete specific Question (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = Question::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Question successfully deleted!',
                'question_id' => $deleted->getQId()
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
}