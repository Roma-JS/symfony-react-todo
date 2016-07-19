<?php

namespace RestBundle\Controller;

use AppBundle\Entity\Todo;
use AppBundle\Repository\TodoRepository;

use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations;
use FOS\RestBundle\Request\ParamFetcherInterface;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use RestBundle\Exception\InvalidFormException;
use RestBundle\Form\TodoType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class TodoController extends BaseController
{
    /**
     * REST action which returns list of todos.
     * Method: GET, url: /api/todos.{_format}
     *
     * @Annotations\QueryParam(name="_page", requirements="\d+", default=1, nullable=true, description="Page number")
     * @Annotations\QueryParam(name="_perPage", requirements="\d+", default=30, nullable=true, description="Limit")
     * @Annotations\QueryParam(name="_sortField", nullable=true, description="Sort field")
     * @Annotations\QueryParam(name="_sortDir", nullable=true, description="Sort direction")
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets todos",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
     * @param ParamFetcherInterface $paramFetcher
     * @return array
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $sortField = $paramFetcher->get('_sortField');
        $sortDir = $paramFetcher->get('_sortDir');
        $page = $paramFetcher->get('_page');
        $limit = $paramFetcher->get('_perPage');

        $orderBy = null;

        if ($sortField && $sortDir) {
            $orderBy = [
                $sortField => $sortDir
            ];
        }

        $offset = ($page - 1) * $limit;

        $repository = $this->getDoctrine()->getRepository('AppBundle:Todo');

        return $repository->findBy([], $orderBy, $limit, $offset);
    }

    /**
     * REST action which returns author by id.
     * Method: GET, url: /api/todos/{id}.{_format}
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Gets a Todo for a given id",
     *   output = "AppBundle\Entity\Todo",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the page is not found"
     *   }
     * )
     *
     * @param $id
     * @return mixed
     */
    public function getAction($id)
    {
        /** @var TodoRepository $todoRepository */
        $todoRepository = $this->getDoctrine()->getRepository('AppBundle:Todo');
        $todo = null;

        try {
            $todo = $todoRepository->find($id);
        } catch (\Exception $exception) {
            $todo = null;
        }

        if (!$todo) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $todo;
    }

    /**
     * Create a Todo from the submitted data.
     *
     * @ApiDoc(
     *     resource = true,
     *     description = "Creates a new author from the submitted data.",
     *     input = {
     *         "class" = "RestBundle\Form\TodoType",
     *         "name" = ""
     *     },
     *     output = "AppBundle\Entity\Todo",
     *     statusCodes = {
     *         200 = "Returned when successful",
     *         400 = "Returned when the form has errors"
     *     }
     * )
     *
     * @param Request $request the request object
     *
     * @return FormTypeInterface|View
     */
    public function postAction(Request $request)
    {
        try {
            $persistedType = $this->createNewTodo($request);

            $routeOptions = [
                'id' => $persistedType->getId(),
                '_format' => $request->get('_format')
            ];

            return $this->routeRedirectView('api_get_todo', $routeOptions, Response::HTTP_CREATED);

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        } catch (\Exception $exception) {
            $this->throwSupportedException($exception);
        }
    }

    /**
     * Creates new author from request parameters and persists it.
     *
     * @param Request $request
     * @return Todo - persisted author
     */
    protected function createNewTodo(Request $request)
    {
        $todo = new Todo();
        $persistedTodo = $this->processForm($todo, $request, 'POST');

        return $persistedTodo;
    }

    /**
     * Processes the form.
     *
     * @param Todo $todo
     * @param Request $request
     * @param String $method
     * @return Todo|static
     */
    private function processForm(Todo $todo, Request $request, $method = 'PUT')
    {
        $form = $this->createForm(TodoType::class, $todo, ['method' => $method]);
        $form->submit($request->request->all(), 'PATCH' !== $method);

        if ($form->isValid()) {
            /** @var Todo $todo */
            $todo = $form->getData();

            /** @var TodoRepository $todoRepository */
            $todoRepository = $this->getDoctrine()->getRepository('AppBundle:Todo');

            return $todoRepository->persistTodo($todo);
        }

        throw new InvalidFormException('Invalid submitted data', $form);
    }

    /**
     * Update existing author from the submitted data or create a new author.
     * All required fields must be set within request data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = {
     *     "class" = "RestBundle\Form\TodoType",
     *     "name" = ""
     *   },
     *   output = "AppBundle\Entity\Todo",
     *   statusCodes = {
     *     201 = "Returned when the Todo is created",
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int $id the author id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when author not exist
     */
    public function putAction(Request $request, $id)
    {
        try {
            /** @var TodoRepository $todoRepository */
            $todoRepository = $this->getDoctrine()->getRepository('AppBundle:Todo');

            /** @var Todo $todo */
            $todo = $todoRepository->find($id);

            if (!$todo) {
                $statusCode = Response::HTTP_CREATED;
                $todo = $this->createNewTodo($request);
            } else {
                $statusCode = Response::HTTP_NO_CONTENT;
                $todo = $this->processForm($todo, $request, 'PUT');
            }

            $routeOptions = [
                'id' => $todo->getId(),
                '_format' => $request->get('_format')
            ];

            return $this->routeRedirectView('api_get_todo', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        } catch (\Exception $exception) {
            $this->throwSupportedException($exception);
        }
    }

    /**
     * REST action which deletes author by id.
     * Method: DELETE, url: /api/todos/{id}.{_format}
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Deletes a Todo for a given id",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     401 = "Returned when not authenticated",
     *     403 = "Returned when not having permissions",
     *     404 = "Returned when the author is not found"
     *   }
     * )
     *
     * @param $id
     * @return mixed
     */
    public function deleteAction($id)
    {
        /** @var TodoRepository $todoRepository */
        $todoRepository = $this->getDoctrine()->getRepository('AppBundle:Todo');
        /** @var Todo $todo */
        $todo = $todoRepository->find($id);

        if ($todo) {
            try {
                $todoRepository->deleteTodo($todo);
            } catch (\Exception $exception) {
                $this->throwSupportedException($exception);
            }
        } else {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }
    }

    /**
     * Update existing author from the submitted data.
     *
     * @ApiDoc(
     *   resource = true,
     *   input = {
     *     "class" = "RestBundle\Form\TodoType",
     *     "name" = ""
     *   },
     *   output = "AppBundle\Entity\Todo",
     *   statusCodes = {
     *     204 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param Request $request the request object
     * @param int $id the author id
     *
     * @return FormTypeInterface|View
     *
     * @throws NotFoundHttpException when author does not exist
     */
    public function patchAction(Request $request, $id)
    {
        try {
            /** @var Todo $todo */
            $todo = $this->getDoctrine()->getRepository('AppBundle:Todo')->find($id);

            if (!$todo) {
                throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
            }
            $statusCode = Response::HTTP_NO_CONTENT;
            $todo = $this->processForm($todo, $request, 'PATCH');

            $routeOptions = [
                'id' => $todo->getId(),
                '_format' => $request->get('_format')
            ];

            return $this->routeRedirectView('api_get_todo', $routeOptions, $statusCode);

        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        } catch (\Exception $exception) {
            $this->throwSupportedException($exception);
        }
    }
}
