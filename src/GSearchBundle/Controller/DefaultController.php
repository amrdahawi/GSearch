<?php

namespace GSearchBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use GSearchBundle\Service\APIClient;
use GSearchBundle\Service\Search;
use GSearchBundle\Validator\SearchValidator;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        $errors = new ArrayCollection();
        // create form
        $form = $this->createFormBuilder()
            ->add('keyword', TextType::class)
            ->add('url', TextType::class)
            ->add('save', SubmitType::class, array('label' => 'Submit'))
            ->getForm();

        $form->handleRequest($request);

        // check if form is submitted
        if ($form->isSubmitted()) {
            // get form data
            $data = $form->getData();

            // get keywords and urls from Form
            $keywords = explode(',', $data['keyword']);
            $urls = explode(',', $data['url']);

            /** @var SearchValidator $searchValidator */
            $searchValidator = $this->container->get('Search_validator');

            // validate input
            if ($searchValidator->isValid($keywords, $urls)) {
                /** @var Search $searchService */
                $searchService = $this->container->get('search');
                try {
                    // search google and return results
                    $results = $searchService->multiSearch($keywords, $urls);
                    return $this->render('@GSearch/Default/result.html.twig', array(
                        'results' => $results,
                    ));
                } catch (\Exception $e) {
                    $errors->add($e->getMessage());
                }
            } else {
                $errors = $searchValidator->getErrors();
            }
        }

        // render
        return $this->render('@GSearch/Default/index.html.twig', array(
            'form' => $form->createView(),
            'errors' => $errors
        ));
    }
}
