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
     *          401="Returned when the error."
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
     *          401="Returned when the user is not authorized."
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
    	// $data = $paramFetcher->get('data');
    	$data = $paramFetcher->all();
        // decode request
       // $data = is_string($data) ? (array) @json_decode($data, true) : $data;

        var_dump($data);
    }

}
