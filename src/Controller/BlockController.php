<?php

namespace App\Controller;

use App\Exception\BlockNotFoundException;
use App\Navcoin\Block\Api\BlockApi;
use App\Navcoin\Block\Api\TransactionApi;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use JMS\Serializer\SerializerInterface;

/**
 * Class BlockController
 *
 * @package App\Controller
 */
class BlockController extends Controller
{
    /**
     * @var int
     */
    private $pageSize = 10;

    /**
     * @var BlockApi
     */
    private $blockApi;

    /**
     * @var TransactionApi
     */
    private $transactionApi;

    /**
     * Constructor
     *
     * @param BlockApi       $blockApi
     * @param TransactionApi $transactionApi
     */
    public function __construct(BlockApi $blockApi, TransactionApi $transactionApi)
    {
        $this->blockApi = $blockApi;
        $this->transactionApi = $transactionApi;
    }

    /**
     * @Route("/blocks")
     * @Template()
     *
     * @return array
     */
    public function index(): array
    {
        return [];
    }

    /**
     * @Route("/blocks.json")
     *
     *
     * @param Request             $request
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function blocks(Request $request, SerializerInterface $serializer): Response
    {
        $blocks = $this->blockApi->getBlocks(
            $request->get('size', $this->pageSize),
            $request->get('from', null),
            $request->get('to', null)
        );

        return new Response($serializer->serialize($blocks, 'json'));
    }

    /**
     * @Route("/block/{height}")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function view(Request $request): Response
    {
        try {
            $block = $this->blockApi->getBlock($request->get('height'));
        } catch (BlockNotFoundException $e) {
            return $this->render('block/not_found.html.twig', [
                'height' => $request->get('height'),
            ]);
        }

        return $this->render('block/view.html.twig', [
            'block' => $block,
        ]);
    }

    /**
     * @Route("/block/{height}/tx.json")
     *
     *
     * @param Request             $request
     * @param SerializerInterface $serializer
     *
     * @return Response
     */
    public function transactions(Request $request, SerializerInterface $serializer): Response
    {
        $transactions = $this->transactionApi->getTransactionsForBlock($request->get('height'));

        return new Response($serializer->serialize($transactions, 'json'));
    }
}
