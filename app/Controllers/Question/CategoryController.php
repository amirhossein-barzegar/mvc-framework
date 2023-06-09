<?php

namespace App\Controllers\Question;

use App\Models\Question\Category;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class CategoryController
{
    /**
     * Get list of all Categories (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $categories = Category::all();
        if ($categories) {
            $response->setBody([
                'state' => 'success',
                'data' => $categories
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
     * Create new Category (POST)
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
            'description' => 'optional'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest();
            $category = Category::create([
                'ct_name' => $validatedData['name'],
                'ct_description' => $validatedData['description'],
                'ct_created_at' => time(),
                'ct_modified_at' => time()
            ]);
            if ($category instanceof Response) {
                return $category;
            } elseif ($category) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Category created successfully!',
                    'category_id' => $category->getCtId()
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
     * Show specific Category (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $category = Category::findById($request->params['id']);
        if ($category) {
            $response->setBody([
                'state' => 'success',
                'data' => $category
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
     * Update specific Category (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $category = Category::findById($request->params['id']);
        if ($category) {
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
                'description' => 'optional'
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('ct_');
                $update = Category::update($category->getCtId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } elseif ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Category successfully updated!',
                        'category_id' => $update->getCtId()
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
     * Delete specific Category (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = Category::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Category successfully deleted!',
                'category_id' => $deleted->getCtId()
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