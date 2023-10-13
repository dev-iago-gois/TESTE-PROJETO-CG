# PROJETO CG - STORE-API

O "PROJETO CG - STORE-API" tem como principal objetivo o desenvolvimento do back-end de uma aplicação projetada para gerenciar o estoque de produtos e facilitar as operações de vendas.

## Instrucoes

### Pre requisitos

Requirements for the software and other tools to build, test and push

-   DB: [mysql](https://www.mysql.com/)
-   BACKEND:
    -   Linguagem: [php](https://www.php.net/)
    -   Gereciador de pacotes: [composer](https://getcomposer.org/)
    -   Framework: [laravel](https://laravel.com/docs/10.x/installation)

### Rodando aplicacao

Com o DB MySQL rodando e alterando no arquivo .env, linha 14 DB_DATABASE=DB_TESTE_CG, alterar para o nome que quiser no seu DB.

Entrar na pasta principal da aplicacao e rodar no temrinal o comando:

    php artisan migrate && php artisan serve

logo apos aparecera uma mensagem indicando em que porta o projeto roda.
entao pode usar o seru client preferido(Insomnia, Postman, ThunderClient) para acessar as rotas da API.

### Endpoints:

    Produtos:
        GET     http://localhost:8000/products              (LISTA TODOS OS PRODUTOS)
        GET     http://localhost:8000/products/:id          (RETORNA PRODUTO PELO ID)
        POST    http://localhost:8000/products              (CADASTRA UM PRODUTO)
                    BODY_EXAMPLE_JSON = {
                        "name": "nome do produto",
                        "description": "descricao do produto",
                        "price": 1.99,
                        "stock": 100,
                    }
        PATCH   http://localhost:8000/products/:id          (ATUALIZA UM PRODUTO)
        DELETE  http://localhost:8000/products/:id          (DELETA UM PRODUTO)

    Vendas:
        GET     http://localhost:8000/sales                 (HISTORICO DE VENDAS)
        POST    http://localhost:8000/sales                 (CRIA UMA VENDA)
                    BODY_EXAMPLE_JSON = {
                        "product_id": 1,
                        "quantity": 5,
                    }
        PATCH   http://localhost:8000/sales/:id             (ATUALIZA UMA VENDA)
                    BODY_EXAMPLE_JSON = {
                        "product_id": 1,
                        "quantity": 5,
                    }
        PATCH   http://localhost:8000/sales/:id/cancel      (CANCELA UMA VENDA)
                     BODY_EXAMPLE_JSON = {
                        "status": "canceled"
                    }

## Descricao do projeto

<details>
<summary>Clique para exibir os Requisitos do Projeto</summary>

### REQUISITO 1

Criar um endpoint que permita o cadastro de um novo produto com os campos: name, description, price e stock. Nome, descircao, preco e quantidade em estoque, respectivamente.

### REQUISITO 2

Implementar um endpoint para listar todos os produtos disponíveis no estoque.

### REQUISITO 3

Desenvolver um endpoint para obter os detalhes de um produto através do ID.

### REQUISITO 4

Criar um endpoint para atualizar as informações de um produto através do ID.

### REQUISITO 5

Implementar um endpoint para excluir um produto através do ID.

### REQUISITO 6

Garantir que a quantidade em estoque seja atualizada automaticamente quando novas unidades são vendidas.

### REQUISITO 7

Adicionar um novo endpoint que permita realizar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve reduzir a quantidade de estoque do produto.

### REQUISITO 8

Adicionar um novo endpoint que permita cancelar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve aumentar a quantidade de estoque do produto.

### REQUISITO 9

Adicionar um novo endpoint que permita editar uma venda, onde o produto é selecionado e a quantidade vendida é registrada. Isso deve reduzir ou aumentar a quantidade em estoque do produto dependendo da edição.

### REQUISITO 10

Criar um endpoint para listar o histórico de vendas, incluindo informações sobre os produtos vendidos, quantidades, data da venda.

</details>

## Autor

-   **Iago Gois** - _Web Developer_
    [dev-iago-gois](https://github.com/dev-iago-gois)

## License

This project is licensed under the [CC0 1.0 Universal](LICENSE.md)
Creative Commons License - see the [LICENSE.md](LICENSE.md) file for
details
