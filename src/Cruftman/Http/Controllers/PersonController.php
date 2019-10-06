<?php
/**
 * @file src/Cruftman/Http/Controllers/PersonController.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Http\Controllers;

use Cruftman\Models\Person;
use Cruftman\Transformers\PersonTransformer;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Person controller.
 */
class PersonController extends Controller
{
    /**
     * Get a single person from database.
     *
     * @param int $id
     * @return Person
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @OA\Get(
     *  path="/person/{id}",
     *  summary="Get a person with given ID",
     *  description="Returns a single person",
     *  operationId="getPersonById",
     *  @OA\Parameter(
     *      name="id",
     *      in="path",
     *      description="ID of person to return",
     *      required=true,
     *      @OA\Schema(
     *          type="integer",
     *          format="int64"
     *      )
     *  ),
     *  @OA\Response(
     *      response=200,
     *      description="successful operation",
     *      @OA\JsonContent(ref="#/components/schemas/Person"),
     *      @OA\XmlContent(ref="#/components/schemas/Person"),
     *  ),
     *  @OA\Response(
     *      response=404,
     *      description="Person not found",
     *      @OA\JsonContent(ref="#/components/schemas/HttpException"),
     *      @OA\XmlContent(ref="#/components/schemas/HttpException"),
     *  ),
     *  security={
     *      {"api_key": {}}
     *  }
     * )
     */
    public function show($id)
    {
        if (($person = Person::find($id)) == null) {
            return $this->response->errorNotFound(__('error.not_found'));
        }
        return $this->response->item($person, new PersonTransformer, ['key' => 'person']);
    }
}

// vim: syntax=php sw=4 ts=4 et:
