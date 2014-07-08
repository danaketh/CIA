<?php

namespace danaketh\CIBundle\Controller;

use danaketh\CIBundle\Entity\Build;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class DefaultController
 *
 * @package danaketh\CIBundle\Controller
 */
class DefaultController extends Controller
{
    /**
     * @param Request $request
     *
     * @return Response
     */
    public function indexAction(Request $request)
    {
        return new Response('Nothing to see here :)');
    }

    /**
     * @param Request $request
     * @param         $id
     *
     * @return JsonResponse
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function webhookAction(Request $request, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository('danakethCIBundle:Project')->find($id);

        if ($project === null) {
            throw $this->createNotFoundException('Requested project does not exist!');
        }

        $payload = json_decode($request->get('payload'), true);

        foreach ($payload['commits'] as $commit) {
            $build = new Build();
            $build->setProject($project);
            $build->setBuild($project->getBuilds()->count() + 1);
            $build->setBranch(
                isset($commit['branch'])
                ? $commit['branch']
                : 'master'
            );
            if (preg_match('~(.*?) <(.*?)>~', $commit['raw_author'], $matches)) {
                $build->setCommitAuthor($matches[1]);
                $build->setCommitEmail($matches[2]);
            }
            $build->setCommitHash($commit['raw_node']);
            $build->setCommitMessage($commit['message']);
            $build->setStatus(Build::PENDING);
            $build->setCreated(new \DateTime("now"));
            $entityManager->persist($build);
            $entityManager->flush();
        }

        return new JsonResponse(array(
            'response' => 'OK'
        ));
    }

    /**
     * @param Request $request
     * @param         $project
     * @param         $id
     *
     * @return Response
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function buildAction(Request $request, $project, $id)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository('danakethCIBundle:Project')->findOneBy(array(
                'token' => $project
            ));
        $build = $entityManager->getRepository('danakethCIBundle:Build')->findOneBy(array(
                'project' => $project,
                'id' => $id
            ));

        if ($project === null || $build === null) {
            throw $this->createNotFoundException('Requested build does not exist!');
        }

        return $this->render('danakethCIBundle:Default:build.html.twig', array(
                'project' => $project,
                'build' => $build,
                'plugins' => $this->container->getParameter('danaketh_ci.plugins')
            ));
    }

    public function projectAction(Request $request, $project)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $project = $entityManager->getRepository('danakethCIBundle:Project')->findOneBy(array(
                'token' => $project
            ));

        if ($project === null) {
            throw $this->createNotFoundException('Requested project does not exist!');
        }

        return $this->render('danakethCIBundle:Default:project.html.twig', array(
                'project' => $project,
            ));
    }
}
