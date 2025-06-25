<?php

namespace App\Controller;

use App\Dto\UsuarioDto;
use App\Entity\Usuario;
use App\Repository\UsuarioRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api')]
final class UsuarioController extends AbstractController
{
    #[Route('/usuarios', name: 'usuarios_criar', methods: ['POST'])]
    public function criar(
        #[MapRequestPayload(acceptFormat:'json')]
        UsuarioDto $usuarioDto,

        EntityManagerInterface $entityManager,
        UsuarioRepository $usuarioRepository
    ): JsonResponse
    {
        $erros = [];
        //Validar os dados do DTO
        if (!$usuarioDto->getCpf()){
            array_push($erros,[
                'message'=>'CPF é obrigatório!'
            ]);
        }
        if (!$usuarioDto->getNome()){
            array_push($erros,[
                'message'=>'Nome é obrigatório!'
            ]);
        }
        if (!$usuarioDto->getEmail()){
            array_push($erros,[
                'message'=>'Email é obrigatório!'
            ]);
        }
        if (!$usuarioDto->getSenha()){
            array_push($erros,[
                'message'=>'Senha é obrigatória!'
            ]);
        }
        if (!$usuarioDto->getTelefone()){
            array_push($erros,[
                'message'=>'Telefone é obrigatório!'
            ]);      
        }
        if (count($erros) > 0){
            return $this->json($erros,422);
        }
        //Valida se o cpf está cadastrado
        $usuarioExistente = $usuarioRepository->findByCpf($usuarioDto->getCpf());
        if ($usuarioExistente){
            return $this->json([
             'message'=> 'O CPF informado já está cadastrado!'   
            ], 409);
        }


        // converte o DTO em entidade usuário
        $usuario = new Usuario();
        $usuario->setCpf($usuarioDto->getCpf());
        $usuario->setNome($usuarioDto->getNome());
        $usuario->setEmail($usuarioDto->getEmail());
        $usuario->setSenha($usuarioDto->getSenha());
        $usuario->setTelefone($usuarioDto->getTelefone());

        // Criar registro no tb usuário
        $entityManager->persist($usuario);
        $entityManager->flush();

        // Criar registro na tb conta
        // Retornar os dados de usuario e conta
        
        return $this->json($usuario);
    }
}
