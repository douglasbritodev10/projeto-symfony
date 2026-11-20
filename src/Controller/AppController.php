<?php

namespace App\Controller;

use App\Entity\Produto;
use App\Repository\ProdutoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AppController extends AbstractController
{
    #[Route('/app', name: 'app', methods: ['GET'])]
    public function index(ProdutoRepository $produtoRepository): Response
    {
        return $this->render('app/index.html.twig', [
            'produtos' => $produtoRepository->findAll(),
        ]);
    }

    #[Route('/app/estoque/{id}/aumentar', name: 'app_estoque_aumentar', methods: ['POST'])]
    public function aumentarEstoque(Request $request, Produto $produto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('aumentar' . $produto->getId(), $request->getPayload()->getString('_token'))) {
            $quantidade = max(1, (int) $request->getPayload()->getInt('quantidade', 1));
            $estoqueAtual = $produto->getEstoque() ?? 0;

            $produto->setEstoque($estoqueAtual + $quantidade);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Adicionado +' . $quantidade . ' em "' . $produto->getNome() . '". Estoque atual: ' . $produto->getEstoque() . '.'
            );
        }

        return $this->redirectToRoute('app', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/app/estoque/{id}/diminuir', name: 'app_estoque_diminuir', methods: ['POST'])]
    public function diminuirEstoque(Request $request, Produto $produto, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('diminuir' . $produto->getId(), $request->getPayload()->getString('_token'))) {
            $quantidade = max(1, (int) $request->getPayload()->getInt('quantidade', 1));
            $estoqueAtual = $produto->getEstoque() ?? 0;

            if ($estoqueAtual <= 0) {
                $this->addFlash('warning', 'O estoque de "' . $produto->getNome() . '" já está zerado.');
            } elseif ($quantidade > $estoqueAtual) {
                $this->addFlash(
                    'danger',
                    'Não é possível baixar ' . $quantidade . ' de "' . $produto->getNome() . '". Estoque disponível: ' . $estoqueAtual . '.'
                );
            } else {
                $produto->setEstoque($estoqueAtual - $quantidade);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'Baixa de -' . $quantidade . ' realizada em "' . $produto->getNome() . '". Estoque atual: ' . $produto->getEstoque() . '.'
                );
            }
        }

        return $this->redirectToRoute('app', [], Response::HTTP_SEE_OTHER);
    }
}