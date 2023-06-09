<?php

namespace App\Controllers\Article;

use App\Controllers\BaseController;
use App\Models\Article\Section;
use App\Requests\FormRequest;
use App\Requests\Request;
use App\Responses\Response;

class SectionController extends BaseController
{
    /**
     * Get list of all Sections (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function all(Request $request, Response $response): Response
    {
        $sections = Section::all();
        if ($sections) {
            $response->setBody([
                'state' => 'success',
                'data' => $sections
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
     * Create new Section (POST)
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
            'law_id' => 'required'
        ];
        if ($formRequest->validate()) {
            $validatedData = $formRequest->getRequest('s_');
            $section = Section::create($validatedData);
            if ($section instanceof Response) {
                return $section;
            } elseif ($section) {
                $response->setBody([
                    'state' => 'success',
                    'message' => 'Section created successfully!',
                    'section_id' => $section->getSId()
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
     * Show specific section (GET)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function show(Request $request, Response $response): Response
    {
        $section = Section::findById($request->params['id']);
        if ($section) {
            $response->setBody([
                'state' => 'success',
                'data' => $section
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
     * Update specific section (PUT)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function update(Request $request, Response $response): Response
    {
        $section = Section::findById($request->params['id']);
        if ($section) {
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
                'law_id' => 'optional|notnull'
            ];
            if ($formRequest->validate()) {
                $validatedData = $formRequest->getRequest('s_');
                $update = Section::update($section->getSId(), $validatedData);
                if ($update instanceof Response) {
                    return $update;
                } else if ($update) {
                    $response->setBody([
                        'state' => 'success',
                        'message' => 'Section successfully updated!',
                        'section_id' => $update->getSId()
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
     * Delete specific section (DELETE)
     * @param Request $request
     * @param Response $response
     * @return Response
     */
    public function delete(Request $request, Response $response): Response
    {
        $deleted = Section::delete($request->params['id']);
        if ($deleted instanceof Response) {
            return $deleted;
        } elseif ($deleted) {
            $response->setBody([
                'state' => 'success',
                'message' => 'Section successfully deleted!',
                'section_id' => $deleted->getSId()
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

    public function getLawCollection(Request $request, Response $response): Response
    {
        $section = Section::findById($request->params['id'],'lawCollection');
        if ($section) {
            if ($section->lawCollection) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $section->lawCollection
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

    public function getChapters(Request $request, Response $response): Response
    {
        $section = Section::findById($request->params['id'],'chapters');
        if ($section) {
            if ($section->chapters) {
                $response->setBody([
                    'state' => 'success',
                    'data' => $section->chapters
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