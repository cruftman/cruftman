<?php
/**
 * @file src/Cruftman/api.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

/**
 * @OA\OpenApi(
 *  @OA\Info(
 *      version="1.0.0",
 *      title="Cruftman",
 *      description="Cruftman",
 *      @OA\Contact(
 *          email="ptomulik@meil.pw.edu.pl"
 *      ),
 *      @OA\License(
 *          name="MIT",
 *          url="https://opensource.org/licenses/MIT"
 *      ),
 *  ),
 *  @OA\Server(
 *      url="/api"
 *  )
 * )
 */

/**
 *  @OA\Schema(
 *      schema="HttpException",
 *      required={"status_code", "message"},
 *      @OA\Property(property="status_code", type="integer", example="404"),
 *      @OA\Property(property="message", type="string", example="Not found"),
 *      @OA\Property(property="debug", ref="#/components/schemas/HttpDebug")
 *  )
 */

/**
 *  @OA\Schema(
 *      schema="HttpDebug",
 *      required={"line", "file", "class", "trace"},
 *      @OA\Property(property="line", type="integer", example=123),
 *      @OA\Property(property="file", type="string", example="src/Cruftman/Http/Controllers/PersonController.php"),
 *      @OA\Property(property="class", type="string", example="NotFoundHttpException"),
 *      @OA\Property(property="trace", ref="#/components/schemas/ExceptionDebugTrace")
 *  )
 */

/**
 *  @OA\Schema(
 *      schema="ExceptionDebugTrace",
 *      oneOf={
 *          @OA\Schema(
 *              @OA\Property(property="previous", type="array", @OA\Items(type="string")),
 *              @OA\Property(property="current", type="array", @OA\Items(type="string"))
 *          ),
 *          @OA\Schema(type="array", @OA\Items(type="string"))
 *      }
 *  )
 */

// vim: syntax=php sw=4 ts=4 et:
