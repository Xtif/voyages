<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ErrorController extends Controller
{
    public function accessDeniedAction(Request $request)
    {
	    return $this->render('default/access_denied.html.twig');
	} //End accessDeniedAction
	       
} //End class
