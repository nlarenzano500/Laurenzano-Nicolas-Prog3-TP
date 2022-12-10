<?php


class MWparaCORS {
	/**
   * @api {any} /HabilitarCORSTodos/  HabilitarCORSTodos
   * @apiVersion 0.1.0
   * @apiName HabilitarCORSTodos
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare habilito que se pueda acceder desde cualquier servidor
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
 * @apiParam {ResponseInterface} response El objeto RESPONSE.
 * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *   ->add(\verificador::class . ':HabilitarCORSTodos')
   */
  // public function HabilitarCORSTodos($request, $response, $next) {
	public function HabilitarCORSTodos($request, $handler) {

    $response = $handler->handle($request);
    //solo afecto la salida con los header
    return $response
      ->withHeader('Access-Control-Allow-Origin', '*')
      ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
      ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}

	/**
   * @api {any} /HabilitarCORS80/  HabilitarCORS80
   * @apiVersion 0.1.0
   * @apiName HabilitarCORS80
   * @apiGroup MIDDLEWARE
   * @apiDescription  Por medio de este MiddleWare habilito que se pueda acceder desde http://localhost:80
   *
   * @apiParam {ServerRequestInterface} request  El objeto REQUEST.
   * @apiParam {ResponseInterface} response El objeto RESPONSE.
   * @apiParam {Callable} next  The next middleware callable.
   *
   * @apiExample Como usarlo:
   *   ->add(\verificador::class . ':HabilitarCORS80')
   */
  // public function HabilitarCORS80($request, $response, $next) {
	public function HabilitarCORS80($request, $handler) {

      $response = $handler->handle($request);
      return $response
            ->withHeader('Access-Control-Allow-Origin', 'http://localhost:80')
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
	}
}
