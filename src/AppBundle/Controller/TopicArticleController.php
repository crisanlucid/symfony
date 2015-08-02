<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Request\ParamFetcher;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use AppBundle\Entity\Topic;
use AppBundle\Form\TopicType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class TopicArticleController
 *
 * @package AppBundle\Controller
 *
 */

class TopicArticleController extends Controller
{
    /** @var EntityManager $em */
    private $em;

    /**
     * Find all Topics
     *
     * @ApiDoc(
     *      resource=true,
     *      section="Topic",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when the error."
     *      }
     * )
     *
     * @Get("/topics/list")
     *
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function getAction(ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:Topic')->findAll();
    }

     /**
     * Show a specific topic
     *
     * @ApiDoc(
     *      resource=true,
     *      section="Topic",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Get("/topics/{type_id}", requirements={"type_id" = "\d+"})
     *
     *
     * @param mixed $type_id
     *
     * @return array
     */
    public function getTopicAction($type_id)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:Topic')->findById($type_id);
    }

  
    /**
     * Create a topic
     * 
     * @ApiDoc(
     *      resource=true,
     *      section="Topic",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Post("/topics/create")
     *
     * @RequestParam(name="data", description="Topic model object data properties.", array=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        $data = $paramFetcher->get('data');
        $topicModel  = new Topic();
        $em = $this->getDoctrine()->getManager();

        // decode request
        $topicData = is_string($data) ? (array) @json_decode($data, true) : $data;
        $topicData['created'] = (new \DateTime())->format('d-M-y H:m:s');


        //save data
        $form = $this->createForm(new TopicType(), $topicModel);
        $form->submit($topicData);

        if ($form->isValid()) {
            $em->persist($topicModel);
            $em->flush();

            return new JsonResponse(
                [
                  'success' => 'OK'
                ],
                200
            );
        }

        return new JsonResponse(
            [
              'error' => (string) $form->getErrors(true, false)
            ],
            400
        );
    }

    /**
     * Update a topic
     * 
     * @ApiDoc(
     *      resource=true,
     *      section="Topic",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Put("/topics/{id}", requirements={"id" = "\d+"})
     *
     * @RequestParam(name="data", description="Topic model object data properties.", array=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function updateAction($id, ParamFetcher $paramFetcher)
    {
        $params['id'] = $id;
        $params = array_merge($paramFetcher->all(), $params);

        $data = $params['data'];

        $em = $this->getDoctrine()->getEntityManager();
        $topicObject = $em->getRepository('AppBundle:Topic')->find($id);

        $em = $this->getDoctrine()->getManager();

        if (!$topicObject) {
            return new JsonResponse(
                [
                  'error' => 'No topic found for id '.$id
                ],
                400
            );
        }


        // decode request
        $topicData = is_string($data) ? (array) @json_decode($data, true) : $data;
        $topicData['created'] = (new \DateTime())->format('d-M-y H:m:s');


        $form = $this->createForm(new TopicType(), $topicObject);
        $form->submit($topicData);

        //validate data and  update
        if ($form->isValid()) {

            $em->persist($topicObject);
            $em->flush();

            return new JsonResponse(
                [
                  'success' => 'OK'
                ],
                200
            );
        }

        return new JsonResponse(
            [
              'error' => (string) $form->getErrors(true, false)
            ],
            400
        );

    }

    /**
     * Delete Topic
     *
     * @ApiDoc(
     *      resource=true,
     *      section="Topic",
     *      statusCodes={
     *          202="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Delete("/topics/{id}")
     *
     * @param mixed $id
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function deleteAction($id, ParamFetcher $paramFetcher)
    {
        $params['id'] = $id;
        $params = array_merge($paramFetcher->all(), $params);

        $em = $this->getDoctrine()->getEntityManager();
        $topicData = $em->getRepository('AppBundle:Topic')->find($id);

        if (!$topicData) {
            return new JsonResponse(
                [
                  'error' => 'No topic found for id '.$id
                ],
                400
            );
        }

        $em->remove($topicData);
        $em->flush();

        return new JsonResponse(
            [
            'success' => 'OK'
            ],
            200
        );
    }
}
