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
use AppBundle\Entity\Article;
use AppBundle\Form\ArticleType;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class ArticleController
 *
 * @package AppBundle\Controller
 *
 */

// Create an article for a topic
// Delete an article
// List all articles from a topic
// Show a specific article

class ArticleController extends Controller
{
   /** @var EntityManager $em */
    private $em;

    /**
     * List all articles from a topic
     *
     * @ApiDoc(
     *      resource=true,
     *      section="Article",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when the error."
     *      }
     * )
     *
     * @Get("/topics/{id}/articles/list", requirements={"id" = "\d+"})
     *
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function getAction($id, ParamFetcher $paramFetcher)
    {
        $em = $this->getDoctrine()->getManager();

        $params['id'] = $id;
        $params = array_merge($paramFetcher->all(), $params);
		
		$topicObject = $em->getRepository('AppBundle:Topic')->find($id);

		if (!$topicObject) {
			return new JsonResponse(
				[
				  'error' => 'No topic found for id '.$id
				],
				400
			);
		}


    	$qb = $em->createQueryBuilder();
        $qb->select('e')
           ->from('AppBundle:Article', 'e')
           ->innerJoin('e.topic', 'u')
           ->where('e.topic = ?1');
  		
  		$qb->setParameter(1, $topicObject->getId());

		return $qb->getQuery()->getResult();
    }

     /**
     * Show a specific article
     *
     * @ApiDoc(
     *      resource=true,
     *      section="article",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Get("/topics/articles/{type_id}", requirements={"type_id" = "\d+"})
     *
     *
     * @param mixed $type_id
     *
     * @return array
     */
    public function getArticleAction($type_id)
    {
        $em = $this->getDoctrine()->getManager();

        return $em->getRepository('AppBundle:Article')->findById($type_id);


    }

  
    /**
     * Create an article for a topic
     * 
     * @ApiDoc(
     *      resource=true,
     *      section="Article",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Post("/topics/articles/create")
     *
     * @RequestParam(name="data", description="Article model object data properties.", array=true)
     *
     * @param ParamFetcher $paramFetcher
     *
     * @return array
     */
    public function postAction(ParamFetcher $paramFetcher)
    {
        $data = $paramFetcher->get('data');
        $articleModel  = new Article();
        $em = $this->getDoctrine()->getManager();

        // decode request
        $articleData = is_string($data) ? (array) @json_decode($data, true) : $data;
        $articleData['created'] = (new \DateTime())->format('d-M-y H:m:s');

		$topicObject = $em->getRepository('AppBundle:Topic')->find($data['topic_id']);
		unset($articleData['topic_id']);

		if (!$topicObject) {
			return new JsonResponse(
				[
				  'error' => 'No topic found for id '.$id
				],
				400
			);
		}


        //save data and validate
        $form = $this->createForm(new ArticleType(), $articleModel);
        $form->submit($articleData);

        if ($form->isValid()) {
        	$articleModel->setTopic($topicObject);
            $em->persist($articleModel);
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
     * Update a specific article
     * 
     * @ApiDoc(
     *      resource=true,
     *      section="article",
     *      statusCodes={
     *          200="Returned when successful.",
     *          401="Returned when exist an error."
     *      }
     * )
     *
     * @Put("/topics/articles/{id}", requirements={"id" = "\d+"})
     *
     * @RequestParam(name="data", description="Article model object data properties.", array=true)
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
        $articleObject = $em->getRepository('AppBundle:Article')->find($id);

        $em = $this->getDoctrine()->getManager();

        if (!$articleObject) {
            return new JsonResponse(
                [
                  'error' => 'No article found for id '.$id
                ],
                400
            );
        }


        // decode request
        $articleData = is_string($data) ? (array) @json_decode($data, true) : $data;
        $articleData['created'] = (new \DateTime())->format('d-M-y H:m:s');
        unset($articleData['topic_id']);


        $form = $this->createForm(new ArticleType(), $articleObject);
        $form->submit($articleData);

        //validate data and  update
        if ($form->isValid()) {

            $em->persist($articleObject);
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
     * Delete Article
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
     * @Delete("/topics/articles/{id}")
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
        $topicData = $em->getRepository('AppBundle:Article')->find($id);

        if (!$topicData) {
            return new JsonResponse(
                [
                  'error' => 'No article found for id '.$id
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