# Biblioteca V2 📚

Sistema simples de biblioteca feito em PHP com MySQL, com foco em:
- boas práticas
- estrutura organizada

## Funcionalidades atuais
- Cadastro de livros
- Listagem de livros
- Edição de livros
- Exclusão de livros
- Mensagem de sucesso após cadastro

## Estrutura do projeto
bibliotecaV2/
├── config/
│ └── database.php
├── public/
│ ├── cadastrar_livro.php
│ ├── salvar_livro.php
│ ├── listar_livros.php
│ ├── editar_livro.php
│ └── atualizar_livro.php
└── src/

## Tecnologias utilizadas
- PHP (PDO)
- MySQL
- Laragon

## Como rodar o projeto
1. Clone o repositório
2. Crie o banco `biblioteca_v2`
3. Crie a tabela `livros`
4. Coloque o projeto na pasta `www` do Laragon
5. Acesse `http://localhost/bibliotecaV2/public/listar_livros.php`