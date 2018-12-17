<?php

use Symfony\Component\Routing\Matcher\Dumper\PhpMatcherTrait;
use Symfony\Component\Routing\RequestContext;

/**
 * This class has been auto-generated
 * by the Symfony Routing Component.
 */
class srcApp_KernelDevDebugContainerUrlMatcher extends Symfony\Bundle\FrameworkBundle\Routing\RedirectableUrlMatcher
{
    use PhpMatcherTrait;

    public function __construct(RequestContext $context)
    {
        $this->context = $context;
        $this->staticRoutes = array(
            '/sa/departamentos' => array(array(array('_route' => 'geDepartamentos', '_controller' => 'App\\Controller\\LugarController::geDepartamentos'), null, array('POST' => 0), null, false, null)),
            '/sa/municipios' => array(array(array('_route' => 'geMunicipios', '_controller' => 'App\\Controller\\LugarController::geMunicipios'), null, array('POST' => 0), null, false, null)),
            '/sa/tipolugar' => array(array(array('_route' => 'geTipoLugar', '_controller' => 'App\\Controller\\LugarController::geTipoLugar'), null, null, null, false, null)),
        );
    }
}
