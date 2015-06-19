<?php
namespace Payum\LaravelPackage\Controller;

use Payum\Core\Request\Authorize;
use Symfony\Component\HttpFoundation\Request;

class AuthorizeController extends PayumController
{
    public function doAction($payum_token)
    {
        /** @var Request $request */
        $request = \App::make('request');
        $request->attributes->set('payum_token', $payum_token);

        $token = $this->getHttpRequestVerifier()->verify($request);

        $gateway = $this->getPayum()->getGateway($token->getGatewayName());

        $response = $this->convertReply($gateway->execute(new Authorize($token), true));

        if($response) {
            return $response;
        }

        $this->getHttpRequestVerifier()->invalidate($token);

        return \Redirect::to($token->getAfterUrl());
    }
}