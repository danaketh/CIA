<?php

namespace danaketh\CIBundle\Controller;

use danaketh\CIBundle\Entity\Build;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class BuildController
 *
 * @package danaketh\CIBundle\Controller
 */
class BuildController extends Controller
{
    /**
     * @param Request $request
     * @param         $project
     * @param         $id
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function summaryAction(Request $request, $project, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository('danakethCIBundle:Project')->findOneBy(array(
                'token' => $project
            ));
        $build = $entityManager->getRepository('danakethCIBundle:Build')->findOneBy(array(
                'project' => $project,
                'build' => $id
            ));

        if ($project === null || $build === null) {
            throw $this->createNotFoundException('Requested build does not exist!');
        }

        return $this->render('danakethCIBundle:Build/detail:summary.html.twig', array(
                'project' => $project,
                'build' => $build,
                'plugins' => $this->container->getParameter('danaketh_ci.plugins')
            ));
    }

    /**
     * @param Request $request
     * @param string  $project
     * @param integer $id
     * @param string  $plugin
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function detailAction(Request $request, $project, $id, $plugin)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository('danakethCIBundle:Project')->findOneBy(array(
                'token' => $project
            ));
        $build = $entityManager->getRepository('danakethCIBundle:Build')->findOneBy(array(
                'project' => $project,
                'build' => $id
            ));

        if ($project === null || $build === null) {
            throw $this->createNotFoundException('Requested build does not exist!');
        }

        return $this->render('danakethCIBundle:Build:detail/'.$plugin.'.html.twig', array(
                'project' => $project,
                'build' => $build,
                'plugins' => $this->container->getParameter('danaketh_ci.plugins')
            ));
    }
}
